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
            'pending' => JobTitle::where('status', 'pending')->count(),
        ]);
    }

    /**
     * Show pending submissions for statistician
     * FIX: exclude null-year records to prevent groupBy keying on empty string
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
     * FIX: removed `int` type hint, added null-safe year handling
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
     * FIX: added null-safe year handling to prevent SQL "= null" error
     */
    public function reject(Request $request, $year)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $query = JobTitle::where('status', 'pending');

            if (is_null($year) || in_array($year, ['null', 'undefined', ''])) {
                $query->whereNull('year');
            } else {
                $query->where('year', (int) $year);
            }

            $query->update([
                'status'           => 'rejected',
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $validated['reason'],
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

    public function checkYear($year)
    {
        $data = \App\Models\JobTitle::where('year', $year)->get();

        if ($data->isEmpty()) {
            return response()->json(['exists' => false, 'data' => null], 404);
        }

        return response()->json([
            'exists' => true,
            'data' => [
                'year' => $year,
                'jobs' => $data->map(function($job) {
                    return [
                        'id'    => $job->id,
                        'title' => $job->job_title,
                        'count' => $job->job_count
                    ];
                })
            ]
        ], 200);
    }
}