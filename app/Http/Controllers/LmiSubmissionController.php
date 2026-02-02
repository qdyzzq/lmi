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
    
    // FIXED: Now only shows PENDING submissions
    public function adminIndex()
    {
        $submissions = LmiSubmission::with(['hardToFillRoles', 'diagnosis', 'engagement'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(1);
    
        return view('admin.LmiSubmissions', compact('submissions'));
    }   
    public function updateEngagement(Request $request, $id)
{
    try {
        $submission = LmiSubmission::findOrFail($id);
        $engagement = LmiEngagement::findOrFail($request->engagement_id);
        
        // Ensure lmi_features is an array
        $lmiFeatures = $request->lmi_features ?? [];
        if (!is_array($lmiFeatures)) {
            $lmiFeatures = [$lmiFeatures];
        }
        
        $engagement->update([
            'lmi_features' => $lmiFeatures,
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
            $diagnosis = LmiDiagnosis::findOrFail($request->diagnosis_id);
            
            $diagnosis->update([
                'rejection_reasons' => $request->rejection_reasons ?? [],
                'rejection_reasons_other' => $request->rejection_reasons_other,
                'coordination_frequency' => $request->coordination_frequency === 'Other' 
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
                'company_name' => 'required|string|max:255',
                'respondent_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'industry_sector' => 'required|string',
                'company_size' => 'required|string',
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
                
                $role->update([
                    'job_title' => $roleData['job_title'],
                    'job_classification' => $roleData['job_classification'],
                    'vacancy_duration' => $roleData['vacancy_duration'],
                    'difficulty_reasons' => $roleData['difficulty_reasons'] ?? [],
                    'technical_skills_missing' => array_filter($techSkills),
                    'soft_skills_missing' => array_filter($softSkills),
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
        $submission = LmiSubmission::findOrFail($id);
        $submission->update(['status' => 'approved']);
        
        return redirect()->route('admin.lmi-submissions.index')
                       ->with('success', 'Submission approved successfully!');
    }

    public function reject($id)
    {
        $submission = LmiSubmission::findOrFail($id);
        $submission->update(['status' => 'rejected']);
        
        return redirect()->route('admin.lmi-submissions.index')
                       ->with('success', 'Submission rejected successfully!');
    }
    
    public function store(Request $request)
    {           
        try {
            Log::info('=== SUBMISSION ATTEMPT STARTED ===');
            Log::info('Request Method: ' . $request->method());
            Log::info('Request URL: ' . $request->fullUrl());
            Log::info('All Input Keys: ' . json_encode(array_keys($request->all())));
            
            // Validate basic fields
            $validated = $request->validate([
                // Part I: Company Profile
                'company' => 'required|string|max:255',
                'respondent' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'industrySelector' => 'required|string',
                'companySize' => 'required|string',
                
                // Part II: Hard-to-Fill Roles
                'job_title' => 'required|array|min:1',
                'job_title.*' => 'required|string|max:255',
                'job_classification' => 'required|array|min:1',
                'job_classification.*' => 'required|string|max:255',
                'vacancy_duration' => 'required|array|min:1',
                'vacancy_duration.*' => 'required|string',
                'technical_skills_missing' => 'nullable|array',
                'soft_skills_missing' => 'nullable|array',
                
                // Part IV: Engagement & Next Steps
                'lmi_features' => 'nullable|array',
                'lmi_features.*' => 'nullable|string',
                'specific_inputs' => 'nullable|string|max:5000',
                
                'consent' => 'required|accepted',
            ]);

            Log::info('Basic validation passed');

            // Validate impact levels
            if (!is_array($request->job_title)) {
                throw new \Exception('job_title must be an array');
            }

            foreach ($request->job_title as $index => $jobTitle) {
                $impactLevel = $request->input("impact_level_{$index}");
                
                Log::info("Checking impact level for job {$index}: " . ($impactLevel ?? 'NULL'));
                
                if (empty($impactLevel)) {
                    $errorMessage = "Impact level is required for job entry #" . ($index + 1);
                    
                    if ($request->ajax() || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ["impact_level_{$index}" => [$errorMessage]]
                        ], 422);
                    }
                    
                    return back()->withErrors([
                        "impact_level_{$index}" => $errorMessage
                    ])->withInput();
                }
            }

            Log::info('Impact level validation passed');

            DB::beginTransaction();

            // Create main submission
            $submission = LmiSubmission::create([
                'company_name' => $request->company,
                'respondent_name' => $request->respondent,
                'position' => $request->position,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'industry_sector' => $request->industrySelector,
                'company_size' => $request->companySize,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            Log::info('Main submission created: ' . $submission->id);

            // Save hard-to-fill roles
            foreach ($request->job_title as $index => $jobTitle) {
                // Process skills
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

                // Get difficulty reasons for this specific job entry
                $jobDifficultyReasons = $request->input("difficulty_reasons_{$index}", []);
                
                // Ensure it's an array
                if (!is_array($jobDifficultyReasons)) {
                    $jobDifficultyReasons = [$jobDifficultyReasons];
                }

                Log::info("Job {$index} - Difficulty Reasons: " . json_encode($jobDifficultyReasons));

                // Create the hard-to-fill role
                $hardToFillRole = LmiHardToFillRole::create([
                    'lmi_submission_id' => $submission->id,
                    'job_title' => $jobTitle,
                    'job_classification' => $request->job_classification[$index],
                    'vacancy_duration' => $request->vacancy_duration[$index],
                    'difficulty_reasons' => $jobDifficultyReasons,
                    'technical_skills_missing' => $technicalSkillsArray,
                    'soft_skills_missing' => $softSkillsArray,
                ]);

                Log::info('Hard-to-fill role created: ' . $hardToFillRole->id);

                // Get the impact level for this specific role
                $impactLevel = $request->input("impact_level_{$index}");

                // Prepare diagnosis data
                $diagnosisData = [
                    'lmi_submission_id' => $submission->id,
                    'lmi_hard_to_fill_role_id' => $hardToFillRole->id,
                    'impact_level' => $impactLevel,
                ];

                // Add Part 3 data ONLY to the FIRST diagnosis (company-level data)
                if ($index === 0) {
                    $rejectionReasons = $request->rejection_reasons ?? [];
                    
                    // Ensure it's an array
                    if (!is_array($rejectionReasons)) {
                        $rejectionReasons = [$rejectionReasons];
                    }
                    
                    $diagnosisData['rejection_reasons'] = $rejectionReasons;
                    $diagnosisData['rejection_reasons_other'] = $request->rejection_reasons_other;
                    $diagnosisData['coordination_frequency'] = $request->coordination_frequency;
                    $diagnosisData['coordination_frequency_other'] = $request->coordination_frequency_other;
                }

                // Create diagnosis record
                LmiDiagnosis::create($diagnosisData);

                Log::info("Diagnosis created for submission {$submission->id} with impact: {$impactLevel}");
            }

            // Save engagement
            $lmiFeatures = $request->lmi_features ?? [];
            
            // Ensure it's an array
            if (!is_array($lmiFeatures)) {
                $lmiFeatures = [$lmiFeatures];
            }
            
            LmiEngagement::create([
                'lmi_submission_id' => $submission->id,
                'lmi_features' => $lmiFeatures,
                'specific_inputs' => $request->specific_inputs,
            ]);

            Log::info('Engagement created');

            DB::commit();

            Log::info('Transaction committed! Submission ID: ' . $submission->id);

            // Check if it's an AJAX request
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your submission (ID: ' . $submission->id . ') has been received and is pending admin confirmation.'
                ]);
            }

            // Regular form submission
            return redirect()->back()->with('success', 'Thank you! Your submission (ID: ' . $submission->id . ') has been received and is pending admin confirmation.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed: ' . json_encode($e->errors()));
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== SUBMISSION ERROR ===');
            Log::error('Message: ' . $e->getMessage());
            Log::error('Line: ' . $e->getLine());
            Log::error('File: ' . $e->getFile());
            Log::error('Trace: ' . $e->getTraceAsString());
            
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