<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LMI Submissions - Admin Review</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.sidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">LMI Submissions Review • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="flex gap-2">
                    <div class="bg-yellow-100 px-3 py-1.5 rounded-lg text-xs font-medium text-yellow-700 border border-yellow-300">
                        <span id="header-pending-count" class="font-bold">{{ $pendingCount }}</span> Pending
                    </div>
                    <div class="bg-green-100 px-3 py-1.5 rounded-lg text-xs font-medium text-green-700 border border-green-300">
                        <span id="header-approved-count" class="font-bold">{{ $approvedCount }}</span> Approved
                    </div>
                    <div class="bg-red-100 px-3 py-1.5 rounded-lg text-xs font-medium text-red-700 border border-red-300">
                        <span id="header-rejected-count" class="font-bold">{{ $rejectedCount }}</span> Rejected
                    </div>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500 flex items-center justify-center">
                    📋
                </div>
            </div>
        </header>

        <!-- STATUS TABS -->
        <div class="bg-white border-b border-slate-200 px-8">
            <div class="flex gap-1">
                <button onclick="switchStatusTab('pending')" 
                        class="status-tab-btn {{ $activeTab === 'pending' ? 'active' : '' }} px-6 py-3 text-sm font-medium rounded-t-lg transition-all"
                        id="tab-pending">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⏳</span>
                        <span>Pending</span>
                        <span id="tab-pending-count" class="px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">{{ $pendingCount }}</span>
                    </div>
                </button>
                <button onclick="switchStatusTab('approved')" 
                        class="status-tab-btn {{ $activeTab === 'approved' ? 'active' : '' }} px-6 py-3 text-sm font-medium rounded-t-lg transition-all"
                        id="tab-approved">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">✅</span>
                        <span>Approved</span>
                        <span id="tab-approved-count" class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">{{ $approvedCount }}</span>
                    </div>
                </button>
                <button onclick="switchStatusTab('rejected')" 
                        class="status-tab-btn {{ $activeTab === 'rejected' ? 'active' : '' }} px-6 py-3 text-sm font-medium rounded-t-lg transition-all"
                        id="tab-rejected">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">❌</span>
                        <span>Rejected</span>
                        <span id="tab-rejected-count" class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $rejectedCount }}</span>
                    </div>
                </button>
            </div>
        </div>

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
        <div id="submission-ajax-container">
        @if($submissions->total() > 0)
            @php $submission = $submissions->first(); @endphp
            
            <!-- Admin Review Card -->
            <div class="bg-white rounded-2xl shadow-lg admin-review-card mb-6" data-id="{{ $submission->id }}">

                <!-- Gradient Header Band -->
                <div class="submission-header-band px-6 pt-5 pb-0
                    {{ $submission->status === 'approved' ? 'bg-gradient-to-135-approved' :
                       ($submission->status === 'rejected' ? 'bg-gradient-to-135-rejected' : 'bg-gradient-to-135-pending') }}">

                    <!-- Company info row -->
                    <div class="flex items-start justify-between pb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                                 style="background:rgba(255,255,255,0.18);backdrop-filter:blur(4px);">
                                🏭
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white leading-tight">{{ $submission->company_name }}</h3>
                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs" style="color:rgba(255,255,255,0.75)">
                                    <span class="flex items-center gap-1">👤 {{ $submission->respondent_name }}</span>
                                    <span class="flex items-center gap-1">💼 {{ $submission->position }}</span>
                                    <span class="flex items-center gap-1">📅 {{ $submission->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Status Badge -->
                        <span class="submission-status-pill flex-shrink-0
                            {{ $submission->status === 'approved' ? 'status-pill-approved' :
                               ($submission->status === 'rejected' ? 'status-pill-rejected' : 'status-pill-pending') }}">
                            {{ $submission->status === 'approved' ? '✓ Approved' :
                               ($submission->status === 'rejected' ? '✕ Rejected' : '⏳ Pending') }}
                        </span>
                    </div>

                    <!-- Tabs + Edit buttons row -->
                    <div class="flex items-end justify-between">
                        <!-- Tab buttons sit on the band, pop white when active -->
                        <div class="flex gap-1">
                            <button class="tab-btn active px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-all"
                                    onclick="switchTab(this, 'company-{{ $submission->id }}')">
                                <span class="tab-inner flex items-center gap-1.5">🏢 Company Profile</span>
                            </button>
                            <button class="tab-btn px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-all"
                                    onclick="switchTab(this, 'roles-{{ $submission->id }}')">
                                <span class="tab-inner flex items-center gap-1.5">
                                    ⚠️ Hard-to-Fill Roles
                                    <span class="tab-count-badge">{{ $submission->hardToFillRoles->count() }}</span>
                                </span>
                            </button>
                            <button class="tab-btn px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-all"
                                    onclick="switchTab(this, 'impact-{{ $submission->id }}')">
                                <span class="tab-inner flex items-center gap-1.5">🔍 Diagnosis of Mismatch</span>
                            </button>
                            <button class="tab-btn px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-all"
                                    onclick="switchTab(this, 'engagement-{{ $submission->id }}')">
                                <span class="tab-inner flex items-center gap-1.5">🤝 Engagement & Next Steps</span>
                            </button>
                        </div>
                        <!-- Edit/Save/Cancel buttons (right) — one group per tab -->
                        <div class="flex items-center gap-2 pb-2">
                            <div class="tab-edit-group tab-edit-company flex gap-2">
                                <button type="button" class="edit-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium" onclick="toggleEdit(this)">✏️ Edit</button>
                                <button type="submit" form="form-company-{{ $submission->id }}" class="save-btn hidden px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium">💾 Save</button>
                                <button type="button" class="cancel-btn hidden px-3 py-1.5 bg-slate-500 text-white rounded-lg hover:bg-slate-600 text-xs font-medium" onclick="cancelEdit(this)">✕ Cancel</button>
                            </div>
                            <div class="tab-edit-group tab-edit-roles hidden flex gap-2">
                                <button type="button" class="edit-roles-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium" onclick="toggleRolesEdit(this)">✏️ Edit</button>
                                <button type="submit" form="form-roles-{{ $submission->id }}" class="save-roles-btn hidden px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium">💾 Save</button>
                                <button type="button" class="cancel-roles-btn hidden px-3 py-1.5 bg-slate-500 text-white rounded-lg hover:bg-slate-600 text-xs font-medium" onclick="cancelRolesEdit(this)">✕ Cancel</button>
                            </div>
                            <div class="tab-edit-group tab-edit-impact hidden flex gap-2">
                                <button type="button" class="edit-diagnosis-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium" onclick="toggleDiagnosisEdit(this)">✏️ Edit</button>
                                <button type="submit" form="form-diagnosis-{{ $submission->id }}" class="save-diagnosis-btn hidden px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium">💾 Save</button>
                                <button type="button" class="cancel-diagnosis-btn hidden px-3 py-1.5 bg-slate-500 text-white rounded-lg hover:bg-slate-600 text-xs font-medium" onclick="cancelDiagnosisEdit(this)">✕ Cancel</button>
                            </div>
                            <div class="tab-edit-group tab-edit-engagement hidden flex gap-2">
                                <button type="button" class="edit-engagement-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium" onclick="toggleEngagementEdit(this)">✏️ Edit</button>
                                <button type="submit" form="form-engagement-{{ $submission->id }}" class="save-engagement-btn hidden px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium">💾 Save</button>
                                <button type="button" class="cancel-engagement-btn hidden px-3 py-1.5 bg-slate-500 text-white rounded-lg hover:bg-slate-600 text-xs font-medium" onclick="cancelEngagementEdit(this)">✕ Cancel</button>
                            </div>
                        </div>
                    </div>
                </div><!-- end .submission-header-band -->

                <!-- ↓ Scrollable tab body — header & action buttons stay fixed -->
                <div class="tab-scroll-body">

                    <!-- Company Profile Tab -->
<div class="tab-content active" id="company-{{ $submission->id }}">
    <form id="form-company-{{ $submission->id }}" action="{{ route('admin.lmi-submissions.update', $submission->id) }}" method="POST" class="edit-form" onsubmit="return handleFormSubmit(event, this, 'company')">
        @csrf
        @method('PUT')
        
        <table class="w-full">
            <thead class="bg-gradient-to-r from-slate-50 to-blue-50/40 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Field</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">🏭 Company Name</td>
                    <td class="px-6 py-4">
                        <input type="text" name="company_name" value="{{ $submission->company_name }}" 
                               data-original="{{ $submission->company_name }}"
                               data-label="Company Name"
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">👤 Respondent</td>
                    <td class="px-6 py-4">
                        <input type="text" name="respondent_name" value="{{ $submission->respondent_name }}" 
                               data-original="{{ $submission->respondent_name }}"
                               data-label="Respondent"
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">💼 Position</td>
                    <td class="px-6 py-4">
                        <input type="text" name="position" value="{{ $submission->position }}" 
                               data-original="{{ $submission->position }}"
                               data-label="Position"
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">📞 Contact Number</td>
                    <td class="px-6 py-4">
                        <input type="text" name="contact_number" value="{{ $submission->contact_number }}" 
                               data-original="{{ $submission->contact_number }}"
                               data-label="Contact Number"
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">✉️ Email</td>
                    <td class="px-6 py-4">
                        <input type="email" name="email" value="{{ $submission->email }}" 
                               data-original="{{ $submission->email }}"
                               data-label="Email"
                               class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">🏗️ Industry Sector</td>
                    <td class="px-6 py-4">
                        <select name="industry_sector" 
                                data-original="{{ $submission->industry_sector }}"
                                data-label="Industry Sector"
                                class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            <option value="Accommodation & Food Service" {{ $submission->industry_sector == 'Accommodation & Food Service' ? 'selected' : '' }}>Accommodation & Food Service</option>
                            <option value="Administrative & Support Services" {{ $submission->industry_sector == 'Administrative & Support Services' ? 'selected' : '' }}>Administrative & Support Services</option>
                            <option value="Agriculture, Forestry, Fishing & Mining" {{ $submission->industry_sector == 'Agriculture, Forestry, Fishing & Mining' ? 'selected' : '' }}>Agriculture, Forestry, Fishing & Mining</option>
                            <option value="Construction" {{ $submission->industry_sector == 'Construction' ? 'selected' : '' }}>Construction</option>
                            <option value="Education" {{ $submission->industry_sector == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Electricity, Gas, Water & Waste Management" {{ $submission->industry_sector == 'Electricity, Gas, Water & Waste Management' ? 'selected' : '' }}>Electricity, Gas, Water & Waste Management</option>
                            <option value="Financial & Insurance Activities" {{ $submission->industry_sector == 'Financial & Insurance Activities' ? 'selected' : '' }}>Financial & Insurance Activities</option>
                            <option value="Human Health & Social Work" {{ $submission->industry_sector == 'Human Health & Social Work' ? 'selected' : '' }}>Human Health & Social Work</option>
                            <option value="Information & Communication" {{ $submission->industry_sector == 'Information & Communication' ? 'selected' : '' }}>Information & Communication</option>
                            <option value="Manufacturing" {{ $submission->industry_sector == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="Other Service Activities" {{ $submission->industry_sector == 'Other Service Activities' ? 'selected' : '' }}>Other Service Activities</option>
                            <option value="Professional, Scientific & Technical Services" {{ $submission->industry_sector == 'Professional, Scientific & Technical Services' ? 'selected' : '' }}>Professional, Scientific & Technical Services</option>
                            <option value="Real Estate Activities" {{ $submission->industry_sector == 'Real Estate Activities' ? 'selected' : '' }}>Real Estate Activities</option>
                            <option value="Transportation, Storage & Logistics" {{ $submission->industry_sector == 'Transportation, Storage & Logistics' ? 'selected' : '' }}>Transportation, Storage & Logistics</option>
                            <option value="Wholesale & Retail Trade" {{ $submission->industry_sector == 'Wholesale & Retail Trade' ? 'selected' : '' }}>Wholesale & Retail Trade</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600 bg-slate-50/60">👥 Company Size</td>
                    <td class="px-6 py-4">
                        <select name="company_size" 
                                data-original="{{ $submission->company_size }}"
                                data-label="Company Size"
                                class="editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            <option value="Less than 50" {{ $submission->company_size == 'Less than 50' ? 'selected' : '' }}>Less than 50</option>
                            <option value="51-200" {{ $submission->company_size == '51-200' ? 'selected' : '' }}>51-200</option>
                            <option value="201-500" {{ $submission->company_size == '201-500' ? 'selected' : '' }}>201-500</option>
                            <option value="More than 500" {{ $submission->company_size == 'More than 500' ? 'selected' : '' }}>More than 500</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </form>
</div>

 <!-- Hard-to-Fill Roles Tab (Continues from previous) -->
<div class="tab-content" id="roles-{{ $submission->id }}">
    <div class="roles-tab-scroll">
    <form id="form-roles-{{ $submission->id }}" action="{{ route('admin.lmi-submissions.update-roles', $submission->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        @foreach($submission->hardToFillRoles as $index => $role)
            <div class="border-b border-slate-200 last:border-b-0 {{ $index > 0 ? 'mt-6 border-t-4 border-slate-300' : 'mt-4' }}">
                <div class="px-6 py-3 bg-blue-50 border-b border-blue-100">
                    <h4 class="font-bold text-slate-800">Job Entry #{{ $index + 1 }}</h4>
                </div>
                
                <input type="hidden" name="roles[{{ $index }}][id]" value="{{ $role->id }}">
                
                <table class="w-full">
                    <tbody class="divide-y">
                        <tr>
                            <td class="px-6 py-4 font-medium w-1/3">Job Title</td>
                            <td class="px-6 py-4">
                                <input type="text" name="roles[{{ $index }}][job_title]" 
                                       value="{{ $role->job_title }}" 
                                       class="role-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Job Classification</td>
                            <td class="px-6 py-4">
                                <select name="roles[{{ $index }}][job_classification]" 
                                        class="role-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                                    <option value="Accounting, Finance & Banking" {{ $role->job_classification == 'Accounting, Finance & Banking' ? 'selected' : '' }}>Accounting, Finance & Banking</option>
                                    <option value="Administrative, HR & Office Support" {{ $role->job_classification == 'Administrative, HR & Office Support' ? 'selected' : '' }}>Administrative, HR & Office Support</option>
                                    <option value="Agriculture, Forestry & Agribusiness" {{ $role->job_classification == 'Agriculture, Forestry & Agribusiness' ? 'selected' : '' }}>Agriculture, Forestry & Agribusiness</option>
                                    <option value="Construction, Engineering & Architecture" {{ $role->job_classification == 'Construction, Engineering & Architecture' ? 'selected' : '' }}>Construction, Engineering & Architecture</option>
                                    <option value="Customer Service & BPO (Contact Center)" {{ $role->job_classification == 'Customer Service & BPO (Contact Center)' ? 'selected' : '' }}>Customer Service & BPO (Contact Center)</option>
                                    <option value="Education, Training & Academe" {{ $role->job_classification == 'Education, Training & Academe' ? 'selected' : '' }}>Education, Training & Academe</option>
                                    <option value="Healthcare, Medical & Allied Services" {{ $role->job_classification == 'Healthcare, Medical & Allied Services' ? 'selected' : '' }}>Healthcare, Medical & Allied Services</option>
                                    <option value="IT, Software, Data & Digital Creative" {{ $role->job_classification == 'IT, Software, Data & Digital Creative' ? 'selected' : '' }}>IT, Software, Data & Digital Creative</option>
                                    <option value="Legal, Compliance & Public Service" {{ $role->job_classification == 'Legal, Compliance & Public Service' ? 'selected' : '' }}>Legal, Compliance & Public Service</option>
                                    <option value="Logistics, Transport & Supply Chain" {{ $role->job_classification == 'Logistics, Transport & Supply Chain' ? 'selected' : '' }}>Logistics, Transport & Supply Chain</option>
                                    <option value="Manufacturing, Production & Technical" {{ $role->job_classification == 'Manufacturing, Production & Technical' ? 'selected' : '' }}>Manufacturing, Production & Technical</option>
                                    <option value="Sales, Marketing, Retail & E-Commerce" {{ $role->job_classification == 'Sales, Marketing, Retail & E-Commerce' ? 'selected' : '' }}>Sales, Marketing, Retail & E-Commerce</option>
                                    <option value="Science, Research & Laboratory" {{ $role->job_classification == 'Science, Research & Laboratory' ? 'selected' : '' }}>Science, Research & Laboratory</option>
                                    <option value="Skilled Trades, Maintenance & General Services" {{ $role->job_classification == 'Skilled Trades, Maintenance & General Services' ? 'selected' : '' }}>Skilled Trades, Maintenance & General Services</option>
                                    <option value="Tourism, Hospitality & Food Service" {{ $role->job_classification == 'Tourism, Hospitality & Food Service' ? 'selected' : '' }}>Tourism, Hospitality & Food Service</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Salary Range</td>
                            <td class="px-6 py-4">
                                <input type="text" name="roles[{{ $index }}][salary_range]" 
                                       value="{{ $role->salary_range }}" 
                                       class="role-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Vacancy Duration</td>
                            <td class="px-6 py-4">
                                <select name="roles[{{ $index }}][vacancy_duration]" 
                                        class="role-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                                    <option value="Less than 30 Days" {{ $role->vacancy_duration == 'Less than 30 Days' ? 'selected' : '' }}>Less than 30 Days</option>
                                    <option value="30-60 Days" {{ $role->vacancy_duration == '30-60 Days' ? 'selected' : '' }}>30-60 Days</option>
                                    <option value="60-90 Days" {{ $role->vacancy_duration == '60-90 Days' ? 'selected' : '' }}>60-90 Days</option>
                                    <option value="90+ Days" {{ $role->vacancy_duration == '90+ Days' ? 'selected' : '' }}>90+ Days</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium align-top" colspan="2">
                                @php
                                    $difficultyReasons = is_array($role->difficulty_reasons) ? $role->difficulty_reasons : [];
                                    $techSkills = is_array($role->technical_skills_missing) ? $role->technical_skills_missing : (is_string($role->technical_skills_missing) && $role->technical_skills_missing ? array_map('trim', explode(',', $role->technical_skills_missing)) : []);
                                    $softSkills = is_array($role->soft_skills_missing) ? $role->soft_skills_missing : (is_string($role->soft_skills_missing) && $role->soft_skills_missing ? array_map('trim', explode(',', $role->soft_skills_missing)) : []);
                                    $hasTech = in_array('Technical / Hard Skills Missing', $difficultyReasons);
                                    $hasSoft = in_array('Soft / Employability Skills Missing', $difficultyReasons);
                                @endphp

                                <div class="px-0 py-2">
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Reasons For Difficulty (Role-Level) <span class="italic text-gray-500 text-xs">(Check all that apply)</span>
                                    </label>
                                    <div class="difficulty-reasons-ui-{{ $index }} space-y-3">

                                        {{-- Technical / Hard Skills --}}
                                        <label class="technical-skills-label-{{ $index }} flex items-start p-3 border rounded-lg cursor-pointer transition-all {{ $hasTech ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:bg-gray-50' }} role-editable-wrapper">
                                            <input type="checkbox"
                                                   name="roles[{{ $index }}][difficulty_reasons][]"
                                                   value="Technical / Hard Skills Missing"
                                                   {{ $hasTech ? 'checked' : '' }}
                                                   class="role-editable-field technical-checkbox-{{ $index }} mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                                   disabled>
                                            <div class="ml-3 flex-1">
                                                <div class="font-medium text-gray-700 text-sm">Technical / Hard Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                                                <div class="technical-details-{{ $index }} mt-3 {{ $hasTech ? '' : 'hidden' }}">
                                                    <label class="block text-gray-600 text-xs font-medium mb-1">What specific technical tools, software, or machinery knowledge is missing?</label>
                                                    <div class="technical-tags-container-{{ $index }} flex flex-wrap gap-2 mb-2">
                                                        @foreach($techSkills as $skill)
                                                            @if(trim($skill))
                                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm" data-tag="{{ trim($skill) }}">
                                                                <span>{{ trim($skill) }}</span>
                                                                <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <input type="text"
                                                               class="technical-skill-input-{{ $index }} flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                               placeholder="Type a skill and press Enter (e.g. Python, SQL, AutoCAD...)"
                                                               disabled>
                                                        <button type="button" class="add-technical-skill-{{ $index }} px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm" disabled>Enter</button>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each skill</p>
                                                    <input type="hidden"
                                                           class="technical-skills-input-{{ $index }}"
                                                           name="roles[{{ $index }}][technical_skills_missing]"
                                                           value="{{ implode(', ', array_filter(array_map('trim', $techSkills))) }}">
                                                </div>
                                            </div>
                                        </label>

                                        {{-- Soft / Employability Skills --}}
                                        <label class="soft-skills-label-{{ $index }} flex items-start p-3 border rounded-lg cursor-pointer transition-all {{ $hasSoft ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:bg-gray-50' }} role-editable-wrapper">
                                            <input type="checkbox"
                                                   name="roles[{{ $index }}][difficulty_reasons][]"
                                                   value="Soft / Employability Skills Missing"
                                                   {{ $hasSoft ? 'checked' : '' }}
                                                   class="role-editable-field soft-checkbox-{{ $index }} mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                                   disabled>
                                            <div class="ml-3 flex-1">
                                                <div class="font-medium text-gray-700 text-sm">Soft / Employability Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                                                <div class="soft-details-{{ $index }} mt-3 {{ $hasSoft ? '' : 'hidden' }}">
                                                    <label class="block text-gray-600 text-xs font-medium mb-1">What attitude or behavioral traits cause you to reject applicants?</label>
                                                    <div class="soft-tags-container-{{ $index }} flex flex-wrap gap-2 mb-2">
                                                        @foreach($softSkills as $skill)
                                                            @if(trim($skill))
                                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm" data-tag="{{ trim($skill) }}">
                                                                <span>{{ trim($skill) }}</span>
                                                                <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <input type="text"
                                                               class="soft-skill-input-{{ $index }} flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                               placeholder="Type a trait and press Enter (e.g. Poor communication, Unprofessional...)"
                                                               disabled>
                                                        <button type="button" class="add-soft-skill-{{ $index }} px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm" disabled>Enter</button>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each trait</p>
                                                    <input type="hidden"
                                                           class="soft-skills-input-{{ $index }}"
                                                           name="roles[{{ $index }}][soft_skills_missing]"
                                                           value="{{ implode(', ', array_filter(array_map('trim', $softSkills))) }}">
                                                </div>
                                            </div>
                                        </label>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Impact Level for this role -->
                        @php
                            $diagnosis = $submission->diagnoses->where('lmi_hard_to_fill_role_id', $role->id)->first();
                        @endphp
                        @if($diagnosis)
                            <tr>
                                <td class="px-6 py-4 font-medium">Impact Level</td>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="roles[{{ $index }}][diagnosis_id]" value="{{ $diagnosis->id }}">
                                    <select name="roles[{{ $index }}][impact_level]" 
                                            class="role-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                                        <option value="High" {{ $diagnosis->impact_level == 'High' ? 'selected' : '' }}>High Impact</option>
                                        <option value="Medium" {{ $diagnosis->impact_level == 'Medium' ? 'selected' : '' }}>Medium Impact</option>
                                        <option value="Low" {{ $diagnosis->impact_level == 'Low' ? 'selected' : '' }}>Low Impact</option>
                                    </select>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach
        
    </form>
    </div><!-- end .roles-tab-scroll -->
</div>

<!-- Diagnosis Tab -->
<div class="tab-content" id="impact-{{ $submission->id }}">
    @php
        $firstDiagnosis = $submission->diagnoses->first();
    @endphp
    
    @if($firstDiagnosis)
        <form id="form-diagnosis-{{ $submission->id }}" action="{{ route('admin.lmi-submissions.update-diagnosis', $submission->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="diagnosis_id" value="{{ $firstDiagnosis->id }}">
            
            <table class="w-full">
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4 font-medium align-top w-1/3">Common Rejection Reasons</td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                @php
                                    $rejectionReasons = is_array($firstDiagnosis->rejection_reasons) ? $firstDiagnosis->rejection_reasons : [];
                                @endphp
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="rejection_reasons[]" value="Lack of practical / hands-on experience" 
                                           {{ in_array('Lack of practical / hands-on experience', $rejectionReasons) ? 'checked' : '' }}
                                           class="diagnosis-editable-field" disabled>
                                    <span class="text-sm">Lack of practical / hands-on experience</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="rejection_reasons[]" value="Skills are outdated" 
                                           {{ in_array('Skills are outdated', $rejectionReasons) ? 'checked' : '' }}
                                           class="diagnosis-editable-field" disabled>
                                    <span class="text-sm">Skills are outdated</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="rejection_reasons[]" value="Poor communication skills" 
                                           {{ in_array('Poor communication skills', $rejectionReasons) ? 'checked' : '' }}
                                           class="diagnosis-editable-field" disabled>
                                    <span class="text-sm">Poor communication skills</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="rejection_reasons[]" value="Low job readiness / poor interview performance" 
                                           {{ in_array('Low job readiness / poor interview performance', $rejectionReasons) ? 'checked' : '' }}
                                           class="diagnosis-editable-field" disabled>
                                    <span class="text-sm">Low job readiness / poor interview performance</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="rejection_reasons[]" value="Other" 
                                           {{ in_array('Other', $rejectionReasons) ? 'checked' : '' }}
                                           class="diagnosis-editable-field" disabled>
                                    <span class="text-sm">Other (please specify)</span>
                                </label>
                                
                                @if($firstDiagnosis->rejection_reasons_other)
                                    <div class="ml-6 mt-2">
                                        <input type="text" name="rejection_reasons_other" 
                                               value="{{ $firstDiagnosis->rejection_reasons_other }}" 
                                               class="diagnosis-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" 
                                               placeholder="Please specify" disabled>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-medium">Coordination with Schools</td>
                        <td class="px-6 py-4">
                            <input type="text" name="coordination_frequency" 
                                   value="{{ $firstDiagnosis->coordination_frequency }}" 
                                   class="diagnosis-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" disabled>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </form>
    @else
        <div class="px-6 py-8 text-center text-slate-500">
            No diagnosis data available
        </div>
    @endif
</div>

<!-- Engagement Tab -->
<div class="tab-content" id="engagement-{{ $submission->id }}">
    @if($submission->engagement)
        <form id="form-engagement-{{ $submission->id }}" action="{{ route('admin.lmi-submissions.update-engagement', $submission->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="engagement_id" value="{{ $submission->engagement->id }}">
            
            <table class="w-full">
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4 font-medium align-top w-1/3">LMI Features Interested In</td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                @php
                                    $lmiFeatures = is_array($submission->engagement->lmi_features) ? $submission->engagement->lmi_features : [];
                                    // Detect if an "Other: ..." value exists
                                    $lmiOtherValue = '';
                                    foreach ($lmiFeatures as $f) {
                                        if (str_starts_with($f, 'Other: ')) {
                                            $lmiOtherValue = substr($f, 7);
                                            break;
                                        }
                                    }
                                    $hasOther = in_array('Other', $lmiFeatures) || !empty($lmiOtherValue);
                                @endphp
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="lmi_features[]" value="Viewing the supply of graduates" 
                                           {{ in_array('Viewing the supply of graduates', $lmiFeatures) ? 'checked' : '' }}
                                           class="engagement-editable-field" disabled>
                                    <span class="text-sm">Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="lmi_features[]" value="A channel to submit real-time feedback" 
                                           {{ in_array('A channel to submit real-time feedback', $lmiFeatures) ? 'checked' : '' }}
                                           class="engagement-editable-field" disabled>
                                    <span class="text-sm">A channel to submit real-time feedback on curriculum quality</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="lmi_features[]" value="A directory of job placement offices" 
                                           {{ in_array('A directory of job placement offices', $lmiFeatures) ? 'checked' : '' }}
                                           class="engagement-editable-field" disabled>
                                    <span class="text-sm">A directory of job placement offices and Public Employment offices (PESOs)</span>
                                </label>
                                <div>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="lmi_features[]" value="Other" 
                                               {{ $hasOther ? 'checked' : '' }}
                                               class="engagement-editable-field admin-lmi-other-checkbox" disabled>
                                        <span class="text-sm">Other (please specify)</span>
                                    </label>
                                    <div class="admin-lmi-other-input ml-6 mt-1 {{ $hasOther ? '' : 'hidden' }}">
                                        <input type="text" name="lmi_features_other"
                                               value="{{ $lmiOtherValue }}"
                                               class="engagement-editable-field w-full border border-slate-300 rounded px-3 py-1.5 text-sm"
                                               placeholder="Please specify" disabled>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-medium align-top">Specific Inputs Needed</td>
                        <td class="px-6 py-4">
                            <textarea name="specific_inputs" rows="4" 
                                      class="engagement-editable-field w-full border border-slate-300 rounded px-3 py-2 text-sm" 
                                      disabled>{{ $submission->engagement->specific_inputs }}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </form>
    @else
        <div class="px-6 py-8 text-center text-slate-500">
            No engagement data available
        </div>
    @endif
</div>

                </div><!-- end .tab-scroll-body -->

                <!-- Action Buttons (only for pending submissions) -->
                @if($submission->status === 'pending')
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-blue-50/30 border-t border-slate-200 flex justify-end gap-3">
                        <button 
                            type="button"
                            onclick="showRejectModal({{ $submission->id }}, '{{ $submission->company_name }}')"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold flex items-center gap-2 shadow-sm hover:shadow-md transition-all text-sm">
                            ✕ Reject
                        </button>
                        <button 
                            type="button"
                            onclick="showApproveModal({{ $submission->id }}, '{{ $submission->company_name }}')"
                            class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-semibold flex items-center gap-2 shadow-sm hover:shadow-md transition-all text-sm">
                            ✓ Approve
                        </button>
                    </div>
                @elseif($submission->status === 'rejected')
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-orange-50/30 border-t border-slate-200 flex justify-end gap-3">
                        <button 
                            type="button"
                            onclick="showRestorePendingModal({{ $submission->id }}, '{{ $submission->company_name }}')"
                            class="px-5 py-2.5 bg-orange-600 text-white rounded-xl hover:bg-orange-700 font-semibold flex items-center gap-2 shadow-sm hover:shadow-md transition-all text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Restore to Pending
                        </button>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6 text-sm">
                <div class="text-slate-600">
                    Showing submission <strong>{{ $submissions->firstItem() }}</strong> of <strong>{{ $submissions->total() }}</strong>
                </div>
                <div class="flex gap-2">
                    @if ($submissions->onFirstPage())
                        <button disabled class="px-3 py-1 bg-slate-200 text-slate-400 rounded cursor-not-allowed">« First</button>
                        <button disabled class="px-3 py-1 bg-slate-200 text-slate-400 rounded cursor-not-allowed">‹ Prev</button>
                    @else
                        <a href="{{ $submissions->url(1) }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">« First</a>
                        <a href="{{ $submissions->previousPageUrl() }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">‹ Prev</a>
                    @endif

                    @foreach(range(1, min(5, $submissions->lastPage())) as $i)
                        @if($i == $submissions->currentPage())
                            <span class="px-3 py-1 bg-blue-600 text-white rounded font-bold">{{ $i }}</span>
                        @else
                            <a href="{{ $submissions->url($i) }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">{{ $i }}</a>
                        @endif
                    @endforeach

                    @if($submissions->lastPage() > 5)
                        <span class="px-3 py-1">...</span>
                        <a href="{{ $submissions->url($submissions->lastPage()) }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">{{ $submissions->lastPage() }}</a>
                    @endif

                    @if ($submissions->hasMorePages())
                        <a href="{{ $submissions->nextPageUrl() }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">Next ›</a>
                        <a href="{{ $submissions->url($submissions->lastPage()) }}" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50">Last »</a>
                    @else
                        <button disabled class="px-3 py-1 bg-slate-200 text-slate-400 rounded cursor-not-allowed">Next ›</button>
                        <button disabled class="px-3 py-1 bg-slate-200 text-slate-400 rounded cursor-not-allowed">Last »</button>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">No {{ ucfirst($activeTab) }} Submissions</h3>
                <p class="text-slate-600">There are currently no submissions with "{{ $activeTab }}" status.</p>
            </div>
        @endif
        </div><!-- end #submission-ajax-container -->
    </div>
</main>
    </div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-[slideIn_0.3s_ease-out]">
        <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white">Approve Submission?</h3>
        </div>
        
        <div class="p-6">
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700 font-medium mb-1">Company</p>
                <p class="text-lg font-bold text-green-900" id="approveCompanyName"></p>
            </div>
            
            <p class="text-gray-600 mb-6 text-center">
                Are you sure you want to approve this submission? The company will be notified and the data will be marked as approved.
            </p>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="closeApproveModal()"
                    class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-all">
                    Cancel
                </button>
                <button 
                    type="button"
                    onclick="confirmApprove()"
                    class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">
                    Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-[slideIn_0.3s_ease-out]">
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white">Reject Submission?</h3>
        </div>
        
        <div class="p-6">
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-700 font-medium mb-1">Company</p>
                <p class="text-lg font-bold text-red-900" id="rejectCompanyName"></p>
            </div>
            
            <p class="text-gray-600 mb-6 text-center">
                Are you sure you want to reject this submission? The submission will be moved to the rejected list.
            </p>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="closeRejectModal()"
                    class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-all">
                    Cancel
                </button>
                <button 
                    type="button"
                    onclick="confirmReject()"
                    class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">
                    Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-[slideIn_0.3s_ease-out]">
        <div id="successModalHeader" class="p-8 text-center">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4" id="successIcon">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-bold mb-2" id="successTitle">Success!</h3>
            <p id="successMessage" class="text-lg"></p>
        </div>
        
        <div class="p-6 text-center">
            <button 
                type="button"
                onclick="closeSuccessModal()"
                id="successButton"
                class="px-8 py-3 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full">
                Continue
            </button>
        </div>
    </div>
</div>

<!-- Edit Changes Confirmation Modal -->
<div id="editChangesModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden animate-[slideIn_0.3s_ease-out]">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-white">Confirm Changes</h3>
                    <p class="text-blue-100 text-sm">Review the changes before saving</p>
                </div>
            </div>
        </div>

        <div class="p-6 max-h-[60vh] overflow-y-auto">
            <div class="mb-4">
                <h4 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Changes Summary
                </h4>
                <div id="changesList" class="space-y-3">
                    <!-- Changes will be inserted here -->
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mt-4">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-800 mb-1">Confirm Update</p>
                        <p class="text-sm text-yellow-700">These changes will be saved to the database immediately.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
            <button 
                type="button"
                onclick="closeEditChangesModal()"
                class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-all">
                Cancel
            </button>
            <button 
                type="button"
                onclick="confirmEditChanges()"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Confirm & Save
            </button>
        </div>
    </div>
</div>

<!-- Restore to Pending Modal with Text Verification -->
<div id="restorePendingModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-[slideIn_0.3s_ease-out]">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white">Restore to Pending?</h3>
        </div>
        
        <div class="p-6">
            <div class="mb-4 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-sm text-orange-700 font-medium mb-1">Company</p>
                <p class="text-lg font-bold text-orange-900" id="restoreCompanyName"></p>
            </div>
            
            <p class="text-gray-600 mb-4 text-center">
                This will move the submission back to the pending queue for review. This action requires confirmation.
            </p>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800 mb-2">Security Verification Required</p>
                        <p class="text-sm text-red-700 mb-3">Type <code class="bg-red-100 px-2 py-1 rounded font-mono font-bold">CONFIRM</code> to proceed:</p>
                        <input 
                            type="text" 
                            id="restoreConfirmText"
                            placeholder="Type CONFIRM here"
                            class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            oninput="validateRestoreInput()">
                        <p id="restoreError" class="text-sm text-red-600 mt-2 hidden">Please type CONFIRM exactly as shown</p>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="closeRestorePendingModal()"
                    class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-all">
                    Cancel
                </button>
                <button 
                    type="button"
                    id="restoreConfirmButton"
                    onclick="confirmRestorePending()"
                    disabled
                    class="flex-1 px-4 py-3 bg-orange-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    Restore to Pending
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Submission -->
<form id="approveForm" method="POST" class="hidden">
    @csrf
</form>

<form id="rejectForm" method="POST" class="hidden">
    @csrf
</form>

<form id="restorePendingForm" method="POST" class="hidden">
    @csrf
</form>

<style>
/* ─── Scrollable Tab Body ─── */
/* Default: no fixed height — card grows naturally for all tabs */
.tab-scroll-body {
    overflow: visible;
}

/* Only the Hard-to-Fill Roles tab gets internal scroll */
.roles-tab-scroll {
    max-height: 520px;
    overflow-y: auto;
    overflow-x: visible; /* allow dropdowns to overflow outside scroll container */
}

/* Give enough bottom padding so dropdowns open downward, not upward */
.roles-tab-scroll form {
    padding-bottom: 160px;
}

/* Compensate — trim that padding when not in edit mode */
.roles-tab-scroll:not(.is-editing) form {
    padding-bottom: 8px;
}

/* Custom thin scrollbar — Chrome/Edge/Safari */
.roles-tab-scroll::-webkit-scrollbar {
    width: 5px;
}
.roles-tab-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.roles-tab-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 99px;
}
.roles-tab-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ─── Gradient Header Bands ─── */
.submission-header-band {
    background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
    border-radius: 16px 16px 0 0;
}
.admin-review-card {
    border-radius: 16px;
    overflow: hidden;
}
.bg-gradient-to-135-pending  { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); }
.bg-gradient-to-135-approved { background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); }
.bg-gradient-to-135-rejected { background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%); }

/* ─── Status Pills ─── */
.submission-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 5px 13px;
    border-radius: 99px;
}
.status-pill-pending  { background: rgba(255,255,255,0.22); color: #fff; border: 1px solid rgba(255,255,255,0.35); }
.status-pill-approved { background: #22c55e; color: #fff; box-shadow: 0 2px 8px rgba(34,197,94,.45); }
.status-pill-rejected { background: #ef4444; color: #fff; box-shadow: 0 2px 8px rgba(239,68,68,.45); }

/* ─── Content Tabs (sit on gradient band, pop white when active) ─── */
.tab-btn {
    color: rgba(255,255,255,0.65);
    background: transparent;
    border: none;
    transition: all 0.18s;
    position: relative;
    bottom: 0;
}

.tab-btn:hover:not(.active) {
    color: rgba(255,255,255,0.9);
    background: rgba(255,255,255,0.1);
}

.tab-btn.active {
    color: #1e40af;
    background: #ffffff;
    box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
}

.tab-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 99px;
    background: #ef4444;
    color: #fff;
    line-height: 1;
}

.tab-btn.active .tab-count-badge {
    background: #2563eb;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* ─── Status Tab Styles (top of page) ─── */
.status-tab-btn {
    color: #64748b;
    background: transparent;
    border-bottom: 3px solid transparent;
}

.status-tab-btn.active {
    color: #1e40af;
    background: #eff6ff;
    border-bottom-color: #2563eb;
}

.status-tab-btn:hover:not(.active) {
    background: #f8fafc;
}

/* ─── Field rows — card style ─── */
.admin-review-card table tbody tr {
    transition: background 0.12s;
}
.admin-review-card table tbody tr:hover {
    background: #f8fafc;
}

/* ─── Edit mode styles ─── */
.editable-field:disabled,
.role-editable-field:disabled,
.diagnosis-editable-field:disabled,
.engagement-editable-field:disabled {
    background-color: #f9fafb;
    cursor: not-allowed;
}

.editable-field:not(:disabled),
.role-editable-field:not(:disabled),
.diagnosis-editable-field:not(:disabled),
.engagement-editable-field:not(:disabled) {
    background-color: white;
    border-color: #3b82f6;
}

/* ─── Modal Animation ─── */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// ─── Toast Notification System ──────────────────────────────────────────
function showToast(message, type = 'error', onExpire = null) {
    const container = document.getElementById('toastContainer');

    const configs = {
        error: {
            bg: 'bg-red-50 border-red-400',
            icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-red-800',
            bar: 'bg-red-400',
        },
        warning: {
            bg: 'bg-amber-50 border-amber-400',
            icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                   </svg>`,
            text: 'text-amber-800',
            bar: 'bg-amber-400',
        },
        success: {
            bg: 'bg-green-50 border-green-400',
            icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                   </svg>`,
            text: 'text-green-800',
            bar: 'bg-green-400',
        },
        info: {
            bg: 'bg-blue-50 border-blue-400',
            icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-blue-800',
            bar: 'bg-blue-400',
        },
    };

    const c = configs[type] || configs.error;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
                       transform transition-all duration-300 translate-x-full opacity-0`;

    toast.innerHTML = `
        <div class="flex items-start gap-3 px-4 py-4">
            ${c.icon}
            <p class="text-sm font-medium ${c.text} flex-1 leading-snug">${message}</p>
            <button onclick="this.closest('.pointer-events-auto').remove()"
                    class="text-gray-400 hover:text-gray-600 transition ml-1 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="h-1 ${c.bar} animate-shrink" style="animation: shrink 4s linear forwards;"></div>
    `;

    container.appendChild(toast);

    // Slide in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
    });

    // Auto-remove after 4s, then fire onExpire callback if provided
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
            if (typeof onExpire === 'function') onExpire();
        }, 300);
    }, 4000);
}

// CSS for the shrink progress bar
if (!document.getElementById('toastStyle')) {
    const style = document.createElement('style');
    style.id = 'toastStyle';
    style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);
}

// Status tab switching
function switchStatusTab(status) {
    window.location.href = "{{ route('admin.lmi-submissions.index') }}?status=" + status;
}

// Content tab switching
function switchTab(button, tabId) {
    const card = button.closest('.admin-review-card');

    // Update tab button styles
    card.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-slate-600');
    });
    button.classList.add('active', 'text-blue-600', 'border-blue-600');
    button.classList.remove('text-slate-600');

    // Update tab content panels
    card.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    // Show the matching top-right edit group, hide the rest
    // tabId is like "company-5", "roles-5", "impact-5", "engagement-5"
    // Strip the numeric submission id to get the key e.g. "company", "roles", "impact"
    const parts = tabId.split('-');
    parts.pop(); // remove trailing id number
    const tabKey = parts.join('-');
    card.querySelectorAll('.tab-edit-group').forEach(g => g.classList.add('hidden'));
    const activeGroup = card.querySelector('.tab-edit-' + tabKey);
    if (activeGroup) activeGroup.classList.remove('hidden');
}

// Helpers — buttons now live in the tab bar (outside <form>)
function _getCard(btn) { return btn.closest('.admin-review-card'); }
function _getForm(btn, prefix) { return _getCard(btn).querySelector('[id^="' + prefix + '"]'); }

// Company Profile Edit
function toggleEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-company-');
    form.querySelectorAll('.editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-btn').classList.add('hidden');
    card.querySelector('.save-btn').classList.remove('hidden');
    card.querySelector('.cancel-btn').classList.remove('hidden');
}

function cancelEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-company-');
    form.querySelectorAll('.editable-field').forEach(f => f.disabled = true);
    form.reset();
    card.querySelector('.edit-btn').classList.remove('hidden');
    card.querySelector('.save-btn').classList.add('hidden');
    card.querySelector('.cancel-btn').classList.add('hidden');
}

// Roles Edit
// ─── SKILL TAG SYSTEM FOR ADMIN ROLES ──────────────────────────────────────

function initSkillTagSystem(container, addButton, textInput, hiddenInput, tagsContainer) {
    // Read existing tags from already-rendered spans
    const tags = [];
    tagsContainer.querySelectorAll('[data-tag]').forEach(el => {
        const val = el.getAttribute('data-tag');
        if (val) tags.push(val);
    });

    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach((tag, i) => {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
            span.setAttribute('data-tag', tag);
            span.innerHTML = `<span>${tag}</span>
                <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5" data-index="${i}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>`;
            tagsContainer.appendChild(span);
        });
        hiddenInput.value = tags.join(', ');
        // Re-bind remove buttons
        tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
            btn.addEventListener('click', e => {
                const idx = parseInt(e.currentTarget.getAttribute('data-index'));
                tags.splice(idx, 1);
                renderTags();
            });
        });
    }

    function addTag() {
        const val = textInput.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) {
            tags.push(val);
            textInput.value = '';
            renderTags();
        }
    }

    addButton.addEventListener('click', addTag);
    textInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag();
        }
    });

    // Initial render to bind remove buttons on existing tags
    renderTags();
}

function initRolesTagSystems(form) {
    // Find all role index wrappers by looking for indexed containers
    // We look for any element with a class matching the pattern
    const allClasses = [...form.querySelectorAll('[class]')]
        .flatMap(el => [...el.classList]);
    const indices = new Set(
        allClasses
            .map(c => c.match(/^technical-tags-container-(\d+)$/))
            .filter(Boolean)
            .map(m => m[1])
    );

    indices.forEach(i => {
        // Technical
        const techTagsContainer = form.querySelector(`.technical-tags-container-${i}`);
        const techTextInput     = form.querySelector(`.technical-skill-input-${i}`);
        const techAddBtn        = form.querySelector(`.add-technical-skill-${i}`);
        const techHidden        = form.querySelector(`.technical-skills-input-${i}`);
        const techCheckbox      = form.querySelector(`.technical-checkbox-${i}`);
        const techDetails       = form.querySelector(`.technical-details-${i}`);

        if (techCheckbox && techDetails) {
            techCheckbox.addEventListener('change', () => {
                const label = techCheckbox.closest('label');
                if (techCheckbox.checked) {
                    techDetails.classList.remove('hidden');
                    label.classList.add('border-teal-500', 'bg-teal-50');
                    label.classList.remove('border-gray-200', 'hover:bg-gray-50');
                } else {
                    techDetails.classList.add('hidden');
                    label.classList.remove('border-teal-500', 'bg-teal-50');
                    label.classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });
        }

        if (techTagsContainer && techTextInput && techAddBtn && techHidden) {
            initSkillTagSystem(null, techAddBtn, techTextInput, techHidden, techTagsContainer);
        }

        // Soft
        const softTagsContainer = form.querySelector(`.soft-tags-container-${i}`);
        const softTextInput     = form.querySelector(`.soft-skill-input-${i}`);
        const softAddBtn        = form.querySelector(`.add-soft-skill-${i}`);
        const softHidden        = form.querySelector(`.soft-skills-input-${i}`);
        const softCheckbox      = form.querySelector(`.soft-checkbox-${i}`);
        const softDetails       = form.querySelector(`.soft-details-${i}`);

        if (softCheckbox && softDetails) {
            softCheckbox.addEventListener('change', () => {
                const label = softCheckbox.closest('label');
                if (softCheckbox.checked) {
                    softDetails.classList.remove('hidden');
                    label.classList.add('border-teal-500', 'bg-teal-50');
                    label.classList.remove('border-gray-200', 'hover:bg-gray-50');
                } else {
                    softDetails.classList.add('hidden');
                    label.classList.remove('border-teal-500', 'bg-teal-50');
                    label.classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });
        }

        if (softTagsContainer && softTextInput && softAddBtn && softHidden) {
            initSkillTagSystem(null, softAddBtn, softTextInput, softHidden, softTagsContainer);
        }
    });
}

// Track whether tag systems have been initialised per form
const _rolesTagInited = new WeakSet();

function toggleRolesEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-roles-');
    form.querySelectorAll('.role-editable-field').forEach(f => f.disabled = false);
    form.querySelectorAll('[class*="technical-skill-input-"], [class*="soft-skill-input-"]').forEach(el => el.disabled = false);
    form.querySelectorAll('[class*="add-technical-skill-"], [class*="add-soft-skill-"]').forEach(el => el.disabled = false);

    if (!_rolesTagInited.has(form)) {
        initRolesTagSystems(form);
        if (typeof initLmiAutocompletes === 'function') initLmiAutocompletes(form);
        _rolesTagInited.add(form);
    }

    card.querySelector('.edit-roles-btn').classList.add('hidden');
    card.querySelector('.save-roles-btn').classList.remove('hidden');
    card.querySelector('.cancel-roles-btn').classList.remove('hidden');
}

function cancelRolesEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-roles-');
    form.querySelectorAll('.role-editable-field').forEach(f => f.disabled = true);
    form.querySelectorAll('[class*="technical-skill-input-"], [class*="soft-skill-input-"]').forEach(el => el.disabled = true);
    form.querySelectorAll('[class*="add-technical-skill-"], [class*="add-soft-skill-"]').forEach(el => el.disabled = true);
    form.reset();

    card.querySelector('.edit-roles-btn').classList.remove('hidden');
    card.querySelector('.save-roles-btn').classList.add('hidden');
    card.querySelector('.cancel-roles-btn').classList.add('hidden');
}

// Diagnosis Edit
function toggleDiagnosisEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-diagnosis-');
    form.querySelectorAll('.diagnosis-editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-diagnosis-btn').classList.add('hidden');
    card.querySelector('.save-diagnosis-btn').classList.remove('hidden');
    card.querySelector('.cancel-diagnosis-btn').classList.remove('hidden');
}

function cancelDiagnosisEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-diagnosis-');
    form.querySelectorAll('.diagnosis-editable-field').forEach(f => f.disabled = true);
    form.reset();
    card.querySelector('.edit-diagnosis-btn').classList.remove('hidden');
    card.querySelector('.save-diagnosis-btn').classList.add('hidden');
    card.querySelector('.cancel-diagnosis-btn').classList.add('hidden');
}

// Engagement Edit
function toggleEngagementEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-engagement-');
    form.querySelectorAll('.engagement-editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-engagement-btn').classList.add('hidden');
    card.querySelector('.save-engagement-btn').classList.remove('hidden');
    card.querySelector('.cancel-engagement-btn').classList.remove('hidden');

    const otherCheckbox = form.querySelector('.admin-lmi-other-checkbox');
    const otherInput = form.querySelector('.admin-lmi-other-input');
    if (otherCheckbox && otherInput) {
        otherCheckbox.addEventListener('change', function () {
            this.checked ? otherInput.classList.remove('hidden') : otherInput.classList.add('hidden');
        });
    }
}

function cancelEngagementEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-engagement-');
    form.querySelectorAll('.engagement-editable-field').forEach(f => f.disabled = true);
    form.reset();

    const otherCheckbox = form.querySelector('.admin-lmi-other-checkbox');
    const otherInput = form.querySelector('.admin-lmi-other-input');
    if (otherCheckbox && otherInput) {
        otherCheckbox.checked ? otherInput.classList.remove('hidden') : otherInput.classList.add('hidden');
    }

    card.querySelector('.edit-engagement-btn').classList.remove('hidden');
    card.querySelector('.save-engagement-btn').classList.add('hidden');
    card.querySelector('.cancel-engagement-btn').classList.add('hidden');
}

// Modal Management Functions
let currentSubmissionId = null;
let currentForm = null;
let detectedChanges = [];

// Form Submit Handler with Change Detection
function handleFormSubmit(event, form, formType) {
    event.preventDefault();
    
    // Detect changes
    detectedChanges = [];
    const fields = form.querySelectorAll('[data-original]');
    
    fields.forEach(field => {
        const originalValue = field.getAttribute('data-original');
        const currentValue = field.value;
        const label = field.getAttribute('data-label');
        
        if (originalValue !== currentValue) {
            detectedChanges.push({
                label: label,
                old: originalValue,
                new: currentValue
            });
        }
    });
    
    // If no changes detected — show toast, then auto-cancel edit mode after timer
    if (detectedChanges.length === 0) {
        // Figure out which cancel function to call based on the form id
        const formId = form.id; // e.g. "form-company-5"
        let cancelFn = null;
        if (formId.startsWith('form-company-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-btn');
            cancelFn = () => cancelBtn && cancelEdit(cancelBtn);
        } else if (formId.startsWith('form-roles-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-roles-btn');
            cancelFn = () => cancelBtn && cancelRolesEdit(cancelBtn);
        } else if (formId.startsWith('form-diagnosis-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-diagnosis-btn');
            cancelFn = () => cancelBtn && cancelDiagnosisEdit(cancelBtn);
        } else if (formId.startsWith('form-engagement-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-engagement-btn');
            cancelFn = () => cancelBtn && cancelEngagementEdit(cancelBtn);
        }
        showToast('No changes detected. Exiting edit mode...', 'info', cancelFn);
        return false;
    }
    
    // Show confirmation modal with changes
    currentForm = form;
    showEditChangesModal(detectedChanges, formType);
    return false;
}

function showEditChangesModal(changes, formType) {
    const changesList = document.getElementById('changesList');
    changesList.innerHTML = '';
    
    changes.forEach(change => {
        const changeItem = document.createElement('div');
        changeItem.className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg';
        changeItem.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 mb-1">${change.label}</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-white p-2 rounded border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">From:</p>
                            <p class="text-gray-700 font-medium">${change.old || '(empty)'}</p>
                        </div>
                        <div class="bg-white p-2 rounded border border-blue-300">
                            <p class="text-xs text-blue-600 mb-1">To:</p>
                            <p class="text-blue-700 font-bold">${change.new || '(empty)'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        changesList.appendChild(changeItem);
    });
    
    document.getElementById('editChangesModal').classList.remove('hidden');
}

function closeEditChangesModal() {
    document.getElementById('editChangesModal').classList.add('hidden');
    currentForm = null;
    detectedChanges = [];
}

function confirmEditChanges() {
    if (currentForm) {
        closeEditChangesModal();
        // Actually submit the form
        currentForm.submit();
    }
}

function showApproveModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('approveCompanyName').textContent = companyName;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    currentSubmissionId = null;
}

function confirmApprove() {
    if (currentSubmissionId) {
        const form = document.getElementById('approveForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/approve`;
        
        // Close the approve modal
        closeApproveModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('approved', 'Submission has been approved successfully!');
    }
}

function showRejectModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('rejectCompanyName').textContent = companyName;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentSubmissionId = null;
}

function confirmReject() {
    if (currentSubmissionId) {
        const form = document.getElementById('rejectForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/reject`;
        
        // Close the reject modal
        closeRejectModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('rejected', 'Submission has been rejected.');
    }
}

function showRestorePendingModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('restoreCompanyName').textContent = companyName;
    document.getElementById('restoreConfirmText').value = '';
    document.getElementById('restoreConfirmButton').disabled = true;
    document.getElementById('restoreError').classList.add('hidden');
    document.getElementById('restorePendingModal').classList.remove('hidden');
}

function closeRestorePendingModal() {
    document.getElementById('restorePendingModal').classList.add('hidden');
    currentSubmissionId = null;
}

function validateRestoreInput() {
    const input = document.getElementById('restoreConfirmText');
    const button = document.getElementById('restoreConfirmButton');
    const error = document.getElementById('restoreError');
    
    if (input.value === 'CONFIRM') {
        button.disabled = false;
        error.classList.add('hidden');
        input.classList.remove('border-red-300');
        input.classList.add('border-green-500');
    } else {
        button.disabled = true;
        if (input.value.length > 0) {
            error.classList.remove('hidden');
            input.classList.remove('border-green-500');
            input.classList.add('border-red-300');
        } else {
            error.classList.add('hidden');
            input.classList.remove('border-green-500', 'border-red-300');
        }
    }
}

function confirmRestorePending() {
    const input = document.getElementById('restoreConfirmText');
    
    if (input.value === 'CONFIRM' && currentSubmissionId) {
        const form = document.getElementById('restorePendingForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/restore-pending`;
        
        // Close the restore modal
        closeRestorePendingModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('restored', 'Submission has been restored to pending status.');
    }
}

function showSuccessModal(action, message) {
    const modal = document.getElementById('successModal');
    const header = document.getElementById('successModalHeader');
    const icon = document.getElementById('successIcon');
    const title = document.getElementById('successTitle');
    const messageEl = document.getElementById('successMessage');
    const button = document.getElementById('successButton');
    
    if (action === 'approved') {
        header.className = 'bg-gradient-to-r from-green-500 to-green-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-green-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-green-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-green-100';
        button.className = 'px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    } else if (action === 'rejected') {
        header.className = 'bg-gradient-to-r from-red-500 to-red-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-red-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-red-100';
        button.className = 'px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    } else if (action === 'restored') {
        header.className = 'bg-gradient-to-r from-orange-500 to-orange-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-orange-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-orange-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-orange-100';
        button.className = 'px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    }
    
    title.textContent = action === 'approved' ? 'Approved!' : action === 'rejected' ? 'Rejected' : 'Restored!';
    messageEl.textContent = message;
    
    modal.classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    // Reload the page to show updated data
    window.location.reload();
}

// ─── AJAX Pagination ────────────────────────────────────────────────────
// Intercept pagination link clicks so we swap just the card+pagination
// without a full page reload — no more scroll jump to the bottom.

function initAjaxPagination() {
    const container = document.getElementById('submission-ajax-container');
    if (!container) return;

    // Use event delegation — works after content is swapped too
    container.addEventListener('click', function (e) {
        const link = e.target.closest('a[href]');
        if (!link || !container.contains(link)) return;
        e.preventDefault();
        loadSubmissionPage(link.href);
    });
}

function loadSubmissionPage(url) {
    const container = document.getElementById('submission-ajax-container');

    // Subtle fade while loading
    container.style.transition = 'opacity 0.15s';
    container.style.opacity = '0.45';
    container.style.pointerEvents = 'none';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContainer = doc.getElementById('submission-ajax-container');

        if (newContainer) {
            container.innerHTML = newContainer.innerHTML;
        }

        // Restore
        container.style.opacity = '1';
        container.style.pointerEvents = '';

        // Scroll the TOP of the card into view — smooth, no jump
        const card = container.querySelector('.admin-review-card');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Update browser URL bar
        window.history.pushState({}, '', url);

        // Re-attach listener on newly swapped content
        initAjaxPagination();
    })
    .catch(err => {
        console.error('AJAX pagination failed:', err);
        container.style.opacity = '1';
        container.style.pointerEvents = '';
        // Graceful fallback to normal navigation
        window.location.href = url;
    });
}

document.addEventListener('DOMContentLoaded', initAjaxPagination);

// ─── Live Polling — detect new submissions every 30s ─────────────────────
(function () {
    let knownCounts = {
        pending:  parseInt('{{ $pendingCount }}'),
        approved: parseInt('{{ $approvedCount }}'),
        rejected: parseInt('{{ $rejectedCount }}'),
    };

    const POLL_INTERVAL = 30_000;
    const activeTab     = '{{ $activeTab }}';

    // Track accumulated new count and the single persistent toast
    let accumulatedNew  = 0;
    let notifToast      = null;

    function fetchCounts() {
        fetch('/admin/lmi-submissions/counts', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;

            const newPending  = parseInt(data.pending  ?? 0);
            const newApproved = parseInt(data.approved ?? 0);
            const newRejected = parseInt(data.rejected ?? 0);

            // Always update badges live
            updateBadge('pending',  newPending);
            updateBadge('approved', newApproved);
            updateBadge('rejected', newRejected);

            // Check if active tab has grown
            const activeNew = parseInt(data[activeTab] ?? 0);
            const activeOld = parseInt(knownCounts[activeTab] ?? 0);

            if (activeNew > activeOld) {
                accumulatedNew += (activeNew - activeOld);
                showOrUpdateNotifToast();
            }

            knownCounts = { pending: newPending, approved: newApproved, rejected: newRejected };
        })
        .catch(() => {});
    }

    function updateBadge(type, count) {
        ['header', 'tab'].forEach(prefix => {
            const el = document.getElementById(`${prefix}-${type}-count`);
            if (el) el.textContent = count;
        });
    }

    function showOrUpdateNotifToast() {
        const label     = activeTab.charAt(0).toUpperCase() + activeTab.slice(1);
        const msgText   = `🔔 ${accumulatedNew} new ${label} submission${accumulatedNew > 1 ? 's' : ''} — click to refresh`;
        const container = document.getElementById('toastContainer');

        if (notifToast && container.contains(notifToast)) {
            // Already showing — just update the text, don't create a new one
            notifToast.querySelector('.notif-text').textContent = msgText;
            // Pulse the toast to draw attention to the update
            notifToast.classList.add('scale-105');
            setTimeout(() => notifToast.classList.remove('scale-105'), 200);
            return;
        }

        // Create a fresh persistent toast
        notifToast = document.createElement('div');
        notifToast.className = [
            'pointer-events-auto w-full rounded-xl shadow-xl overflow-hidden',
            'border-l-4 border-blue-500 bg-blue-50',
            'transform transition-all duration-300 translate-x-full opacity-0',
            'cursor-pointer hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]',
            'transition-transform'
        ].join(' ');

        notifToast.innerHTML = `
            <div class="flex items-center gap-3 px-4 py-4">
                <span class="relative flex-shrink-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                <p class="notif-text text-sm font-semibold text-blue-800 flex-1 leading-snug">${msgText}</p>
                <button class="notif-dismiss text-blue-400 hover:text-blue-700 transition ml-1 flex-shrink-0"
                        title="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        // Clicking the toast body refreshes submissions
        notifToast.addEventListener('click', function (e) {
            if (e.target.closest('.notif-dismiss')) return; // ignore dismiss button
            dismissNotifToast();
            reloadSubmissions();
        });

        // Dismiss button just closes without refreshing
        notifToast.querySelector('.notif-dismiss').addEventListener('click', function (e) {
            e.stopPropagation();
            dismissNotifToast();
        });

        container.appendChild(notifToast);

        // Slide in
        requestAnimationFrame(() => requestAnimationFrame(() => {
            notifToast.classList.remove('translate-x-full', 'opacity-0');
        }));
    }

    function dismissNotifToast() {
        if (!notifToast) return;
        notifToast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notifToast?.remove();
            notifToast = null;
            accumulatedNew = 0;
        }, 300);
    }

    setInterval(fetchCounts, POLL_INTERVAL);

    window.reloadSubmissions = function () {
        loadSubmissionPage(window.location.href);
        setTimeout(() => {
            knownCounts = {
                pending:  parseInt(document.getElementById('header-pending-count')?.textContent  || 0),
                approved: parseInt(document.getElementById('header-approved-count')?.textContent || 0),
                rejected: parseInt(document.getElementById('header-rejected-count')?.textContent || 0),
            };
            accumulatedNew = 0;
        }, 1500);
    };
})();
</script>

<!-- TOAST NOTIFICATION -->
<div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>
    
</body>
</html>