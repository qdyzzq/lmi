<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMI Submissions - Admin Review</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    <aside class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10">
        <div class="p-6 border-b border-blue-800">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/dole_logo.png') }}" alt="LMI Logo" class="w-full h-full object-contain">
                </div>
                    <div>
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-auto">
            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span>📋</span> Regional Statistics
            </a>
            
            <a href="{{ route('admin.lmi-submissions.index') }}" class="flex items-center gap-3 p-3 bg-yellow-400 text-blue-900 font-bold rounded-lg transition shadow-md">
                <span>📋</span> LMI Submissions
            </a>
            
            <a href="{{ route('admin.job-titles.form') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
        <span class="opacity-70 group-hover:opacity-100">💼</span> Job Titles Form
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full mt-4">
                @csrf
                <button type="submit" class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group w-full text-left">
                    <span class="opacity-70 group-hover:opacity-100">🚪</span> Logout
                </button>
            </form>
        </nav>

        <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
            © 2026 DOLE Region XI
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">LMI Submissions Review • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 border border-yellow-300">
                    <span class="font-bold">{{ $submissions->total() }}</span> Pending Submissions
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500 flex items-center justify-center">
                    📋
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
<main class="flex-1 overflow-y-auto p-8 bg-slate-100">
    <div class="max-w-7xl mx-auto">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Single Submission Display -->
        @if($submissions->total() > 0)
            @php $submission = $submissions->first(); @endphp
            
            <!-- Admin Review Card -->
            <div class="bg-white rounded-xl shadow overflow-hidden admin-review-card mb-6" data-id="{{ $submission->id }}">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30 flex justify-between items-center">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-2xl">
                                🏢
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">{{ $submission->company_name }}</h3>
                                <div class="flex gap-4 mt-1 text-sm text-slate-600">
                                    <span><strong>Submitted by:</strong> {{ $submission->respondent_name }}</span>
                                    <span><strong>Position:</strong> {{ $submission->position }}</span>
                                    <span><strong>Date:</strong> {{ $submission->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <!-- Status Badge -->
                        <span class="px-3 py-1 rounded-full text-xs font-bold 
                            {{ $submission->status === 'approved' ? 'bg-green-100 text-green-800' : 
                               ($submission->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-slate-200">
                    <div class="px-6 flex gap-1">
                        <button class="tab-btn active px-4 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600" 
                                onclick="switchTab(this, 'company-{{ $submission->id }}')">
                            Company Profile
                        </button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-900" 
                                onclick="switchTab(this, 'roles-{{ $submission->id }}')">
                            Hard-to-Fill Roles ({{ $submission->hardToFillRoles->count() }})
                        </button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-900" 
                                onclick="switchTab(this, 'impact-{{ $submission->id }}')">
                            DIAGNOSIS OF MISMATCH
                        </button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-900" 
                                onclick="switchTab(this, 'engagement-{{ $submission->id }}')">
                            Engagement & Next Steps
                        </button>
                    </div>
                </div>

                    <!-- Company Profile Tab -->
<div class="tab-content active" id="company-{{ $submission->id }}">
    <form action="{{ route('admin.lmi-submissions.update', $submission->id) }}" method="POST" class="edit-form">
        @csrf
        @method('PUT')
        
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">Field</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">Value</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <tr>
                    <td class="px-6 py-4 font-medium">Company Name</td>
                    <td class="px-6 py-4">
                        <input type="text" name="company_name" value="{{ $submission->company_name }}" 
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Respondent</td>
                    <td class="px-6 py-4">
                        <input type="text" name="respondent_name" value="{{ $submission->respondent_name }}" 
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Position</td>
                    <td class="px-6 py-4">
                        <input type="text" name="position" value="{{ $submission->position }}" 
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Contact Number</td>
                    <td class="px-6 py-4">
                        <input type="text" name="contact_number" value="{{ $submission->contact_number }}" 
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Email</td>
                    <td class="px-6 py-4">
                        <input type="email" name="email" value="{{ $submission->email }}" 
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Industry Sector</td>
                    <td class="px-6 py-4">
                        <select name="industry_sector" class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            <option value="Agriculture, Forestry and Fishing" {{ $submission->industry_sector == 'Agriculture, Forestry and Fishing' ? 'selected' : '' }}>Agriculture, Forestry and Fishing</option>
                            <option value="Mining and Quarrying" {{ $submission->industry_sector == 'Mining and Quarrying' ? 'selected' : '' }}>Mining and Quarrying</option>
                            <option value="Manufacturing" {{ $submission->industry_sector == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="Construction" {{ $submission->industry_sector == 'Construction' ? 'selected' : '' }}>Construction</option>
                            <option value="Wholesale and Retail Trade" {{ $submission->industry_sector == 'Wholesale and Retail Trade' ? 'selected' : '' }}>Wholesale and Retail Trade</option>
                            <option value="Transportation and Storage" {{ $submission->industry_sector == 'Transportation and Storage' ? 'selected' : '' }}>Transportation and Storage</option>
                            <option value="Accommodation and Food Service" {{ $submission->industry_sector == 'Accommodation and Food Service' ? 'selected' : '' }}>Accommodation and Food Service</option>
                            <option value="Information and Communication" {{ $submission->industry_sector == 'Information and Communication' ? 'selected' : '' }}>Information and Communication</option>
                            <option value="Financial and Insurance Activities" {{ $submission->industry_sector == 'Financial and Insurance Activities' ? 'selected' : '' }}>Financial and Insurance Activities</option>
                            <option value="Real Estate Activities" {{ $submission->industry_sector == 'Real Estate Activities' ? 'selected' : '' }}>Real Estate Activities</option>
                            <option value="Professional, Scientific and Technical" {{ $submission->industry_sector == 'Professional, Scientific and Technical' ? 'selected' : '' }}>Professional, Scientific and Technical</option>
                            <option value="Administrative & Support Services" {{ $submission->industry_sector == 'Administrative & Support Services' ? 'selected' : '' }}>Administrative & Support Services</option>
                            <option value="Public Administration and Defense" {{ $submission->industry_sector == 'Public Administration and Defense' ? 'selected' : '' }}>Public Administration and Defense</option>
                            <option value="Education" {{ $submission->industry_sector == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Human Health and Social Work" {{ $submission->industry_sector == 'Human Health and Social Work' ? 'selected' : '' }}>Human Health and Social Work</option>
                            <option value="Arts, Entertainment and Recreation" {{ $submission->industry_sector == 'Arts, Entertainment and Recreation' ? 'selected' : '' }}>Arts, Entertainment and Recreation</option>
                            <option value="Other Service Activities" {{ $submission->industry_sector == 'Other Service Activities' ? 'selected' : '' }}>Other Service Activities</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium">Company Size</td>
                    <td class="px-6 py-4">
                        <select name="company_size" class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            <option value="Less than 50" {{ $submission->company_size == 'Less than 50' ? 'selected' : '' }}>Less than 50</option>
                            <option value="51-200" {{ $submission->company_size == '51-200' ? 'selected' : '' }}>51-200</option>
                            <option value="201-500" {{ $submission->company_size == '201-500' ? 'selected' : '' }}>201-500</option>
                            <option value="More than 500" {{ $submission->company_size == 'More than 500' ? 'selected' : '' }}>More than 500</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Edit/Save Buttons -->
        <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 border-t">
            <button type="button" onclick="toggleEdit(this)" class="edit-btn px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                ✏️ Edit
            </button>
            <button type="submit" class="save-btn hidden px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                💾 Save Changes
            </button>
            <button type="button" onclick="cancelEdit(this)" class="cancel-btn hidden px-6 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 transition">
                Cancel
            </button>
        </div>
    </form>
</div>

                  <!-- Hard-to-Fill Roles Tab with Editable Impact Levels -->
<div class="tab-content hidden" id="roles-{{ $submission->id }}">
    <form action="{{ route('admin.lmi-submissions.update-roles', $submission->id) }}" method="POST" class="edit-roles-form">
        @csrf
        @method('PUT')
        
        <div class="p-6">
            <h4 class="font-bold text-slate-800 mb-4">Hard-to-Fill Roles & Impact Assessment</h4>
            @if($submission->hardToFillRoles->count() > 0)
                @foreach($submission->hardToFillRoles as $index => $role)
                    @php
                        // Get the diagnosis for this specific role
                        $diagnosis = $submission->diagnoses->get($index);
                    @endphp
                    
                    <div class="bg-white rounded-lg p-6 mb-6 border-2 border-slate-200 role-card">
                        <input type="hidden" name="roles[{{ $index }}][id]" value="{{ $role->id }}">
                        @if($diagnosis)
                            <input type="hidden" name="roles[{{ $index }}][diagnosis_id]" value="{{ $diagnosis->id }}">
                        @endif
                        
                        <!-- Role Header with Impact Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="font-bold text-lg text-slate-800">Role {{ $index + 1 }}</h5>
                            
                            <!-- View Mode - Badge -->
                            <div class="impact-view-mode-{{ $index }}">
                                @if($diagnosis)
                                    <span class="px-4 py-2 rounded-full text-sm font-bold
                                        {{ $diagnosis->impact_level === 'High' ? 'bg-red-100 text-red-700' : 
                                           ($diagnosis->impact_level === 'Medium' ? 'bg-orange-100 text-orange-700' : 
                                           'bg-green-100 text-green-700') }}">
                                        {{ $diagnosis->impact_level }} Impact
                                    </span>
                                @else
                                    <span class="px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-700">
                                        No Impact Assessment
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Edit Mode - Dropdown -->
                            <div class="impact-edit-mode-{{ $index }} hidden">
                                <select name="roles[{{ $index }}][impact_level]" 
                                        class="role-editable-field impact-select-{{ $index }} px-4 py-2 border-2 rounded-full text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500
                                        {{ $diagnosis && $diagnosis->impact_level === 'High' ? 'bg-red-100 text-red-700 border-red-300' : 
                                           ($diagnosis && $diagnosis->impact_level === 'Medium' ? 'bg-orange-100 text-orange-700 border-orange-300' : 
                                           'bg-green-100 text-green-700 border-green-300') }}"
                                        onchange="updateImpactColor({{ $index }})"
                                        disabled>
                                    <option value="Low" {{ $diagnosis && $diagnosis->impact_level === 'Low' ? 'selected' : '' }}>Low Impact</option>
                                    <option value="Medium" {{ $diagnosis && $diagnosis->impact_level === 'Medium' ? 'selected' : '' }}>Medium Impact</option>
                                    <option value="High" {{ $diagnosis && $diagnosis->impact_level === 'High' ? 'selected' : '' }}>High Impact</option>
                                </select>
                            </div>
                        </div>

                        <!-- Job Title -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Job Title: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="roles[{{ $index }}][job_title]" 
                                   value="{{ $role->job_title }}" 
                                   class="role-editable-field w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500"
                                   placeholder="e.g. Senior Java Developer"
                                   disabled>
                        </div>
                        
                        <!-- Job Classification -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Standard Job Classifications / Families: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="roles[{{ $index }}][job_classification]" 
                                   value="{{ $role->job_classification }}" 
                                   class="role-editable-field w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500"
                                   placeholder="Select job classification"
                                   disabled>
                        </div>

                        <!-- Vacancy Duration -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Duration that the Vacancy is Open: <span class="text-red-500">*</span>
                            </label>
                            <select name="roles[{{ $index }}][vacancy_duration]" 
                                    class="role-editable-field w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    disabled>
                                <option value="">Select duration</option>
                                <option value="Less than 1 month" {{ $role->vacancy_duration == 'Less than 1 month' ? 'selected' : '' }}>Less than 1 month</option>
                                <option value="1-3 months" {{ $role->vacancy_duration == '1-3 months' ? 'selected' : '' }}>1-3 months</option>
                                <option value="3-6 months" {{ $role->vacancy_duration == '3-6 months' ? 'selected' : '' }}>3-6 months</option>
                                <option value="6-12 months" {{ $role->vacancy_duration == '6-12 months' ? 'selected' : '' }}>6-12 months</option>
                                <option value="More than 12 months" {{ $role->vacancy_duration == 'More than 12 months' ? 'selected' : '' }}>More than 12 months</option>
                            </select>
                        </div>

                        <!-- Difficulty Reasons -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Reasons For Difficulty (Role-Level) <span class="text-sm text-gray-500 italic">(Check all that apply)</span>
                            </label>
                            
                            @php
                                $reasons = $role->difficulty_reasons;
                                if (is_string($reasons)) {
                                    $reasons = json_decode($reasons, true) ?? [];
                                }
                                if (!is_array($reasons)) {
                                    $reasons = [];
                                }
                                
                                $flatReasons = [];
                                foreach ($reasons as $reason) {
                                    if (is_array($reason)) {
                                        $flatReasons = array_merge($flatReasons, array_filter($reason));
                                    } elseif (is_string($reason) && !empty($reason)) {
                                        $flatReasons[] = $reason;
                                    }
                                }
                                
                                $hasTechnical = in_array('Technical / Hard Skills Missing', $flatReasons);
                                $hasSoft = in_array('Soft / Employability Skills Missing', $flatReasons);
                                
                                $techSkills = $role->technical_skills_missing;
                                if (is_string($techSkills)) {
                                    $techSkills = json_decode($techSkills, true) ?? [];
                                }
                                if (!is_array($techSkills)) {
                                    $techSkills = [];
                                }
                                
                                $softSkills = $role->soft_skills_missing;
                                if (is_string($softSkills)) {
                                    $softSkills = json_decode($softSkills, true) ?? [];
                                }
                                if (!is_array($softSkills)) {
                                    $softSkills = [];
                                }
                            @endphp

                            <div class="space-y-3">
                                <!-- Technical Skills Option -->
                                <div class="technical-skills-container-{{ $index }} border-2 rounded-lg transition-all {{ $hasTechnical ? 'border-teal-400 bg-teal-50' : 'border-gray-200' }}">
                                    <label class="flex items-start p-4 cursor-pointer">
                                        <input type="checkbox" 
                                               name="roles[{{ $index }}][difficulty_reasons][]" 
                                               value="Technical / Hard Skills Missing"
                                               class="technical-checkbox-{{ $index }} role-editable-field mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                               {{ $hasTechnical ? 'checked' : '' }}
                                               disabled>
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-gray-700">Technical / Hard Skills Missing</div>
                                            <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                                            
                                            <!-- Technical Skills Details -->
                                            <div class="technical-details-{{ $index }} mt-3 {{ $hasTechnical ? '' : 'hidden' }}">
                                                <label class="block text-gray-600 text-xs font-medium mb-2">
                                                    What specific technical tools, software, or machinery knowledge is missing?
                                                </label>
                                                
                                                <!-- View Mode -->
                                                <div class="view-mode">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($techSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm font-medium">
                                                                    {{ $skill }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                        @if(count($techSkills) == 0)
                                                            <span class="text-gray-400 text-sm">No skills specified</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Edit Mode -->
                                                <div class="edit-mode hidden">
                                                    <div class="technical-tags-{{ $index }} flex flex-wrap gap-2 mb-2" data-role-index="{{ $index }}">
                                                        @foreach($techSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="skill-tag px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm flex items-center gap-2">
                                                                    {{ $skill }}
                                                                    <button type="button" class="text-teal-600 hover:text-teal-800 font-bold text-lg leading-none" onclick="removeTag(this)">×</button>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    
                                                    <div class="flex gap-2">
                                                        <input type="text"
                                                               class="technical-skill-input-{{ $index }} role-editable-field flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                               placeholder="Type a skill and press Enter"
                                                               onkeypress="handleTagInput(event, 'technical', {{ $index }})"
                                                               disabled>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each skill</p>
                                                </div>
                                                
                                                <input type="hidden" 
                                                       name="roles[{{ $index }}][technical_skills_missing]" 
                                                       class="technical-skills-hidden-{{ $index }}"
                                                       value="{{ implode(',', $techSkills) }}">
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Soft Skills Option -->
                                <div class="soft-skills-container-{{ $index }} border-2 rounded-lg transition-all {{ $hasSoft ? 'border-teal-400 bg-teal-50' : 'border-gray-200' }}">
                                    <label class="flex items-start p-4 cursor-pointer">
                                        <input type="checkbox" 
                                               name="roles[{{ $index }}][difficulty_reasons][]" 
                                               value="Soft / Employability Skills Missing"
                                               class="soft-checkbox-{{ $index }} role-editable-field mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                               {{ $hasSoft ? 'checked' : '' }}
                                               disabled>
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-gray-700">Soft / Employability Skills Missing</div>
                                            <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                                            
                                            <!-- Soft Skills Details -->
                                            <div class="soft-details-{{ $index }} mt-3 {{ $hasSoft ? '' : 'hidden' }}">
                                                <label class="block text-gray-600 text-xs font-medium mb-2">
                                                    What attitude or behavioral traits cause you to reject applicants?
                                                </label>
                                                
                                                <!-- View Mode -->
                                                <div class="view-mode">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($softSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm font-medium">
                                                                    {{ $skill }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                        @if(count($softSkills) == 0)
                                                            <span class="text-gray-400 text-sm">No skills specified</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Edit Mode -->
                                                <div class="edit-mode hidden">
                                                    <div class="soft-tags-{{ $index }} flex flex-wrap gap-2 mb-2" data-role-index="{{ $index }}">
                                                        @foreach($softSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="skill-tag px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm flex items-center gap-2">
                                                                    {{ $skill }}
                                                                    <button type="button" class="text-pink-600 hover:text-pink-800 font-bold text-lg leading-none" onclick="removeTag(this)">×</button>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    
                                                    <div class="flex gap-2">
                                                        <input type="text"
                                                               class="soft-skill-input-{{ $index }} role-editable-field flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                               placeholder="Type a trait and press Enter"
                                                               onkeypress="handleTagInput(event, 'soft', {{ $index }})"
                                                               disabled>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each trait</p>
                                                </div>
                                                
                                                <input type="hidden" 
                                                       name="roles[{{ $index }}][soft_skills_missing]" 
                                                       class="soft-skills-hidden-{{ $index }}"
                                                       value="{{ implode(',', $softSkills) }}">
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-slate-400">No roles specified.</p>
            @endif
        </div>
        
        <!-- Edit/Save Buttons -->
        <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 border-t">
            <button type="button" onclick="toggleRolesEdit(this)" class="edit-roles-btn px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                ✏️ Edit Roles
            </button>
            <button type="submit" class="save-roles-btn hidden px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                💾 Save Changes
            </button>
            <button type="button" onclick="cancelRolesEdit(this)" class="cancel-roles-btn hidden px-6 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 transition">
                Cancel
            </button>
        </div>
    </form>
</div>

<!-- Diagnosis of Mismatch Tab -->
<div class="tab-content hidden" id="impact-{{ $submission->id }}">
    <form action="{{ route('admin.lmi-submissions.update-diagnosis', $submission->id) }}" method="POST" class="edit-diagnosis-form">
        @csrf
        @method('PUT')
        
        <div class="p-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-2 text-orange-700 text-base font-semibold mb-2">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    PART III: DIAGNOSIS OF MISMATCH
                </div>
                <p class="text-orange-600 text-xs italic">
                    For applicants who meet formal qualifications (degree, license, or certification), which observable factors most often cause them to be rejected?
                </p>
            </div>

            @if($submission->diagnoses->count() > 0)
                @php $diagnosis = $submission->diagnoses->first(); @endphp
                
                <input type="hidden" name="diagnosis_id" value="{{ $diagnosis->id }}">

                <!-- 13. Reason Qualified Applicants Are Rejected -->
                <div class="bg-white rounded-lg p-6 border-2 border-slate-200 mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-4">
                        13. Reason Qualified Applicants Are Rejected (Applicant-Level) 
                        <span class="text-gray-500 italic text-xs">(Check all that apply)</span>
                    </label>
                    
                    @php
                        $rejectionReasons = $diagnosis->rejection_reasons;
                        if (is_string($rejectionReasons)) {
                            $rejectionReasons = json_decode($rejectionReasons, true) ?? [];
                        }
                        if (!is_array($rejectionReasons)) {
                            $rejectionReasons = [];
                        }
                        
                        $hasOther = in_array('Other', $rejectionReasons);
                    @endphp

                    <!-- View Mode -->
                    <div class="diagnosis-view-mode">
                        @if(count($rejectionReasons) > 0)
                            <div class="space-y-2">
                                @foreach($rejectionReasons as $reason)
                                    @if($reason !== 'Other' && !empty($reason))
                                        <div class="flex items-center gap-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-gray-800">{{ $reason }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                @if($hasOther && $diagnosis->rejection_reasons_other)
                                    <div class="flex items-center gap-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-800">Other: {{ $diagnosis->rejection_reasons_other }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-slate-400 text-sm">No rejection reasons specified</p>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div class="diagnosis-edit-mode hidden space-y-3">
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ in_array('Lack of practical / hands-on experience', $rejectionReasons) ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="checkbox" 
                                   name="rejection_reasons[]" 
                                   value="Lack of practical / hands-on experience"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                   {{ in_array('Lack of practical / hands-on experience', $rejectionReasons) ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Lack of practical / hands-on experience</div>
                                <div class="text-xs text-gray-500 mt-1">Cannot apply theory to real work; requires supervision</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ in_array('Skills are outdated', $rejectionReasons) ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="checkbox" 
                                   name="rejection_reasons[]" 
                                   value="Skills are outdated"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                   {{ in_array('Skills are outdated', $rejectionReasons) ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Skills are outdated</div>
                                <div class="text-xs text-gray-500 mt-1">Training received does not match current tools, systems, or industry practices</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ in_array('Poor communication skills', $rejectionReasons) ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="checkbox" 
                                   name="rejection_reasons[]" 
                                   value="Poor communication skills"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                   {{ in_array('Poor communication skills', $rejectionReasons) ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Poor communication skills</div>
                                <div class="text-xs text-gray-500 mt-1">Oral, written, presentation, or cross-cultural communication issues</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ in_array('Low job readiness / poor interview performance', $rejectionReasons) ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="checkbox" 
                                   name="rejection_reasons[]" 
                                   value="Low job readiness / poor interview performance"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                   {{ in_array('Low job readiness / poor interview performance', $rejectionReasons) ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Low job readiness / poor interview performance</div>
                                <div class="text-xs text-gray-500 mt-1">Cannot demonstrate readiness during recruitment; fails assessments; lacks workplace etiquette</div>
                            </div>
                        </label>
                        
                        <!-- Other Option -->
                        <div class="border-2 rounded-lg transition-all {{ $hasOther ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <label class="flex items-start p-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="rejection_reasons[]" 
                                       value="Other"
                                       class="diagnosis-editable-field other-rejection-checkbox mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                       {{ $hasOther ? 'checked' : '' }}
                                       onchange="toggleOtherRejectionInput(this)"
                                       disabled>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-800">Other (please specify)</div>
                                </div>
                            </label>
                            
                            <div class="other-rejection-input px-3 pb-3 ml-7 {{ $hasOther ? '' : 'hidden' }}">
                                <input type="text"
                                       name="rejection_reasons_other"
                                       value="{{ $diagnosis->rejection_reasons_other ?? '' }}"
                                       placeholder="Please specify other reasons..."
                                       class="diagnosis-editable-field w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                       disabled />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 14. Coordination with Universities/Colleges -->
                <div class="bg-white rounded-lg p-6 border-2 border-slate-200 mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-4">
                        14. How often do you coordinate with Universities/Colleges to discuss your skills requirements? 
                        <span class="text-gray-500 italic text-xs">(Select ONE)</span>
                    </label>

                    @php
                        $isOtherCoordination = !in_array($diagnosis->coordination_frequency, [
                            'Never', 'Rarely', 'Occasionally', 'Frequently'
                        ]) && !empty($diagnosis->coordination_frequency);
                    @endphp

                    <!-- View Mode -->
                    <div class="diagnosis-view-mode">
                        <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium text-gray-800">{{ $diagnosis->coordination_frequency }}</span>
                            </div>
                            @if($isOtherCoordination && $diagnosis->coordination_frequency_other)
                                <p class="text-sm text-gray-600 mt-2 ml-7">{{ $diagnosis->coordination_frequency_other }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div class="diagnosis-edit-mode hidden space-y-3">
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ $diagnosis->coordination_frequency === 'Never' ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="coordination_frequency" 
                                   value="Never"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                   {{ $diagnosis->coordination_frequency === 'Never' ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Never</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ $diagnosis->coordination_frequency === 'Rarely' ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="coordination_frequency" 
                                   value="Rarely"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                   {{ $diagnosis->coordination_frequency === 'Rarely' ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Rarely</div>
                                <div class="text-xs text-gray-500 mt-1">Only when invited to graduations/events</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ $diagnosis->coordination_frequency === 'Occasionally' ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="coordination_frequency" 
                                   value="Occasionally"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                   {{ $diagnosis->coordination_frequency === 'Occasionally' ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Occasionally</div>
                                <div class="text-xs text-gray-500 mt-1">During OJT placement</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300
                            {{ $diagnosis->coordination_frequency === 'Frequently' ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="coordination_frequency" 
                                   value="Frequently"
                                   class="diagnosis-editable-field mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                   {{ $diagnosis->coordination_frequency === 'Frequently' ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-800">Frequently</div>
                                <div class="text-xs text-gray-500 mt-1">We sit on advisory boards/curriculum reviews</div>
                            </div>
                        </label>
                        
                        <!-- Other Option -->
                        <div class="border-2 rounded-lg transition-all {{ $isOtherCoordination ? 'bg-orange-50 border-orange-300' : 'border-gray-200' }}">
                            <label class="flex items-start p-3 cursor-pointer">
                                <input type="radio" 
                                       name="coordination_frequency" 
                                       value="Other"
                                       class="diagnosis-editable-field other-coordination-radio mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                       {{ $isOtherCoordination ? 'checked' : '' }}
                                       onchange="toggleOtherCoordinationInput(this)"
                                       disabled>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-800">Other (please specify)</div>
                                </div>
                            </label>
                            
                            <div class="other-coordination-input px-3 pb-3 ml-7 {{ $isOtherCoordination ? '' : 'hidden' }}">
                                <input type="text"
                                       name="coordination_frequency_other"
                                       value="{{ $diagnosis->coordination_frequency_other ?? '' }}"
                                       placeholder="Please specify..."
                                       class="diagnosis-editable-field w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                       disabled />
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📋</div>
                    <p class="text-slate-500 text-lg font-medium">No diagnosis data available</p>
                    <p class="text-slate-400 text-sm mt-2">Complete Part III of the form to see diagnosis information</p>
                </div>
            @endif
        </div>
        
        <!-- Edit/Save Buttons -->
        <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 border-t">
            <button type="button" onclick="toggleDiagnosisEdit(this)" class="edit-diagnosis-btn px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                ✏️ Edit Diagnosis
            </button>
            <button type="submit" class="save-diagnosis-btn hidden px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                💾 Save Changes
            </button>
            <button type="button" onclick="cancelDiagnosisEdit(this)" class="cancel-diagnosis-btn hidden px-6 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 transition">
                Cancel
            </button>
        </div>
    </form>
</div>

                   

                   <!-- Engagement Tab -->
<div class="tab-content hidden" id="engagement-{{ $submission->id }}">
    <form action="{{ route('admin.lmi-submissions.update-engagement', $submission->id) }}" method="POST" class="engagement-edit-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="engagement_id" value="{{ $submission->engagement->id ?? '' }}">
        
        <div class="p-6">
            @if($submission->engagement)
                <!-- Part IV Header -->
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-blue-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">
                        🤝
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-lg">PART IV: ENGAGEMENT & NEXT STEPS</h4>
                        <p class="text-sm text-slate-500">Features and feedback preferences</p>
                    </div>
                </div>

                <!-- Question 20: LMI Features -->
                <div class="mb-8">
                    <label class="block text-slate-700 font-semibold mb-4">
                        20. If DOLE provides a Regional LMI Dashboard, what features would be most useful for you?
                        <span class="text-slate-500 font-normal text-sm">(Top 2 selected)</span>
                    </label>

                    @php
                        $features = $submission->engagement->lmi_features;
                        if (is_string($features)) {
                            $features = json_decode($features, true) ?? [];
                        }
                        if (!is_array($features)) {
                            $features = [];
                        }
                    @endphp

                    <!-- View Mode -->
                    <div class="engagement-view-mode space-y-3">
                        @if(count($features) > 0)
                            @foreach($features as $feature)
                                @if(!empty($feature))
                                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-slate-700">{{ $feature }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg text-center">
                                <p class="text-slate-400">No features selected</p>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div class="engagement-edit-mode hidden space-y-3">
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" name="lmi_features[]" value="Viewing the supply of graduates" 
                                   class="engagement-editable-field mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   {{ in_array('Viewing the supply of graduates', $features) ? 'checked' : '' }}
                                   disabled>
                            <span class="ml-3 text-sm text-gray-700">
                                Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")
                            </span>
                        </label>
                        
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" name="lmi_features[]" value="A channel to submit real-time feedback" 
                                   class="engagement-editable-field mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   {{ in_array('A channel to submit real-time feedback', $features) ? 'checked' : '' }}
                                   disabled>
                            <span class="ml-3 text-sm text-gray-700">
                                A channel to submit real-time feedback on curriculum quality
                            </span>
                        </label>
                        
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" name="lmi_features[]" value="A directory of job placement offices" 
                                   class="engagement-editable-field mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   {{ in_array('A directory of job placement offices', $features) ? 'checked' : '' }}
                                   disabled>
                            <span class="ml-3 text-sm text-gray-700">
                                A directory of job placement offices and Public Employment offices (PESOs)
                            </span>
                        </label>
                        
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" name="lmi_features[]" value="Other" 
                                   class="engagement-editable-field mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   {{ in_array('Other', $features) ? 'checked' : '' }}
                                   disabled>
                            <span class="ml-3 text-sm text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <!-- Additional Insights/Suggestions -->
                <div class="mb-6">
                    <label class="block text-slate-700 font-semibold mb-3">
                        Additional Insights or Suggestions
                        <span class="text-slate-500 font-normal text-sm">(Optional)</span>
                    </label>

                    <!-- View Mode -->
                    <div class="engagement-view-mode">
                        @if($submission->engagement->specific_inputs)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                                <p class="text-slate-700 whitespace-pre-wrap">{{ $submission->engagement->specific_inputs }}</p>
                            </div>
                        @else
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg text-center">
                                <p class="text-slate-400">No additional inputs provided</p>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div class="engagement-edit-mode hidden">
                        <textarea 
                            name="specific_inputs"
                            rows="5"
                            placeholder="Share any additional insights or suggestions..."
                            class="engagement-editable-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"
                            disabled>{{ $submission->engagement->specific_inputs ?? '' }}</textarea>
                    </div>
                </div>

            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🤝</div>
                    <p class="text-slate-500 text-lg font-medium">No engagement data available</p>
                    <p class="text-slate-400 text-sm mt-2">Complete Part IV of the form to see engagement information</p>
                </div>
            @endif
        </div>

        <!-- Edit/Save Buttons -->
        @if($submission->engagement)
            <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 border-t">
                <button type="button" onclick="toggleEngagementEdit(this)" class="edit-engagement-btn px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    ✏️ Edit Engagement
                </button>
                <button type="submit" class="save-engagement-btn hidden px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    💾 Save Changes
                </button>
                <button type="button" onclick="cancelEngagementEdit(this)" class="cancel-engagement-btn hidden px-6 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 transition">
                    Cancel
                </button>
            </div>
        @endif
    </form>
</div>
                    <!-- Admin Actions with Pagination -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    @if($submission->admin_notes)
                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <span class="font-bold text-blue-700">Admin Notes:</span>
                            <p class="text-sm text-blue-600 mt-1">{{ $submission->admin_notes }}</p>
                        </div>
                    @endif

                    @if($submission->status === 'pending')
                        <div class="flex gap-3 justify-end mb-4">
                            <!-- Reject Button -->
                            <form id="reject-form" action="{{ route('admin.lmi-submissions.reject', $submission->id) }}" method="POST">
                            @csrf
                            <button type="button" id="reject-btn" class="px-6 py-2 border-2 border-red-600 text-red-600 font-bold rounded-lg hover:bg-red-50 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                        </form>

                            <!-- Approve Button -->
                            <form id="approve-form" action="{{ route('admin.lmi-submissions.approve', $submission->id) }}" method="POST">
                            @csrf
                            <button type="button" id="approve-btn" class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                        </form>
                        </div>
                    @endif

                    <!-- Pagination Controls -->
                    @if($submissions->hasPages())
                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <!-- Page Info -->
                            <div class="text-sm text-slate-600">
                                Showing submission <strong>{{ $submissions->currentPage() }}</strong> of <strong>{{ $submissions->total() }}</strong>
                            </div>

                            <!-- Pagination Buttons -->
                            <div class="flex gap-2 flex-wrap justify-center">
                                {{-- First Page --}}
                                @if($submissions->onFirstPage())
                                    <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                        &laquo; First
                                    </button>
                                @else
                                    <a href="{{ $submissions->url(1) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                        &laquo; First
                                    </a>
                                @endif

                                {{-- Previous Page --}}
                                @if($submissions->onFirstPage())
                                    <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                        &lsaquo; Prev
                                    </button>
                                @else
                                    <a href="{{ $submissions->previousPageUrl() }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                        &lsaquo; Prev
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach(range(1, $submissions->lastPage()) as $page)
                                    @if($page == $submissions->currentPage())
                                        <button class="px-3 py-2 border-2 border-blue-600 bg-blue-600 text-white rounded font-bold text-sm">
                                            {{ $page }}
                                        </button>
                                    @elseif($page === 1 || $page === $submissions->lastPage() || abs($page - $submissions->currentPage()) <= 2)
                                        <a href="{{ $submissions->url($page) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                            {{ $page }}
                                        </a>
                                    @elseif(abs($page - $submissions->currentPage()) === 3)
                                        <span class="px-3 py-2 text-slate-400 text-sm">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Page --}}
                                @if($submissions->hasMorePages())
                                    <a href="{{ $submissions->nextPageUrl() }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                        Next &rsaquo;
                                    </a>
                                @else
                                    <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                        Next &rsaquo;
                                    </button>
                                @endif

                                {{-- Last Page --}}
                                @if($submissions->hasMorePages())
                                    <a href="{{ $submissions->url($submissions->lastPage()) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                        Last &raquo;
                                    </a>
                                @else
                                    <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                        Last &raquo;
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-8 text-center">
                <div class="text-6xl mb-4">🎉</div>
                <p class="text-slate-500 text-lg font-medium">No submissions to review at this time.</p>
                <p class="text-slate-400 text-sm mt-2">All submissions have been processed!</p>
            </div>
        @endif
    </div>
</main>
<!-- Approve Confirmation Modal -->
<div id="approve-confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center px-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold mb-2">Confirm Approval</h3>
            <p class="text-sm text-gray-500 mb-6">
                Are you sure you want to approve this submission? The data will be published to the public dashboard and cannot be easily undone.
            </p>
            <div class="flex gap-3">
                <button type="button" id="cancel-approve-btn"
                        class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button type="button" id="confirm-approve-btn"
                        class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                    Yes, Approve
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Reject Confirmation Modal -->
<div id="reject-confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center px-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Rejection</h3>
            <p class="text-sm text-gray-500 mb-6">
                Are you sure you want to reject this submission? This action will remove it from the pending queue.
            </p>
            <div class="flex gap-3">
                <button type="button" id="cancel-reject-btn"
                        class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button type="button" id="confirm-reject-btn"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                    Yes, Reject
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // Auto-hide success/error messages after 2 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.bg-green-50');
        const errorMessage = document.querySelector('.bg-red-50');
        
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500); // Remove after fade out
            }, 2000); // Wait 2 seconds before fading
        }
        
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }, 2000);
        }
    });

    function toggleEdit(button) {
        const form = button.closest('form');
        
        const editableFields = form.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.disabled = false;
            field.classList.add('bg-white', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
            field.classList.remove('bg-gray-50');
        });
        
        form.querySelector('.edit-btn').classList.add('hidden');
        form.querySelector('.save-btn').classList.remove('hidden');
        form.querySelector('.cancel-btn').classList.remove('hidden');
    }

    function cancelEdit(button) {
        const form = button.closest('form');
        
        const editableFields = form.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.disabled = true;
            field.classList.remove('bg-white', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
            field.classList.add('bg-gray-50');
        });
        
        form.reset();
        
        form.querySelector('.edit-btn').classList.remove('hidden');
        form.querySelector('.save-btn').classList.add('hidden');
        form.querySelector('.cancel-btn').classList.add('hidden');
    }

    function switchTab(button, tabId) {
        const card = button.closest('.admin-review-card');
        
        card.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-slate-600');
        });
        
        button.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
        button.classList.remove('text-slate-600');
        
        card.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('active');
        });
        
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
            selectedTab.classList.add('active');
        }
    }
    function toggleRolesEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    // Enable all editable fields
    const editableFields = form.querySelectorAll('.role-editable-field');
    editableFields.forEach(field => {
        field.disabled = false;
        field.classList.add('bg-white', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
        field.classList.remove('bg-gray-50');
    });
    
    // Toggle view/edit modes for skills
    tab.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    tab.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
    
    // Toggle impact level view/edit modes
    tab.querySelectorAll('[class^="impact-view-mode-"]').forEach(el => el.classList.add('hidden'));
    tab.querySelectorAll('[class^="impact-edit-mode-"]').forEach(el => el.classList.remove('hidden'));
    
    // Toggle buttons
    form.querySelector('.edit-roles-btn').classList.add('hidden');
    form.querySelector('.save-roles-btn').classList.remove('hidden');
    form.querySelector('.cancel-roles-btn').classList.remove('hidden');
}

// Update the cancelRolesEdit function to handle impact modes
function cancelRolesEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    // Disable all editable fields
    const editableFields = form.querySelectorAll('.role-editable-field');
    editableFields.forEach(field => {
        field.disabled = true;
        field.classList.remove('bg-white', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
        field.classList.add('bg-gray-50');
    });
    
    // Toggle view/edit modes for skills
    tab.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    tab.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
    
    // Toggle impact level view/edit modes
    tab.querySelectorAll('[class^="impact-view-mode-"]').forEach(el => el.classList.remove('hidden'));
    tab.querySelectorAll('[class^="impact-edit-mode-"]').forEach(el => el.classList.add('hidden'));
    
    // Reset form
    form.reset();
    
    // Toggle buttons
    form.querySelector('.edit-roles-btn').classList.remove('hidden');
    form.querySelector('.save-roles-btn').classList.add('hidden');
    form.querySelector('.cancel-roles-btn').classList.add('hidden');
}
function updateImpactColor(index) {
    const select = document.querySelector(`.impact-select-${index}`);
    const value = select.value;
    
    // Remove all color classes
    select.classList.remove('bg-red-100', 'text-red-700', 'border-red-300',
                           'bg-orange-100', 'text-orange-700', 'border-orange-300',
                           'bg-green-100', 'text-green-700', 'border-green-300');
    
    // Add appropriate color classes
    if (value === 'High') {
        select.classList.add('bg-red-100', 'text-red-700', 'border-red-300');
    } else if (value === 'Medium') {
        select.classList.add('bg-orange-100', 'text-orange-700', 'border-orange-300');
    } else {
        select.classList.add('bg-green-100', 'text-green-700', 'border-green-300');
    }
}

function handleTagInput(event, type, index) {
    if (event.key === 'Enter' || event.key === ',') {
        event.preventDefault();
        if (type === 'technical') {
            addTechnicalTag(index);
        } else {
            addSoftTag(index);
        }
    }
}

function addTechnicalTag(index) {
    const input = document.querySelector(`.technical-skill-input-${index}`);
    const value = input.value.trim().replace(/,/g, '');
    
    if (value) {
        const container = document.querySelector(`.technical-tags-${index}`);
        const tag = document.createElement('span');
        tag.className = 'skill-tag px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm flex items-center gap-2';
        tag.innerHTML = `
            ${value}
            <button type="button" class="text-teal-600 hover:text-teal-800 font-bold text-lg leading-none" onclick="removeTag(this)">×</button>
        `;
        container.appendChild(tag);
        input.value = '';
        updateHiddenInput(index, 'technical');
    }
}

function addSoftTag(index) {
    const input = document.querySelector(`.soft-skill-input-${index}`);
    const value = input.value.trim().replace(/,/g, '');
    
    if (value) {
        const container = document.querySelector(`.soft-tags-${index}`);
        const tag = document.createElement('span');
        tag.className = 'skill-tag px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm flex items-center gap-2';
        tag.innerHTML = `
            ${value}
            <button type="button" class="text-pink-600 hover:text-pink-800 font-bold text-lg leading-none" onclick="removeTag(this)">×</button>
        `;
        container.appendChild(tag);
        input.value = '';
        updateHiddenInput(index, 'soft');
    }
}

function removeTag(button) {
    const tag = button.closest('.skill-tag');
    const container = tag.parentElement;
    const index = container.dataset.roleIndex;
    const type = container.className.includes('technical') ? 'technical' : 'soft';
    
    tag.remove();
    updateHiddenInput(index, type);
}

function updateHiddenInput(index, type) {
    const container = document.querySelector(`.${type}-tags-${index}`);
    const tags = container.querySelectorAll('.skill-tag');
    const values = Array.from(tags).map(tag => tag.textContent.replace('×', '').trim());
    
    const hiddenInput = document.querySelector(`.${type}-skills-hidden-${index}`);
    hiddenInput.value = values.join(',');
}

document.addEventListener('DOMContentLoaded', function() {
        const approveBtn = document.getElementById('approve-btn');
        const approveForm = document.getElementById('approve-form');
        const confirmModal = document.getElementById('approve-confirmation-modal');
        const cancelBtn = document.getElementById('cancel-approve-btn');
        const confirmBtn = document.getElementById('confirm-approve-btn');

        if (approveBtn && confirmModal) {
            // Show modal when approve button is clicked
            approveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                confirmModal.classList.remove('hidden');
            });

            // Cancel button - hide modal
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    confirmModal.classList.add('hidden');
                });
            }

            // Confirm button - submit the form
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    confirmModal.classList.add('hidden');
                    approveForm.submit();
                });
            }

            // Close modal when clicking backdrop
            confirmModal.addEventListener('click', function(e) {
                if (e.target === confirmModal || e.target.classList.contains('bg-black')) {
                    confirmModal.classList.add('hidden');
                }
            });

            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
                    confirmModal.classList.add('hidden');
                }
            });
        }
    });
    // Reject Confirmation Modal
const rejectBtn = document.getElementById('reject-btn');
const rejectForm = document.getElementById('reject-form');
const rejectModal = document.getElementById('reject-confirmation-modal');
const cancelRejectBtn = document.getElementById('cancel-reject-btn');
const confirmRejectBtn = document.getElementById('confirm-reject-btn');

if (rejectBtn && rejectModal) {
    // Show modal when reject button is clicked
    rejectBtn.addEventListener('click', function(e) {
        e.preventDefault();
        rejectModal.classList.remove('hidden');
    });

    // Cancel button - hide modal
    if (cancelRejectBtn) {
        cancelRejectBtn.addEventListener('click', function() {
            rejectModal.classList.add('hidden');
        });
    }

    // Confirm button - submit the form
    if (confirmRejectBtn) {
        confirmRejectBtn.addEventListener('click', function() {
            rejectModal.classList.add('hidden');
            rejectForm.submit();
        });
    }

    // Close modal when clicking backdrop
    rejectModal.addEventListener('click', function(e) {
        if (e.target === rejectModal || e.target.classList.contains('bg-black')) {
            rejectModal.classList.add('hidden');
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !rejectModal.classList.contains('hidden')) {
            rejectModal.classList.add('hidden');
        }
    });
}

// Toggle diagnosis edit mode
function toggleDiagnosisEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    // Enable all editable fields
    const editableFields = form.querySelectorAll('.diagnosis-editable-field');
    editableFields.forEach(field => {
        field.disabled = false;
    });
    
    // Hide view mode, show edit mode
    tab.querySelectorAll('.diagnosis-view-mode').forEach(el => el.classList.add('hidden'));
    tab.querySelectorAll('.diagnosis-edit-mode').forEach(el => el.classList.remove('hidden'));
    
    // Toggle buttons
    form.querySelector('.edit-diagnosis-btn').classList.add('hidden');
    form.querySelector('.save-diagnosis-btn').classList.remove('hidden');
    form.querySelector('.cancel-diagnosis-btn').classList.remove('hidden');
}

// Cancel diagnosis edit
function cancelDiagnosisEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    // Disable all editable fields
    const editableFields = form.querySelectorAll('.diagnosis-editable-field');
    editableFields.forEach(field => {
        field.disabled = true;
    });
    
    // Show view mode, hide edit mode
    tab.querySelectorAll('.diagnosis-view-mode').forEach(el => el.classList.remove('hidden'));
    tab.querySelectorAll('.diagnosis-edit-mode').forEach(el => el.classList.add('hidden'));
    
    // Reset form
    form.reset();
    
    // Toggle buttons
    form.querySelector('.edit-diagnosis-btn').classList.remove('hidden');
    form.querySelector('.save-diagnosis-btn').classList.add('hidden');
    form.querySelector('.cancel-diagnosis-btn').classList.add('hidden');
}

// Toggle "Other" rejection reasons input
function toggleOtherRejectionInput(checkbox) {
    const container = checkbox.closest('.border-2');
    const input = container.querySelector('.other-rejection-input');
    
    if (checkbox.checked) {
        input.classList.remove('hidden');
        container.classList.add('bg-orange-50', 'border-orange-300');
    } else {
        input.classList.add('hidden');
        container.classList.remove('bg-orange-50', 'border-orange-300');
        input.querySelector('input').value = '';
    }
}

// Toggle "Other" coordination input
function toggleOtherCoordinationInput(radio) {
    const container = radio.closest('.border-2');
    const input = container.querySelector('.other-coordination-input');
    
    if (radio.checked) {
        input.classList.remove('hidden');
        container.classList.add('bg-orange-50', 'border-orange-300');
    } else {
        input.classList.add('hidden');
        container.classList.remove('bg-orange-50', 'border-orange-300');
        input.querySelector('input').value = '';
    }
}
function toggleEngagementEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    const editableFields = form.querySelectorAll('.engagement-editable-field');
    editableFields.forEach(field => {
        field.disabled = false;
    });
    
    tab.querySelectorAll('.engagement-view-mode').forEach(el => el.classList.add('hidden'));
    tab.querySelectorAll('.engagement-edit-mode').forEach(el => el.classList.remove('hidden'));
    
    form.querySelector('.edit-engagement-btn').classList.add('hidden');
    form.querySelector('.save-engagement-btn').classList.remove('hidden');
    form.querySelector('.cancel-engagement-btn').classList.remove('hidden');
}

function cancelEngagementEdit(button) {
    const form = button.closest('form');
    const tab = button.closest('.tab-content');
    
    const editableFields = form.querySelectorAll('.engagement-editable-field');
    editableFields.forEach(field => {
        field.disabled = true;
    });
    
    tab.querySelectorAll('.engagement-view-mode').forEach(el => el.classList.remove('hidden'));
    tab.querySelectorAll('.engagement-edit-mode').forEach(el => el.classList.add('hidden'));
    
    form.reset();
    
    form.querySelector('.edit-engagement-btn').classList.remove('hidden');
    form.querySelector('.save-engagement-btn').classList.add('hidden');
    form.querySelector('.cancel-engagement-btn').classList.add('hidden');
}

</script>
    
</body>
</html>