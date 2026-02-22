<?php

namespace App\Http\Controllers;

use App\Models\AnalysisTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalysisTemplateController extends Controller
{
    /** The 4 quarter-start months */
    protected const QUARTERS = [1 => 'January', 4 => 'April', 7 => 'July', 10 => 'October'];

    public function editor()
    {
        return view('statistician.template_editor');
    }

    // ─── INDEX (load templates for a given year + month) ─────────────────────

    public function index(Request $request)
    {
        try {
            // 1. Available years from stats table
            $availableYears = DB::table('regional_labor_market_statistics')
                ->distinct()
                ->pluck('year')
                ->map(fn($y) => (int)$y)
                ->sortDesc()
                ->values()
                ->toArray();

            if (empty($availableYears)) {
                $availableYears = [(int)date('Y')];
            }

            // 2. Available months for the chosen year (only months that have stats data)
            $year = (int)($request->query('year', $availableYears[0]));

            $availableMonths = DB::table('regional_labor_market_statistics')
                ->where('year', $year)
                ->distinct()
                ->pluck('month')
                ->map(fn($m) => (int)$m)
                ->sort()
                ->values()
                ->toArray();

            // Fallback: if no months found, show the 4 standard quarters
            if (empty($availableMonths)) {
                $availableMonths = [1, 4, 7, 10];
            }

            // 3. Pick the month
            $month = (int)($request->query('month', $availableMonths[0]));

            // 4. Fetch templates for year + month
            $fetched = AnalysisTemplate::where('year', $year)
                ->where('month', $month)
                ->get()
                ->keyBy('template_key');

            // 5. Build response — fill in defaults for any missing keys
            $templates = [];
            foreach (['employment', 'underemployment', 'unemployment', 'lfpr'] as $key) {
                if ($fetched->has($key)) {
                    $t = $fetched->get($key);
                    $templates[$key] = [
                        'id'            => $t->id,
                        'template_key'  => $t->template_key,
                        'template_text' => $t->template_text,
                        'year'          => $t->year,
                        'month'         => $t->month,
                        'is_active'     => $t->is_active,
                    ];
                } else {
                    $templates[$key] = [
                        'id'            => null,
                        'template_key'  => $key,
                        'template_text' => $this->getDefaultTemplate($key),
                        'year'          => $year,
                        'month'         => $month,
                        'is_active'     => false,
                    ];
                }
            }

            return response()->json([
                'success'         => true,
                'data'            => $templates,
                'years'           => $availableYears,
                'selected_year'   => $year,
                'months'          => $availableMonths,   // e.g. [1, 4, 7, 10]
                'selected_month'  => $month,
                'quarter_labels'  => self::QUARTERS,     // {1:"January", 4:"April", ...}
            ]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@index', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── NEW: PREVIEW DATA (fetch actual stats for preview) ──────────────────

    public function previewData(Request $request)
    {
        try {
            $year = (int)$request->query('year');
            $month = (int)$request->query('month');

            // Get previous period
            $previous = $this->getPreviousPeriod($month, $year);

            // Fetch current period stats
            $current = DB::table('regional_labor_market_statistics')
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            // Fetch previous period stats
            $prev = DB::table('regional_labor_market_statistics')
                ->where('year', $previous['year'])
                ->where('month', $previous['month'])
                ->first();

            if (!$current) {
                return response()->json([
                    'success' => false,
                    'error' => 'No data found for the selected period'
                ], 404);
            }

            // Format period labels
            $currentPeriod = self::QUARTERS[$month] . ' ' . $year;
            $previousPeriod = self::QUARTERS[$previous['month']] . ' ' . $previous['year'];

            // Build preview data for each metric
            $previewData = [];

            // Employment
            if ($current && $prev) {
                $previewData['employment'] = $this->buildMetricData(
                    'employment_rate',
                    $current,
                    $prev,
                    $currentPeriod,
                    $previousPeriod
                );

                $previewData['underemployment'] = $this->buildMetricData(
                    'underemployment_rate',
                    $current,
                    $prev,
                    $currentPeriod,
                    $previousPeriod
                );

                $previewData['unemployment'] = $this->buildMetricData(
                    'unemployment_rate',
                    $current,
                    $prev,
                    $currentPeriod,
                    $previousPeriod
                );

                $previewData['lfpr'] = $this->buildMetricData(
                    'labor_force_participation_rate',
                    $current,
                    $prev,
                    $currentPeriod,
                    $previousPeriod
                );
            }

            return response()->json([
                'success' => true,
                'data' => $previewData,
                'has_data' => !empty($previewData)
            ]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@previewData', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── HELPER: Build metric data ────────────────────────────────────────────

    private function buildMetricData($field, $current, $prev, $currentPeriod, $previousPeriod)
    {
        $currentRate = $current->{$field} ?? 0;
        $prevRate = $prev->{$field} ?? 0;

        $diff = $currentRate - $prevRate;
        
        // Determine trend
        if (abs($diff) < 0.01) {
            $trend = 'remained the same';
            $trendClass = 'text-slate-600';
            $trendIcon = '→';
        } elseif ($diff > 0) {
            $trend = 'higher';
            $trendClass = 'text-green-600';
            $trendIcon = '↑';
        } else {
            $trend = 'lower';
            $trendClass = 'text-red-600';
            $trendIcon = '↓';
        }

        return [
            '{current_period}' => "<strong>{$currentPeriod}</strong>",
            '{previous_period}' => "<strong>{$previousPeriod}</strong>",
            '{current_rate}' => "<strong>" . number_format($currentRate, 1) . "%</strong>",
            '{previous_rate}' => "<strong>" . number_format($prevRate, 1) . "%</strong>",
            '{trend}' => "<span class=\"{$trendClass} font-semibold\">{$trend} {$trendIcon}</span>"
        ];
    }

    // ─── HELPER: Get previous period ──────────────────────────────────────────

    private function getPreviousPeriod($month, $year)
    {
        // For quarterly data: compare to previous quarter
        // For annual data (January): compare to same month previous year
        
        if ($month == 1) {
            // January compares to January of previous year
            return ['month' => 1, 'year' => $year - 1];
        }
        
        $map = [
            4  => ['month' => 1,  'year' => $year],  // April → January same year
            7  => ['month' => 4,  'year' => $year],  // July → April same year
            10 => ['month' => 7,  'year' => $year]   // October → July same year
        ];

        return $map[$month] ?? ['month' => 1, 'year' => $year - 1];
    }

    // ─── UPDATE (save one template) ───────────────────────────────────────────

    public function update(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'template_text' => 'required|string|max:5000',
            'year'          => 'required|integer',
            'month'         => 'required|integer|in:1,4,7,10',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $template = AnalysisTemplate::updateOrCreate(
                [
                    'template_key' => $key,
                    'year'         => (int)$request->year,
                    'month'        => (int)$request->month,
                ],
                [
                    'template_text' => $request->template_text,
                    'updated_by'   => auth()->id() ?? null,
                    'is_active'    => true,
                ]
            );

            return response()->json(['success' => true, 'data' => $template]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@update', ['key' => $key, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── RESET (return default text, no DB write) ────────────────────────────

    public function reset($key)
    {
        return response()->json([
            'success'      => true,
            'default_text' => $this->getDefaultTemplate($key),
        ]);
    }

    // ─── DEFAULTS ─────────────────────────────────────────────────────────────

    private function getDefaultTemplate($key): string
    {
        return match($key) {
            'employment'      => 'The employment rate in {current_period} was estimated at {current_rate}. This was {trend} than the recorded rate in {previous_period} of {previous_rate}.',
            'underemployment' => 'The underemployment rate in {current_period} {trend} to {current_rate}, from {previous_rate} in {previous_period}.',
            'unemployment'    => 'The unemployment rate {trend} to {current_rate} in {current_period}, from its rate in {previous_period} of {previous_rate}.',
            'lfpr'            => 'The country\'s labor force participation rate (LFPR) in {current_period} was recorded at {current_rate}, {trend} than the estimated LFPR in {previous_period} at {previous_rate}.',
            default           => '',
        };
    }
}