<?php

namespace App\Http\Controllers\Sprs\Composers;

use App\Models\User;
use App\Services\Sprs\SubmissionScope;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * Supplies the sidebar and topbar to every view that extends the SPRS
 * layout, so no controller has to remember to pass them.
 *
 * The layout reads $sidebarPartial, which stays as-is. What's new is that
 * it's resolved from the user's role rather than set by hand.
 */
class SidebarComposer
{
    public function __construct(private SubmissionScope $scope)
    {
    }

    public function compose(View $view): void
    {
        $user = auth()->user();

        // The login and OTP screens use the same layout.
        if (! $user) {
            $view->with([
                'sidebarPartial' => null,
                'topbarPartial'  => null,
                'navGroups'      => [],
                'navUser'        => null,
                'navPeriod'      => null,
            ]);

            return;
        }

        $level = $user->office?->officeType?->hierarchy_level;

        $view->with([
            'sidebarPartial' => $this->partialFor('sidebar', $user, $level),
            // The existing topbar reads Auth::user() directly and is the
            // same for every role, so there's nothing to resolve. Kept
            // here only so the layout can keep using $topbarPartial.
            'topbarPartial'  => $this->partialFor('topbar', $user, $level),
            'navGroups'      => $this->groupsFor($user, $level),
            'navUser'        => [
                'name'   => $user->name,
                'office' => $user->office?->name,
                'role'   => $user->office?->officeType?->name ?? $user->role,
                'avatar' => $user->avatar,
            ],

            // The reporting period, on every page rather than only the
            // ones that happen to show it. Someone three tabs deep in
            // DOLE Programs on the 20th shouldn't have to navigate away
            // to find out the deadline is tomorrow.
            'navPeriod'      => $this->periodFor($user),
        ]);
    }

    /**
     * The current reporting month and how long this user has left.
     *
     * Null for anyone outside the hierarchy — super admin and
     * statistician have no deadline, so a countdown would be noise.
     *
     * @return array{label: string, deadline: ?string, daysLeft: ?int}|null
     */
    private function periodFor(User $user): ?array
    {
        if ($user->office_id === null) {
            return null;
        }

        $deadline = $this->scope->deadlineDate($user);

        return [
            'label'    => now()->format('F Y'),
            'deadline' => $deadline?->format('j F'),
            'daysLeft' => $deadline
                ? (int) now()->startOfDay()->diffInDays($deadline->copy()->startOfDay(), absolute: false)
                : null,
        ];
    }

    /**
     * A role-specific partial if one exists, otherwise the shared one.
     *
     * So `jpo_sidebar.blade.php` is picked up automatically for level 1,
     * and everyone else falls through to `sidebar.blade.php`. Add
     * `fo_sidebar.blade.php` and FO starts using it with no code change —
     * but only add one if that role genuinely needs a different layout,
     * not just different menu items. Different items are what navGroups
     * is for.
     */
    private function partialFor(string $kind, User $user, ?int $level): string
    {
        $prefix = match (true) {
            $user->role === 'super_admin' => 'superadmin',
            $level === 1                  => 'jpo',
            $level === 2                  => 'peso',
            $level === 3                  => 'province',
            $level === 4                  => 'fo',
            $level === 5                  => 'ro',
            default                       => null,
        };

        if ($prefix && view()->exists("sprs.partials.{$prefix}_{$kind}")) {
            return "sprs.partials.{$prefix}_{$kind}";
        }

        return "sprs.partials.{$kind}";
    }

    /**
     * config/sprs_nav.php, filtered to this user.
     *
     * Items pointing at a route that doesn't exist yet are dropped rather
     * than throwing, so the config can be written ahead of the pages.
     *
     * @return array<int, array{label: string, items: array}>
     */
    private function groupsFor(User $user, ?int $level): array
    {
        $groups = [];

        foreach (config('sprs_nav.groups', []) as $group) {
            $items = [];

            foreach ($group['items'] ?? [] as $item) {
                if (! $this->visibleTo($item, $user, $level)) {
                    continue;
                }

                if (! Route::has($item['route'])) {
                    continue;
                }

                $items[] = [
                    'label'  => $item['label'],
                    'icon'   => $item['icon'] ?? null,
                    'url'    => route($item['route']),
                    'active' => request()->routeIs($item['route']),
                ];
            }

            if ($items !== []) {
                $groups[] = ['label' => $group['label'] ?? null, 'items' => $items];
            }
        }

        return $groups;
    }

    private function visibleTo(array $item, User $user, ?int $level): bool
    {
        if (isset($item['roles']) && in_array($user->role, $item['roles'], true)) {
            return true;
        }

        if (isset($item['levels']) && $level !== null) {
            return in_array($level, $item['levels'], true);
        }

        return false;
    }
}