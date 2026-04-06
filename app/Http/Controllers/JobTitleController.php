<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobTitle;

class JobTitleController extends Controller
{
    /**
     * Show the job titles form
     */
    public function showForm()
    {
        return view('admin.jobTitleForm');
    }

    /**
     * Store job titles data
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'jobs' => 'required|array|min:1',
            'jobs.*.title' => 'required|string|max:255',
            'jobs.*.count' => 'required|integer|min:0',
        ]);

        try {
            // Delete existing pending job titles for this year by this user
            JobTitle::where('year', $validated['year'])
                ->where('submitted_by', auth()->id())
                ->where('status', 'pending')
                ->delete();

            // Insert new job titles as pending
            foreach ($validated['jobs'] as $job) {
                JobTitle::create([
                    'year' => $validated['year'],
                    'title' => $job['title'],
                    'count' => $job['count'],
                    'status' => 'pending',
                    'submitted_by' => auth()->id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job titles submitted successfully and pending statistician approval!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving job titles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Live polling endpoint — returns current pending job title count
     */
    public function pendingCount()
    {
        return response()->json([
            'pending' => JobTitle::where('status', 'pending')->distinct('year')->count('year'),
        ]);
    }

    /**
     * Show pending submissions for statistician
     */
    public function pendingSubmissions()
    {
        $submissions = JobTitle::where('status', 'pending')
            ->whereNotNull('year')
            ->with('submitter')
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('year');

        return view('statistician.JobTittlePending', compact('submissions'));
    }

    /**
     * Approve job titles
     */
    public function approve(Request $request, $year)
    {
        try {
            $edits = $request->input('edits', []);

            // Apply any edits the statistician made before approving
            foreach ($edits as $jobId => $changes) {
                $job = JobTitle::find($jobId);
                if (!$job) continue;

                if (isset($changes['title'])) {
                    $job->title = $changes['title'];
                }
                if (isset($changes['count'])) {
                    $job->count = $changes['count'];
                }
                $job->save();
            }

            // Update all pending job titles for this year to approved
            $query = JobTitle::where('status', 'pending');

            if (is_null($year) || in_array($year, ['null', 'undefined', ''])) {
                $query->whereNull('year');
            } else {
                $query->where('year', (int) $year);
            }

            $query->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job titles approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving job titles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject job titles
     * Reason field removed — rejection no longer requires a comment
     */
    public function reject(Request $request, $year)
    {
        try {
            $query = JobTitle::where('status', 'pending');

            if (is_null($year) || in_array($year, ['null', 'undefined', ''])) {
                $query->whereNull('year');
            } else {
                $query->where('year', (int) $year);
            }

            $query->update([
                'status'      => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job titles rejected successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting job titles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job titles for a specific year (only approved)
     */
    public function getByYear($year)
    {
        $jobTitles = JobTitle::where('year', $year)
            ->where('status', 'approved')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json($jobTitles);
    }

    /**
     * Return pending / approved / rejected records for the admin history panel.
     * Query params: status (pending|approved|rejected), year (optional)
     */
    public function history(Request $request)
    {
        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'])
            ? $request->query('status')
            : 'pending';

        $year = $request->query('year');

        $query = JobTitle::where('status', $status)
            ->orderBy('year', 'desc')
            ->orderBy($status === 'pending' ? 'created_at' : 'reviewed_at', 'desc');

        if ($year) {
            $query->where('year', (int) $year);
        }

        $records = $query->get(['id', 'year', 'title', 'count', 'status', 'reviewed_at', 'created_at']);

        // Badge counts — count distinct year submissions per status, not individual rows
        $countQuery = JobTitle::whereIn('status', ['pending', 'approved', 'rejected']);
        if ($year) {
            $countQuery->where('year', (int) $year);
        }
        $counts = $countQuery->selectRaw("status, count(DISTINCT year) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        // Distinct years available for the current status (for the year filter dropdown)
        $availableYears = JobTitle::where('status', $status)
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'records' => $records,
            'counts'  => [
                'pending'  => $counts['pending']  ?? 0,
                'approved' => $counts['approved'] ?? 0,
                'rejected' => $counts['rejected'] ?? 0,
            ],
            'years' => $availableYears,
        ]);
    }

    /**
     * Check if a year already has a pending submission
     */
    public function checkYear(Request $request)
    {
        $year = $request->query('year');

        $exists = JobTitle::where('year', (int) $year)
            ->where('status', 'pending')
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}