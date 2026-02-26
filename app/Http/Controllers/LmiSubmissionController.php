<?php

namespace App\Http\Controllers;

use App\Models\LmiSubmission;
use App\Models\LmiHardToFillRole;
use App\Models\LmiDiagnosis;
use App\Models\LmiEngagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LmiSubmissionController extends Controller
{   
    
    // UPDATED: Now supports pending, approved, and rejected tabs
    public function adminIndex(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $status = 'pending';
        }
        
        $pendingCount  = LmiSubmission::where('status', 'pending')->count();
        $approvedCount = LmiSubmission::where('status', 'approved')->count();
        $rejectedCount = LmiSubmission::where('status', 'rejected')->count();
        
        $submissions = LmiSubmission::with(['hardToFillRoles', 'diagnosis', 'engagement'])
            ->where('status', $status)
            ->orderBy('created_at', 'asc')
            ->paginate(1)
            ->appends(['status' => $status]);
    
        return view('admin.LmiSubmissions', compact(
            'submissions', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount'
        ))->with('activeTab', $status);
    }

    // Live polling endpoint
    public function counts()
    {
        return response()->json([
            'pending'  => LmiSubmission::where('status', 'pending')->count(),
            'approved' => LmiSubmission::where('status', 'approved')->count(),
            'rejected' => LmiSubmission::where('status', 'rejected')->count(),
        ]);
    }
    
    public function updateEngagement(Request $request, $id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            $engagement = LmiEngagement::findOrFail($request->engagement_id);
            
            $lmiFeatures = $request->lmi_features ?? [];
            if (!is_array($lmiFeatures)) {
                $lmiFeatures = [$lmiFeatures];
            }

            if (in_array('Other', $lmiFeatures) && !empty($request->lmi_features_other)) {
                $lmiFeatures = array_diff($lmiFeatures, ['Other']);
                $lmiFeatures[] = 'Other: ' . trim($request->lmi_features_other);
                $lmiFeatures = array_values($lmiFeatures);
            }
            
            $engagement->update([
                'lmi_features'   => $lmiFeatures,
                'specific_inputs' => $request->specific_inputs,
            ]);
            
            return redirect()->back()->with('success', 'Engagement information updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Update engagement error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update engagement: ' . $e->getMessage());
        }
    }
    
    public function updateDiagnosis(Request $request, $id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            $diagnosis  = LmiDiagnosis::findOrFail($request->diagnosis_id);
            
            $diagnosis->update([
                'rejection_reasons'          => $request->rejection_reasons ?? [],
                'rejection_reasons_other'    => $request->rejection_reasons_other,
                'coordination_frequency'     => $request->coordination_frequency === 'Other' 
                    ? $request->coordination_frequency_other 
                    : $request->coordination_frequency,
                'coordination_frequency_other' => $request->coordination_frequency_other,
            ]);
            
            return redirect()->back()->with('success', 'Diagnosis information updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Update diagnosis error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update diagnosis: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            
            $validated = $request->validate([
                'company_name'    => 'required|string|max:255',
                'respondent_name' => 'required|string|max:255',
                'position'        => 'required|string|max:255',
                'contact_number'  => 'required|string|min:9|max:20',
                'contact_type'    => 'required|in:mobile,telephone', // ← ADDED
                'email'           => 'required|email|max:255',
                'industry_sector' => 'required|string',
                'company_size'    => 'required|string',
            ]);
            
            $submission->update($validated);
            
            return redirect()->back()->with('success', 'Submission updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Update submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update submission: ' . $e->getMessage());
        }
    }
    
    public function updateRoles(Request $request, $id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            
            foreach ($request->roles as $roleData) {
                $role = LmiHardToFillRole::findOrFail($roleData['id']);
                
                $techSkills = isset($roleData['technical_skills_missing']) 
                    ? array_map('trim', explode(',', $roleData['technical_skills_missing']))
                    : [];
                    
                $softSkills = isset($roleData['soft_skills_missing'])
                    ? array_map('trim', explode(',', $roleData['soft_skills_missing']))
                    : [];
                
                $salaryRange = $roleData['salary_range'] ?? null;

                // If "Below ₱30,000" was selected, use the exact amount field instead
                if ($salaryRange === 'Below ₱30,000') {
                    $exactAmount = isset($roleData['below_30k_salary'])
                        ? (int) preg_replace('/[^0-9]/', '', $roleData['below_30k_salary'])
                        : null;
                    $salaryRange = $exactAmount ?: null;
                } elseif (is_numeric(str_replace(',', '', $salaryRange ?? ''))) {
                    $salaryRange = (int) str_replace(',', '', $salaryRange);
                }
                
                $role->update([
                    'job_title'               => $roleData['job_title'],
                    'job_classification'      => $roleData['job_classification'],
                    'salary_range'            => $salaryRange,
                    'vacancy_duration'        => $roleData['vacancy_duration'],
                    'difficulty_reasons'      => $roleData['difficulty_reasons'] ?? [],
                    'technical_skills_missing' => array_filter($techSkills),
                    'soft_skills_missing'     => array_filter($softSkills),
                ]);
                
                if (isset($roleData['diagnosis_id']) && isset($roleData['impact_level'])) {
                    LmiDiagnosis::where('id', $roleData['diagnosis_id'])->update([
                        'impact_level' => $roleData['impact_level']
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'Hard-to-Fill Roles and Impact Levels updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Update roles error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update roles: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            $submission->update([
                'status'      => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);
            
            return redirect()->route('admin.lmi-submissions.index', ['status' => 'pending'])
                             ->with('success', 'Submission approved successfully!');
        } catch (\Exception $e) {
            Log::error('Approve submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve submission: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            $submission->update([
                'status'      => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);
            
            return redirect()->route('admin.lmi-submissions.index', ['status' => 'pending'])
                             ->with('success', 'Submission rejected successfully!');
        } catch (\Exception $e) {
            Log::error('Reject submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject submission: ' . $e->getMessage());
        }
    }

    public function restorePending($id)
    {
        try {
            $submission = LmiSubmission::findOrFail($id);
            $submission->update([
                'status'      => 'pending',
                'reviewed_at' => null,
                'reviewed_by' => null,
            ]);
            
            return redirect()->route('admin.lmi-submissions.index', ['status' => 'pending'])
                             ->with('success', 'Submission restored to pending status successfully!');
        } catch (\Exception $e) {
            Log::error('Restore pending error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore submission: ' . $e->getMessage());
        }
    }
    
    public function store(Request $request)
    {           
        try {
            Log::info('=== SUBMISSION ATTEMPT STARTED ===');
            Log::info('Request Method: ' . $request->method());
            Log::info('Request URL: ' . $request->fullUrl());
            Log::info('All Input Keys: ' . json_encode(array_keys($request->all())));
            
            $validated = $request->validate([
                // Part I: Company Profile
                'company'        => 'required|string|max:255',
                'respondent'     => 'required|string|max:255',
                'position'       => 'required|string|max:255',
                'contact_number' => 'required|string|min:9|max:20',
                'contact_type'   => 'required|in:mobile,telephone', // ← ADDED
                'email'          => 'required|email|max:255',
                'industrySelector' => 'required|string',
                'companySize'    => 'required|string',
                
                // Part II: Hard-to-Fill Roles
                'job_title'               => 'required|array|min:1',
                'job_title.*'             => 'required|string|max:255',
                'job_classification'      => 'required|array',
                'job_classification.*'    => 'required|string',
                'salary_range'            => 'required|array',
                'vacancy_duration'        => 'required|array',
                'vacancy_duration.*'      => 'required|string',
                'technical_skills_missing' => 'nullable|array',
                'soft_skills_missing'     => 'nullable|array',
                
                // Part III: Diagnosis
                'rejection_reasons'          => 'nullable|array',
                'rejection_reasons_other'    => 'nullable|string|max:500',
                'coordination_frequency'     => 'nullable|string',
                'coordination_frequency_other' => 'nullable|string|max:255',
                
                // Part IV: Engagement
                'lmi_features'   => 'nullable|array',
                'specific_inputs' => 'nullable|string',
            ]);
            
            // Validate impact levels
            foreach ($request->job_title as $index => $jobTitle) {
                $impactLevel = $request->input("impact_level_{$index}");
                
                if (empty($impactLevel)) {
                    $errorMessage = "Impact level is required for job entry #" . ($index + 1);
                    
                    if ($request->ajax() || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors'  => ["impact_level_{$index}" => [$errorMessage]]
                        ], 422);
                    }
                    
                    return back()->withErrors([
                        "impact_level_{$index}" => $errorMessage
                    ])->withInput();
                }
            }

            Log::info('Impact level validation passed');

            DB::beginTransaction();

            // Create main submission — now includes contact_type
            $submission = LmiSubmission::create([
                'company_name'    => $request->company,
                'respondent_name' => $request->respondent,
                'position'        => $request->position,
                'contact_number'  => $request->contact_number,
                'contact_type'    => $request->contact_type,  // ← ADDED
                'email'           => $request->email,
                'industry_sector' => $request->industrySelector,
                'company_size'    => $request->companySize,
                'status'          => 'pending',
                'submitted_at'    => now(),
            ]);

            Log::info('Main submission created: ' . $submission->id);

            // Save hard-to-fill roles
            foreach ($request->job_title as $index => $jobTitle) {
                $technicalSkillsArray = [];
                if (isset($request->technical_skills_missing[$index]) && !empty($request->technical_skills_missing[$index])) {
                    $technicalSkillsArray = array_filter(
                        array_map('trim', explode(',', $request->technical_skills_missing[$index]))
                    );
                }

                $softSkillsArray = [];
                if (isset($request->soft_skills_missing[$index]) && !empty($request->soft_skills_missing[$index])) {
                    $softSkillsArray = array_filter(
                        array_map('trim', explode(',', $request->soft_skills_missing[$index]))
                    );
                }

                $jobDifficultyReasons = $request->input("difficulty_reasons_{$index}", []);
                
                if (!is_array($jobDifficultyReasons)) {
                    $jobDifficultyReasons = [$jobDifficultyReasons];
                }

                $salaryRange = $request->salary_range[$index];

                // If "Below ₱30,000" was selected, use the exact amount field instead
                if ($salaryRange === 'Below ₱30,000') {
                    $rawBelow = $request->input("below_30k_salary.{$index}");
                    $exactAmount = $rawBelow ? (int) preg_replace('/[^0-9]/', '', $rawBelow) : null;
                    $salaryRange = $exactAmount ?: null;
                } elseif (is_numeric(str_replace(',', '', $salaryRange ?? ''))) {
                    $salaryRange = (int) str_replace(',', '', $salaryRange);
                }

                $hardToFillRole = LmiHardToFillRole::create([
                    'lmi_submission_id'        => $submission->id,
                    'job_title'                => $jobTitle,
                    'job_classification'       => $request->job_classification[$index],
                    'salary_range'             => $salaryRange,
                    'vacancy_duration'         => $request->vacancy_duration[$index],
                    'difficulty_reasons'       => $jobDifficultyReasons,
                    'technical_skills_missing' => $technicalSkillsArray,
                    'soft_skills_missing'      => $softSkillsArray,
                ]);

                $impactLevel = $request->input("impact_level_{$index}");

                $diagnosisData = [
                    'lmi_submission_id'       => $submission->id,
                    'lmi_hard_to_fill_role_id' => $hardToFillRole->id,
                    'impact_level'            => $impactLevel,
                ];

                if ($index === 0) {
                    $rejectionReasons = $request->rejection_reasons ?? [];
                    if (!is_array($rejectionReasons)) {
                        $rejectionReasons = [$rejectionReasons];
                    }
                    $diagnosisData['rejection_reasons']          = $rejectionReasons;
                    $diagnosisData['rejection_reasons_other']    = $request->rejection_reasons_other;
                    $diagnosisData['coordination_frequency']     = $request->coordination_frequency;
                    $diagnosisData['coordination_frequency_other'] = $request->coordination_frequency_other;
                }

                LmiDiagnosis::create($diagnosisData);
            }

            // Save engagement
            $lmiFeatures = $request->lmi_features ?? [];
            if (!is_array($lmiFeatures)) {
                $lmiFeatures = [$lmiFeatures];
            }
            if (in_array('Other', $lmiFeatures) && !empty($request->lmi_features_other)) {
                $lmiFeatures = array_diff($lmiFeatures, ['Other']);
                $lmiFeatures[] = 'Other: ' . trim($request->lmi_features_other);
                $lmiFeatures = array_values($lmiFeatures);
            }
            
            LmiEngagement::create([
                'lmi_submission_id' => $submission->id,
                'lmi_features'      => $lmiFeatures,
                'specific_inputs'   => $request->specific_inputs,
            ]);

            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your submission (ID: ' . $submission->id . ') has been received and is pending admin confirmation.'
                ]);
            }

            return redirect()->back()->with('success', 'Thank you! Your submission (ID: ' . $submission->id . ') has been received and is pending admin confirmation.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors'  => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submission error: ' . $e->getMessage());
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}