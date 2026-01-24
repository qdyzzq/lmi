<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('path/to/chart-filtering.js') }}"></script>

    <title>LMI</title>
</head>

<body x-data="{ sidebarExpanded: true, activeView: 'job-market' }" class="bg-slate-100 flex h-screen overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-y-auto min-w-0">
        <div x-show="activeView === 'job-market'" x-transition class="p-5">
            <div class="space-y-6">

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <span class="text-blue-600">📈</span>
                            Davao Employment Dashboard
                        </h1>
                        <p class="text-sm text-slate-500">
                            Regional Labor Market Intelligence & Trends
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-8">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Supply Side Analysis</h2>
                        <p class="text-sm text-slate-500">Projected workforce entry based on academic enrollment and
                            licensure performance.</p>
                    </div>
                    <div class="flex gap-3">
                        <div
                            class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
                            <span class="text-slate-400">📍</span>
                            <span class="text-sm text-slate-500 font-medium">Province:</span>
                            <select
                                class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                <option>All Region</option>
                            </select>
                        </div>
                        <div
                            class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
                            <span class="text-slate-400">📖</span>
                            <span class="text-sm text-slate-500 font-medium">Discipline:</span>
                            <select
                                class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                <option>Education</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-blue-600 text-lg">🎓</span>
                            <h3 class="font-bold text-slate-800">Enrollment Trend (2024-2025)</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-6">Comparison of student enrollment per discipline.</p>
                        <div class="relative h-[350px]"><canvas id="enrollmentTrendChart"></canvas></div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-600 text-lg">📈</span>
                            <h3 class="font-bold text-slate-800">Projected Graduates (2026)</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-6">Forecasted workforce entry by discipline for Year 2026.
                        </p>
                        <div class="relative h-[350px]"><canvas id="projectedGraduatesChart"></canvas></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-emerald-500 text-lg">⚖️</span>
                        <h3 class="font-bold text-slate-800">Licensure Passing Rates</h3>
                    </div>
                    <p class="text-xs text-slate-400 mb-6">Performance in regulated professions (Latest Board Exams).
                    </p>
                    <div class="relative h-[400px]">
                        <canvas id="licensurePassingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Enrollment Trend Chart
            const ctxEnrollment = document.getElementById('enrollmentTrendChart').getContext('2d');
            new Chart(ctxEnrollment, {
                type: 'bar',
                data: {
                    labels: ['Education'],
                    datasets: [{
                            label: 'Enrolled 2024',
                            data: [11800],
                            backgroundColor: '#bfdbfe',
                            borderRadius: 4,
                            barThickness: 100
                        },
                        {
                            label: 'Enrolled 2025',
                            data: [12500],
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            barThickness: 100
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 14000,
                            ticks: {
                                stepSize: 3500
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                rotation: -15
                            }
                        }
                    }
                }
            });

            // 2. Projected Graduates Chart
            const ctxProjected = document.getElementById('projectedGraduatesChart').getContext('2d');
            new Chart(ctxProjected, {
                type: 'bar',
                data: {
                    labels: ['Education'],
                    datasets: [{
                        label: 'Proj. Graduates 2026',
                        data: [3100],
                        backgroundColor: '#a855f7',
                        borderRadius: 4,
                        barThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 3200,
                            ticks: {
                                stepSize: 800
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                rotation: -15
                            }
                        }
                    }
                }
            });

            // 3. Licensure Passing Rates Chart
            const ctxPassing = document.getElementById('licensurePassingChart').getContext('2d');
            const passingData = [{
                    label: 'Nurses',
                    rate: 75
                },
                {
                    label: 'Teachers (LET)',
                    rate: 58
                },
                {
                    label: 'Civil Engineers',
                    rate: 42
                },
                {
                    label: 'CPA',
                    rate: 25
                },
                {
                    label: 'Electronics Eng.',
                    rate: 38
                }
            ];

            new Chart(ctxPassing, {
                type: 'bar',
                data: {
                    labels: passingData.map(d => d.label),
                    datasets: [{
                        label: 'Passing Rate %',
                        data: passingData.map(d => d.rate),
                        backgroundColor: passingData.map(d => d.rate >= 40 ? '#4ade80' : '#fca5a5'),
                        borderRadius: 4,
                        barThickness: 25,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: v => v + '%',
                                stepSize: 25
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
