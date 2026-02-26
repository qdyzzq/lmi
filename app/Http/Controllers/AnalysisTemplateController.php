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

    public function adminEditor()
    {
        return view('admin.template_editor');
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
                'months'          => $availableMonths,
                'selected_month'  => $month,
                'quarter_labels'  => self::QUARTERS,
            ]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@index', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── PREVIEW DATA ─────────────────────────────────────────────────────────

    public function previewData(Request $request)
    {
        try {
            $year  = (int)$request->query('year');
            $month = (int)$request->query('month');

            $previous = $this->getPreviousPeriod($month, $year);

            $current = DB::table('regional_labor_market_statistics')
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            $prev = DB::table('regional_labor_market_statistics')
                ->where('year', $previous['year'])
                ->where('month', $previous['month'])
                ->first();

            if (!$current) {
                return response()->json(['success' => false, 'error' => 'No data found for the selected period'], 404);
            }

            $currentPeriod  = self::QUARTERS[$month] . ' ' . $year;
            $previousPeriod = self::QUARTERS[$previous['month']] . ' ' . $previous['year'];

            $previewData = [];

            if ($current && $prev) {
                $previewData['employment']      = $this->buildMetricData('employment_rate', $current, $prev, $currentPeriod, $previousPeriod);
                $previewData['underemployment'] = $this->buildMetricData('underemployment_rate', $current, $prev, $currentPeriod, $previousPeriod);
                $previewData['unemployment']    = $this->buildMetricData('unemployment_rate', $current, $prev, $currentPeriod, $previousPeriod);
                $previewData['lfpr']            = $this->buildMetricData('labor_force_participation_rate', $current, $prev, $currentPeriod, $previousPeriod);
            }

            return response()->json(['success' => true, 'data' => $previewData, 'has_data' => !empty($previewData)]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@previewData', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── HELPER: Build metric data ────────────────────────────────────────────

    private function buildMetricData($field, $current, $prev, $currentPeriod, $previousPeriod)
    {
        $currentRate = $current->{$field} ?? 0;
        $prevRate    = $prev->{$field} ?? 0;
        $diff        = $currentRate - $prevRate;

        if (abs($diff) < 0.01) {
            $trend = 'remained the same'; $trendClass = 'text-slate-600'; $trendIcon = '→';
        } elseif ($diff > 0) {
            $trend = 'higher'; $trendClass = 'text-green-600'; $trendIcon = '↑';
        } else {
            $trend = 'lower'; $trendClass = 'text-red-600'; $trendIcon = '↓';
        }

        return [
            '{current_period}'  => "<strong>{$currentPeriod}</strong>",
            '{previous_period}' => "<strong>{$previousPeriod}</strong>",
            '{current_rate}'    => "<strong>" . number_format($currentRate, 1) . "%</strong>",
            '{previous_rate}'   => "<strong>" . number_format($prevRate, 1) . "%</strong>",
            '{trend}'           => "<span class=\"{$trendClass} font-semibold\">{$trend} {$trendIcon}</span>",
        ];
    }

    // ─── HELPER: Get previous period ──────────────────────────────────────────

    private function getPreviousPeriod($month, $year)
    {
        if ($month == 1) return ['month' => 1, 'year' => $year - 1];

        $map = [
            4  => ['month' => 1, 'year' => $year],
            7  => ['month' => 4, 'year' => $year],
            10 => ['month' => 7, 'year' => $year],
        ];

        return $map[$month] ?? ['month' => 1, 'year' => $year - 1];
    }

    // ─── UPDATE (statistician saves as published) ─────────────────────────────

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
            // Remove any pending draft for this key + year + month
            AnalysisTemplate::where('template_key', $key)
                ->where('year', (int)$request->year)
                ->where('month', (int)$request->month)
                ->where('status', 'pending')
                ->delete();

            $template = AnalysisTemplate::updateOrCreate(
                [
                    'template_key' => $key,
                    'year'         => (int)$request->year,
                    'month'        => (int)$request->month,
                    'status'       => 'published',
                ],
                [
                    'template_text' => $request->template_text,
                    'updated_by'    => auth()->id() ?? null,
                    'is_active'     => true,
                    'status'        => 'published',
                    'submitted_by'  => null,
                    'submitted_at'  => null,
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

    // ─── ADMIN: Submit a draft (saved as 'pending') ───────────────────────────

    public function adminSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year'                      => 'required|integer',
            'month'                     => 'required|integer|in:1,4,7,10',
            'templates'                 => 'required|array',
            'templates.employment'      => 'sometimes|string|max:5000',
            'templates.underemployment' => 'sometimes|string|max:5000',
            'templates.unemployment'    => 'sometimes|string|max:5000',
            'templates.lfpr'            => 'sometimes|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $year  = (int)$request->year;
            $month = (int)$request->month;
            $saved = [];

            foreach ($request->templates as $key => $text) {
                if (!in_array($key, ['employment', 'underemployment', 'unemployment', 'lfpr'])) continue;

                // updateOrCreate handles the unique constraint on (template_key, year, month)
                // Overwrites whatever status is there (null for old records, or pending)
                $saved[] = AnalysisTemplate::updateOrCreate(
                    [
                        'template_key' => $key,
                        'year'         => $year,
                        'month'        => $month,
                    ],
                    [
                        'template_text' => $text,
                        'is_active'     => false,
                        'status'        => 'pending',
                        'submitted_by'  => auth()->user()?->name ?? auth()->id() ?? 'Admin',
                        'submitted_at'  => now(),
                        'updated_by'    => auth()->id() ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Templates submitted for review successfully.',
                'data'    => $saved,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AnalysisTemplateController@adminSubmit', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── STATISTICIAN: Get all pending drafts ────────────────────────────────

    public function allPending()
    {
        try {
            $pending = AnalysisTemplate::where('status', 'pending')
                ->orderByDesc('submitted_at')
                ->get();

            $grouped = $pending->groupBy(fn($t) => $t->year . '-' . $t->month);

            $data = $grouped->map(function ($items) {
                $first = $items->first();
                return [
                    'id'            => $first->id,
                    'year'          => $first->year,
                    'month'         => $first->month,
                    'submitted_by'  => $first->submitted_by,
                    'submitted_at'  => $first->submitted_at?->toDateTimeString(),
                    'template_keys' => $items->pluck('template_key')->toArray(),
                    'templates'     => $items->pluck('template_text', 'template_key')->toArray(),
                ];
            })->values();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('AnalysisTemplateController@allPending', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── STATISTICIAN: Pending count for badge ───────────────────────────────

    public function pendingCount()
    {
        $count = AnalysisTemplate::where('status', 'pending')
            ->selectRaw('COUNT(DISTINCT CONCAT(year, "-", month)) as count')
            ->value('count') ?? 0;

        return response()->json(['success' => true, 'count' => (int)$count]);
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