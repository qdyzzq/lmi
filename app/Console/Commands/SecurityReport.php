<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Summarises the security log (storage/logs/security-*.log) for monitoring.
 *
 *   php artisan security:report              last 24 hours, printed
 *   php artisan security:report --hours=168  last 7 days
 *   php artisan security:report --mail       also email it to SECURITY_ALERT_EMAIL
 */
class SecurityReport extends Command
{
    protected $signature = 'security:report {--hours=24 : How many hours back to include} {--mail : Email the report to SECURITY_ALERT_EMAIL}';

    protected $description = 'Summarise security events (failed logins, lockouts, OTP failures, rate limits, admin changes)';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $since = now()->subHours($hours);

        $events = collect();
        foreach (glob(storage_path('logs/security-*.log')) ?: [] as $file) {
            // Daily files are named security-YYYY-MM-DD.log; skip ones older than the window.
            if (preg_match('/security-(\d{4}-\d{2}-\d{2})\.log$/', $file, $m) && Carbon::parse($m[1])->endOfDay()->lt($since)) {
                continue;
            }

            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $entry = json_decode($line, true);
                if (! is_array($entry) || empty($entry['datetime'])) {
                    continue;
                }
                if (Carbon::parse($entry['datetime'])->lt($since)) {
                    continue;
                }
                $events->push($entry);
            }
        }

        $lines   = [];
        $lines[] = 'Security report for ' . config('app.name') . ' — last ' . $hours . ' hour(s), ' . $events->count() . ' event(s)';
        $lines[] = str_repeat('-', 70);

        $byEvent = $events->groupBy(fn ($e) => $e['message'] ?? 'unknown')->map->count()->sortDesc();
        foreach ($byEvent as $event => $count) {
            $lines[] = str_pad((string) $event, 45) . $count;
        }

        $high = $events->filter(fn ($e) => in_array($e['level_name'] ?? '', ['ALERT', 'CRITICAL', 'EMERGENCY'], true));
        if ($high->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'High-severity events:';
            foreach ($high as $e) {
                $ctx = $e['context'] ?? [];
                $lines[] = sprintf('  %s  %s  ip=%s  %s', $e['datetime'], $e['message'], $ctx['ip'] ?? '-', $ctx['email'] ?? ($ctx['target_user_id'] ?? ''));
            }
        }

        $topIps = $events
            ->filter(fn ($e) => in_array($e['message'] ?? '', ['auth.login_failed', 'auth.otp_failed', 'request.rate_limited', 'request.csrf_mismatch', 'access.forbidden'], true))
            ->groupBy(fn ($e) => $e['context']['ip'] ?? '-')->map->count()->sortDesc()->take(10);
        if ($topIps->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Top IPs by failed/blocked requests:';
            foreach ($topIps as $ip => $count) {
                $lines[] = '  ' . str_pad((string) $ip, 41) . $count;
            }
        }

        $report = implode("\n", $lines);
        $this->line($report);

        if ($this->option('mail')) {
            $recipients = config('security.alert_emails', []);
            if (empty($recipients)) {
                $this->warn('SECURITY_ALERT_EMAIL is not set; report not emailed.');
            } else {
                Mail::raw($report, fn ($m) => $m->to($recipients)->subject('[Security Report] ' . config('app.name')));
                $this->info('Report emailed to ' . implode(', ', $recipients));
            }
        }

        return self::SUCCESS;
    }
}
