<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>LMI - Job Titles Form</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    <aside id="sidebar" class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10 transition-all duration-300">
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">LMI</div>
                <div class="leading-tight">
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-auto">
            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📋</span> Regional Statistics
            </a>

            <a href="{{ route('admin.job-titles.form') }}" class="flex items-center gap-3 p-3 bg-yellow-400 text-blue-900 font-bold rounded-lg transition shadow-md">
                <span>💼</span> Job Titles Form
            </a>

             <a href="{{ route('admin.lmi-submissions.index') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📋</span> LMI Submissions
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
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

    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Titles Form • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="flex-1 overflow-auto p-8">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-8">
                <h1 class="text-xl font-semibold text-gray-900 mb-8">High-Volume Job Titles Form</h1>
                
                <form id="jobTitlesForm">
                    <div class="mb-8">
                        <label for="year" class="block text-sm font-medium text-gray-900 mb-2">
                            Year <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="year" 
                            placeholder="e.g. 2024" 
                            min="2000" 
                            max="2100" 
                            required 
                            class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <div id="jobEntries" class="space-y-3 mb-6">
                        <!-- Job entries will be added here -->
                    </div>

                    <button 
                        type="button" 
                        onclick="addJobEntry()" 
                        class="w-full py-2.5 px-6 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors mb-8"
                    >
                        + Add Job Title
                    </button>

                    <div class="flex justify-end gap-3">
                        <button 
                            type="button" 
                            onclick="resetForm()" 
                            class="py-2.5 px-6 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-md transition-colors"
                        >
                            Reset
                        </button>
                        <button 
                            type="submit" 
                            class="py-2.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
                        >
                            Submit Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-3">Confirm Submission</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to submit this data to the pending queue? The statistician will review and verify it before posting to the database.</p>

                <div class="flex gap-3">
                    <button 
                        onclick="closeConfirmModal()"
                        class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmSubmit()"
                        class="flex-1 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                    >
                        Yes, Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Successfully Submitted!</h3>
                <p class="text-sm text-gray-600 mb-6">Your data has been successfully submitted to the pending queue. It will be reviewed by a statistician before being posted to the database.</p>
                <button 
                    onclick="closeSuccessModal()"
                    class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
        let entryCount = 0;
        let pendingData = null;

        function addJobEntry() {
            entryCount++;
            const jobEntries = document.getElementById('jobEntries');
            
            const entryDiv = document.createElement('div');
            entryDiv.className = 'grid grid-cols-1 md:grid-cols-[1fr_200px_50px] gap-3 items-end';
            entryDiv.id = `entry-${entryCount}`;
            
            entryDiv.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">Job Title</label>
                    <input 
                        type="text" 
                        name="jobTitle[]" 
                        placeholder="e.g. Customer Service Rep" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">Count</label>
                    <input 
                        type="number" 
                        name="jobCount[]" 
                        placeholder="e.g. 1250" 
                        min="0" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <button 
                    type="button" 
                    onclick="removeJobEntry(${entryCount})" 
                    title="Remove"
                    class="py-2 px-3 bg-red-500 hover:bg-red-600 text-white text-xl font-bold rounded-md transition-colors"
                >
                    ×
                </button>
            `;
            
            jobEntries.appendChild(entryDiv);
        }

        function removeJobEntry(id) {
            const entry = document.getElementById(`entry-${id}`);
            if (entry) {
                entry.remove();
            }
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form?')) {
                document.getElementById('jobEntries').innerHTML = '';
                document.getElementById('year').value = '';
                entryCount = 0;
                addJobEntry();
            }
        }

        function showConfirmModal(data) {
            pendingData = data;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            pendingData = null;
        }

        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            // Reset form
            document.getElementById('jobEntries').innerHTML = '';
            document.getElementById('year').value = '';
            entryCount = 0;
            addJobEntry();
        }

       async function confirmSubmit() {
    const dataToSubmit = pendingData; // ✅ Save it first
    closeConfirmModal();              // Now safe to null it out

    try {
        const response = await fetch('{{ route("admin.job-titles.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSubmit) // ✅ Use the local copy
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showSuccessModal();
        } else {
            alert('Error: ' + (result.message || 'An error occurred while saving the data.'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the data.');
    }
}
        document.getElementById('jobTitlesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const year = document.getElementById('year').value;
            const titles = document.querySelectorAll('input[name="jobTitle[]"]');
            const counts = document.querySelectorAll('input[name="jobCount[]"]');
            
            const jobData = [];
            
            for (let i = 0; i < titles.length; i++) {
                if (titles[i].value && counts[i].value) {
                    jobData.push({
                        title: titles[i].value,
                        count: parseInt(counts[i].value)
                    });
                }
            }

            const dataToSave = {
                year: parseInt(year),
                jobs: jobData
            };

            // Show confirmation modal instead of directly submitting
            showConfirmModal(dataToSave);
        });

        // Add initial entry when page loads
        addJobEntry();
    </script>
</body>
</html>