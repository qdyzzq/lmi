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
     * Show pending submissions for statistician
     */
    public function pendingSubmissions()
    {
        $submissions = JobTitle::where('status', 'pending')
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
    public function approve(Request $request, int $year)
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
            JobTitle::where('year', $year)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
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
     */
    public function reject(Request $request, $year)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            JobTitle::where('year', $year)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
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
}