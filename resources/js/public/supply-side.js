// ─── Supply Side Analysis (Public) ───────────────────────────────────────────
// No Blade PHP data is injected into this file — all data is fetched via API.
// ─────────────────────────────────────────────────────────────────────────────

// ─── Discipline Colors, Alpine Component & Chart Logic (Block 1) ──────────────
// ── Fixed discipline-to-color map ──────────────────────────────────────────
    // Colors are permanently assigned by discipline name so they never shift
    // when percentages change between filters/years.
    // ── Fixed discipline colors — keyed by every possible API format ─────────
    // Colors from the reference chart image. Never changes by rank or value.
    function getDeepBlueByRank(rank, total) {
        const stops = [
            { r: 15,  g: 23,  b: 83  },
            { r: 23,  g: 37,  b: 115 },
            { r: 30,  g: 58,  b: 138 },
            { r: 29,  g: 78,  b: 216 },
            { r: 37,  g: 99,  b: 235 },
            { r: 59,  g: 130, b: 246 },
            { r: 96,  g: 165, b: 250 },
            { r: 147, g: 197, b: 253 },
            { r: 186, g: 220, b: 254 },
            { r: 219, g: 238, b: 255 },
        ];
        const factor = total > 1 ? rank / (total - 1) : 0;
        const pos = factor * (stops.length - 1);
        const lo  = Math.floor(pos);
        const hi  = Math.min(lo + 1, stops.length - 1);
        const t   = pos - lo;
        const r   = Math.round(stops[lo].r + (stops[hi].r - stops[lo].r) * t);
        const g   = Math.round(stops[lo].g + (stops[hi].g - stops[lo].g) * t);
        const b   = Math.round(stops[lo].b + (stops[hi].b - stops[lo].b) * t);
        return `rgb(${r}, ${g}, ${b})`;
    }

    // Fixed Deep Blue shade per discipline — never changes regardless of value
    const DISCIPLINE_DEEP_BLUE = {
        'business':           'rgb(15,23,83)',
        'education':          'rgb(20,34,102)',
        'medical':            'rgb(25,48,122)',
        'criminal_justice':   'rgb(29,64,140)',
        'engineering':        'rgb(30,78,160)',
        'it':                 'rgb(32,92,185)',
        'social_sciences':    'rgb(35,105,200)',
        'service_trades':     'rgb(37,99,235)',
        'agriculture':        'rgb(50,115,240)',
        'maritime':           'rgb(59,130,246)',
        'architecture':       'rgb(80,148,248)',
        'natural_science':    'rgb(96,165,250)',
        'humanities':         'rgb(120,182,251)',
        'mass_comm':          'rgb(140,195,252)',
        'arts':               'rgb(155,206,253)',
        'religion':           'rgb(168,214,253)',
        'mathematics':        'rgb(180,220,254)',
        'law':                'rgb(190,226,254)',
        'home_economics':     'rgb(200,232,255)',
        'general':            'rgb(210,237,255)',
        'other_disciplines':  'rgb(219,238,255)',
        'Business Administration':          'rgb(15,23,83)',
        'Education Science':                'rgb(20,34,102)',
        'Medical and Allied':               'rgb(25,48,122)',
        'Criminal Justice Education':       'rgb(29,64,140)',
        'Engineering and Technology':       'rgb(30,78,160)',
        'IT-Related Disciplines':           'rgb(32,92,185)',
        'Social and Behavioral Sciences':   'rgb(35,105,200)',
        'Social and Behavioral Science':    'rgb(35,105,200)',
        'Service Trades':                   'rgb(37,99,235)',
        'Agriculture, Forestry, Fisheries': 'rgb(50,115,240)',
        'Maritime':                         'rgb(59,130,246)',
        'Architecture and Town Planning':   'rgb(80,148,248)',
        'Natural Science':                  'rgb(96,165,250)',
        'Humanities':                       'rgb(120,182,251)',
        'Mass Communication':               'rgb(140,195,252)',
        'Fine and Applied Arts':            'rgb(155,206,253)',
        'Religion and Theology':            'rgb(168,214,253)',
        'Mathematics':                      'rgb(180,220,254)',
        'Law and Jurisprudence':            'rgb(190,226,254)',
        'Home Economics':                   'rgb(200,232,255)',
        'General Programs':                 'rgb(210,237,255)',
        'Other Disciplines':                'rgb(219,238,255)',
        'Business & Admin':   'rgb(15,23,83)',
        'Medical & Allied':   'rgb(25,48,122)',
        'Criminal Justice':   'rgb(29,64,140)',
        'Engineering & Tech': 'rgb(30,78,160)',
        'IT & Related':       'rgb(32,92,185)',
        'Social Sciences':    'rgb(35,105,200)',
        'Agri & Forestry':    'rgb(50,115,240)',
        'Fine Arts':          'rgb(155,206,253)',
        'Religion':           'rgb(168,214,253)',
        'Mass Comm':          'rgb(140,195,252)',
        'Education':          'rgb(20,34,102)',
        'Law':                'rgb(190,226,254)',
    };
    function getDeepBlueForDiscipline(key) {
        return DISCIPLINE_DEEP_BLUE[key]
            || DISCIPLINE_DEEP_BLUE[(key||'').toLowerCase()]
            || 'rgb(219,238,255)';
    }

    // ─────────────────────────────────────────────────────────────────────────

    document.addEventListener('alpine:init', () => {
        Alpine.data('licensureChartData', () => ({
            selectedSector: 'all',
            selectedYear: new Date().getFullYear(),
            availableYears: [],
            sectors: [],
            allData: [],
            chart: null,
            expanded: false,
            chartHeight: 600,
            // Modal open states for each chart
            enrollmentTrendModalOpen: false,
            disciplineEnrollmentModalOpen: false,
            pieModalOpen: false,
            
            // Enrollment data variables
            selectedEnrollmentYear: '',
            availableEnrollmentYears: [],
            selectedEnrollmentProvince: 'Davao Region',
            availableEnrollmentProvinces: [], // Will be loaded from API
            enrollmentData: [],
            enrollmentChart: null,
            enrollmentNoDataForCombo: false,
            
            // New charts variables
            disciplineMarketShareChart: null,
            enrollmentTrendChart: null,
            selectedProvince: 'Davao Region',
            selectedTrendYear: '',
            availableTrendYears: [], // Will be loaded from API
            selectedTrendProvince: 'Davao Region',
            availableTrendProvinces: [], // Will be loaded from API
            
            // Enrollment Trend totals for stats display
            enrollmentTrendTotals: {
                public: 0,
                private: 0,
                combined: 0
            },
            trendDataCount: 0,
            trendTableData: [], // Populated by buildEnrollmentTrendChart() — used by mobile inline bar table
            
            // NEW: Enrollment Overview Data (for top cards and pie chart)
            totalEnrollees: 0,
            disciplineShares: {}, // Will be populated dynamically with all individual disciplines
            
            // Executive Analysis
            executiveAnalysisText: 'Loading analysis...',
            loadingExecutiveAnalysis: false,
            
            // NEW: Graduation Rate Data (for top metric cards)
            graduationRateData: {
                graduate_year: null,
                enrollment_year: null,
                graduation_rate: 60,
                base_enrollees: 0,
                projected_graduates: 0,
                is_default: true
            },
            loadingGraduationRate: false,
            latestEnrollmentTotal: 0,
            latestEnrollmentYear: null,
            loadingLatestEnrollment: false,
            loadingPieChart: false,
            loadingEnrollmentTrend: false,
            loadingDisciplineEnrollment: false,
            loadingLicensure: false,
            
            
            // === INITIALIZATION FLAGS === (PREVENTS INFINITE RECURSION)
            chartInitialized: {
                disciplineMarketShare: false,
                enrollmentTrend: false
            },
            
            sectorColors: {
                'Engineering, Architecture & Technical': '#FF6B00',
                'Healthcare & Nursing': '#00AA00',
                'Natural Sciences': '#FFCC00',
                'Education': '#0066FF',
                'Social Work & Behavioral Sciences': '#9900FF',
                'Real Estate Industry': '#FF1493',
                'Defense Industry': '#FF0000',
                'Business, Finance & Logistics': '#00CCCC',
            },

            async init() {
                await this.loadAvailableYears();
                await this.loadData();
                await this.loadEnrollmentProvinces(); // Load provinces first so selectedEnrollmentProvince is valid
                await this.loadEnrollmentYearsForProvince(this.selectedEnrollmentProvince); // Years filtered by default province
                await this.loadEnrollmentData();
                await this.loadTrendProvinces(); // Load trend provinces from API
                await this.loadTrendYearsForProvince(this.selectedTrendProvince); // Load trend years filtered by default province (Davao City)
                
                // NEW: Load graduation rate data (for top metric cards)
                await this.loadGraduationRateData();
                
                // Load latest enrollment total for the Total Enrollees KPI card
                await this.loadLatestEnrollmentTotal();
                
                setTimeout(() => this.initOtherCharts(), 100);
                setTimeout(() => this.initEnrollmentTrendChart(), 100);
                
                // NEW: Load enrollment overview data
                await this.loadEnrollmentOverviewData();
                
                // Watch for expanded changes — render a fresh chart in the modal canvas
                this.$watch('expanded', (isExpanded) => {
                    if (isExpanded) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderModalChart(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const modalCanvas = document.getElementById('licensurePassingChartModal');
                        if (modalCanvas) { const e = Chart.getChart(modalCanvas); if (e) e.destroy(); }
                    }
                });

                // Enrollment Trend modal
                this.$watch('enrollmentTrendModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderEnrollmentTrendModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('enrollmentTrendChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });

                // Discipline Enrollment modal
                this.$watch('disciplineEnrollmentModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderDisciplineEnrollmentModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('disciplineEnrollmentChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });

                // Pie modal
                this.$watch('pieModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderPieModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('disciplineMarketShareChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });
            },

            async loadAvailableYears() {
                try {
                    const response = await fetch('/api/licensure-rates/years');
                    this.availableYears = await response.json();
                    if (this.availableYears.length > 0) {
                        this.selectedYear = this.availableYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableYears = [2025, 2024, 2023, 2022, 2021];
                    this.selectedYear = 2025;
                }
            },

            // NEW: Load Graduation Rate Data for current year
            async loadGraduationRateData() {
                this.loadingGraduationRate = true;

                try {
                    // Fetch all saved graduation rate records and pick the most recent
                    // one that has actual projected_graduates data.
                    // This avoids showing 0 when the current year's cohort enrollment
                    // (4 years back) hasn't been entered yet.
                    const allResponse = await fetch('/api/graduation-rate/');
                    if (!allResponse.ok) throw new Error(`HTTP ${allResponse.status}`);
                    const allRecords = await allResponse.json();

                    // Sort descending by graduate_year so newest is first
                    const sorted = Array.isArray(allRecords)
                        ? [...allRecords].sort((a, b) => (b.graduate_year ?? '').localeCompare(a.graduate_year ?? ''))
                        : [];

                    // Walk through records and pick the first one with real data
                    const validRecord = sorted.find(r =>
                        r.projected_graduates > 0 &&
                        r.enrollment_year &&
                        r.graduation_rate
                    );

                    if (validRecord) {
                        this.graduationRateData = validRecord;
                    } else {
                        // No valid record found — show empty state
                        this.graduationRateData = {
                            graduate_year: null,
                            enrollment_year: null,
                            graduation_rate: null,
                            base_enrollees: 0,
                            projected_graduates: 0,
                            is_default: true
                        };
                    }
                } catch (error) {
                    this.graduationRateData = {
                        graduate_year: null,
                        enrollment_year: null,
                        graduation_rate: null,
                        base_enrollees: 0,
                        projected_graduates: 0,
                        is_default: true
                    };
                } finally {
                    this.loadingGraduationRate = false;
                }
            },

            // Load the latest enrollment total for the Total Enrollees KPI card
            async loadLatestEnrollmentTotal() {
                this.loadingLatestEnrollment = true;
                try {
                    // Get all available enrollment years
                    const yearsResponse = await fetch('/api/discipline-enrollment/meta/years');
                    const years = await yearsResponse.json();
                    if (!years || years.length === 0) return;

                    // Walk through years newest-first, find the latest year where
                    // Davao Region has Private OR Public data (now uses Private+Public split)
                    let resolvedYear = null;
                    let resolvedTotal = 0;

                    for (const year of years) {
                        const [privateResult, publicResult] = await Promise.allSettled([
                            fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=Davao+Region&institution_type=Private`),
                            fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=Davao+Region&institution_type=Public`)
                        ]);
                        let yearTotal = 0;
                        for (const res of [privateResult, publicResult]) {
                            if (res.status === 'fulfilled' && res.value.ok) {
                                const raw = await res.value.json();
                                if (raw.exists && raw.data && raw.data.disciplines) {
                                    yearTotal += Object.values(raw.data.disciplines)
                                        .reduce((sum, val) => sum + (parseInt(val) || 0), 0);
                                }
                            }
                        }
                        if (yearTotal > 0) {
                            resolvedYear  = year;
                            resolvedTotal = yearTotal;
                            break; // Found the most recent valid year — stop
                        }
                    }

                    if (resolvedYear) {
                        this.latestEnrollmentYear  = resolvedYear;
                        this.latestEnrollmentTotal = resolvedTotal;
                    }
                    // If nothing found at all, card stays at 0 / 'No data' — that's fine

                } catch (error) {
                    // silently fail — card will show 0
                } finally {
                    this.loadingLatestEnrollment = false;
                }
            },

            // Helper function to format numbers with commas
            formatNumber(num) {
                if (!num && num !== 0) return '0';
                return parseInt(num).toLocaleString();
            },

            // Helper function to format discipline names (convert snake_case to Title Case)
            formatDisciplineName(discipline) {
                const fullNames = {
                    // snake_case keys (from enrollment by discipline API)
                    'agriculture':      'Agriculture, Forestry, Fisheries',
                    'architecture':     'Architecture and Town Planning',
                    'business':         'Business Administration',
                    'criminal_justice': 'Criminal Justice Education',
                    'education':        'Education Science',
                    'engineering':      'Engineering and Technology',
                    'arts':             'Fine and Applied Arts',
                    'general':          'General Programs',
                    'home_economics':   'Home Economics',
                    'humanities':       'Humanities',
                    'it':               'IT-Related Disciplines',
                    'law':              'Law and Jurisprudence',
                    'maritime':         'Maritime',
                    'mass_comm':        'Mass Communication',
                    'mathematics':      'Mathematics',
                    'medical':          'Medical and Allied',
                    'natural_science':  'Natural Science',
                    'other_disciplines':'Other Disciplines',
                    'other':            'Other Disciplines',
                    'religion':         'Religion and Theology',
                    'service_trades':   'Service Trades',
                    'social_sciences':  'Social and Behavioral Sciences',
                    // Short display strings (from trend API)
                    'Education':             'Education Science',
                    'Business & Admin':      'Business Administration',
                    'Medical & Allied':      'Medical and Allied',
                    'Engineering & Tech':    'Engineering and Technology',
                    'Criminal Justice':      'Criminal Justice Education',
                    'IT & Related':          'IT-Related Disciplines',
                    'Social Sciences':       'Social and Behavioral Sciences',
                    'Maritime':              'Maritime',
                    'Architecture':          'Architecture and Town Planning',
                    'Service Trades':        'Service Trades',
                    'Agri & Forestry':       'Agriculture, Forestry, Fisheries',
                    'Other Disciplines':     'Other Disciplines',
                    'Humanities':            'Humanities',
                    'Natural Science':       'Natural Science',
                    'Law':                   'Law and Jurisprudence',
                    'Fine Arts':             'Fine and Applied Arts',
                    'Religion':              'Religion and Theology',
                    'Mass Comm':             'Mass Communication',
                    'Mathematics':           'Mathematics',
                    'Home Economics':        'Home Economics',
                };
                return fullNames[discipline] || discipline
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },

            // Helper function to generate consistent colors for disciplines
            // Delegates to the global fixed color map so colors never change by rank/value
            getDisciplineColor(discipline) {
                return getDeepBlueForDiscipline(discipline);
            },

            async loadData() {
                this.loadingLicensure = true;
                try {
                    const response = await fetch(`/api/licensure-rates/year/${this.selectedYear}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    this.allData = await response.json();
                    this.sectors = [...new Set(this.allData.map(item => item.sector))].sort();
                    // removed debug log
                    this.$nextTick(() => this.updateChart());
                } catch (error) {
                    // removed error
                    this.allData = [];
                    this.sectors = [];
                    alert('Failed to load data from API. Please check:\n1. API endpoint is correct\n2. Server is running\n3. API returns data in correct format');
                } finally {
                    this.loadingLicensure = false;
                }
            },

            async loadEnrollmentYears() {
                try {
                    const response = await fetch('/api/discipline-enrollment/meta/years');
                    this.availableEnrollmentYears = await response.json();
                    if (this.availableEnrollmentYears.length > 0) {
                        this.selectedEnrollmentYear = this.availableEnrollmentYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableEnrollmentYears = ['2024-2025', '2023-2024'];
                    this.selectedEnrollmentYear = '2024-2025';
                }
            },

            async loadEnrollmentProvinces() {
                try {
                    const response = await fetch('/api/discipline-enrollment/provinces');
                    const provinces = await response.json();
                    // Always include "Davao Region" as the first option
                    // Filter out 'Davao Region' from API results to avoid duplicate, then prepend it
                    const filteredProvinces = provinces.filter(p => p !== 'Davao Region');
                    this.availableEnrollmentProvinces = ['Davao Region', ...filteredProvinces];
                    // removed debug log
                } catch (error) {
                    // removed error
                    // Fallback to Davao Region provinces
                    this.availableEnrollmentProvinces = [
                        'Davao Region',
                        'Davao del Norte',
                        'Davao del Sur', 
                        'Davao Oriental',
                        'Davao de Oro',
                        'Davao Occidental'
                    ];
                }
            },

            async loadEnrollmentYearsForProvince(province) {
                try {
                    // Always pass province so the API returns only years with data for that province
                    let url = '/api/discipline-enrollment/meta/years';
                    if (province) {
                        url += '?province=' + encodeURIComponent(province);
                    }
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Failed');
                    const years = await response.json();
                    if (years.length > 0) {
                        this.availableEnrollmentYears = years;
                        if (!years.includes(this.selectedEnrollmentYear)) {
                            this.selectedEnrollmentYear = years[0];
                        }
                        this.enrollmentNoDataForCombo = false;
                    } else {
                        this.availableEnrollmentYears = [];
                        this.selectedEnrollmentYear = '';
                        this.enrollmentNoDataForCombo = true;
                    }
                } catch (error) {
                    // Fallback: keep existing year list
                }
            },

            async loadTrendYears() {
                try {
                    const response = await fetch('/api/discipline-enrollment/meta/years');
                    this.availableTrendYears = await response.json();
                    if (this.availableTrendYears.length > 0) {
                        this.selectedTrendYear = this.availableTrendYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableTrendYears = ['2024-2025', '2023-2024', '2022-2023'];
                    this.selectedTrendYear = '2024-2025';
                }
            },

            async loadTrendYearsForProvince(province) {
                try {
                    let url = '/api/discipline-enrollment/meta/years';
                    if (province) {
                        url += '?province=' + encodeURIComponent(province);
                    }
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Failed');
                    const years = await response.json();
                    if (years.length > 0) {
                        this.availableTrendYears = years;
                        if (!years.includes(this.selectedTrendYear)) {
                            this.selectedTrendYear = years[0];
                        }
                    } else {
                        this.availableTrendYears = [];
                        this.selectedTrendYear = '';
                    }
                } catch (error) {
                    // Fallback: keep existing year list
                }
            },


            async loadTrendProvinces() {
                try {
                    const response = await fetch('/api/discipline-enrollment/provinces');
                    const provinces = await response.json();
                    // Always include "Davao Region" as the first option
                    // Davao Region now has Private/Public data — include it in the trend list
                    const filteredTrend = provinces.filter(p => p !== 'Davao Region');
                    this.availableTrendProvinces = ['Davao Region', ...filteredTrend];
                    // removed debug log
                } catch (error) {
                    // removed error
                    // Fallback to Davao Region provinces
                    this.availableTrendProvinces = [
                        'Davao Region',
                        'Davao del Norte',
                        'Davao del Sur',
                        'Davao Oriental',
                        'Davao de Oro',
                        'Davao Occidental'
                    ];
                }
            },



            // Smart enrollment fetch: uses Private+Public for all provinces including Davao Region
            async fetchEnrollmentByProvince(year, province) {
                // NOTE: Davao Region now has Private/Public data — treat it like any other province
                const [privateResult, publicResult] = await Promise.allSettled([
                    fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=${encodeURIComponent(province)}&institution_type=Private`),
                    fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=${encodeURIComponent(province)}&institution_type=Public`)
                ]);
                let privateData = { disciplines: {} };
                let publicData = { disciplines: {} };
                if (privateResult.status === 'fulfilled' && privateResult.value.ok) {
                    const raw = await privateResult.value.json();
                    if (raw.exists && raw.data) { privateData = raw.data; }
                }
                if (publicResult.status === 'fulfilled' && publicResult.value.ok) {
                    const raw = await publicResult.value.json();
                    if (raw.exists && raw.data) { publicData = raw.data; }
                }
                return { privateData, publicData, isDavaoTotal: false };
            },
            async loadEnrollmentData() {
                this.loadingDisciplineEnrollment = true;
                try {
                    // Fetch aggregated data based on selected province for both Private and Public
                    const province = this.selectedEnrollmentProvince;

                    // Smart fetch: uses Private+Public for all provinces including Davao Region
                    const { privateData, publicData } = await this.fetchEnrollmentByProvince(this.selectedEnrollmentYear, province);

                    // Combine for total enrollment
                    this.enrollmentData = [
                        { 
                            discipline: 'Agriculture, Forestry, Fisheries', 
                            count: (privateData.disciplines.agriculture || 0) + (publicData.disciplines.agriculture || 0),
                            private: privateData.disciplines.agriculture || 0,
                            public: publicData.disciplines.agriculture || 0
                        },
                        { 
                            discipline: 'Architecture and Town Planning', 
                            count: (privateData.disciplines.architecture || 0) + (publicData.disciplines.architecture || 0),
                            private: privateData.disciplines.architecture || 0,
                            public: publicData.disciplines.architecture || 0
                        },
                        { 
                            discipline: 'Business Administration', 
                            count: (privateData.disciplines.business || 0) + (publicData.disciplines.business || 0),
                            private: privateData.disciplines.business || 0,
                            public: publicData.disciplines.business || 0
                        },
                        { 
                            discipline: 'Criminal Justice Education', 
                            count: (privateData.disciplines.criminal_justice || 0) + (publicData.disciplines.criminal_justice || 0),
                            private: privateData.disciplines.criminal_justice || 0,
                            public: publicData.disciplines.criminal_justice || 0
                        },
                        { 
                            discipline: 'Education Science', 
                            count: (privateData.disciplines.education || 0) + (publicData.disciplines.education || 0),
                            private: privateData.disciplines.education || 0,
                            public: publicData.disciplines.education || 0
                        },
                        { 
                            discipline: 'Engineering and Technology', 
                            count: (privateData.disciplines.engineering || 0) + (publicData.disciplines.engineering || 0),
                            private: privateData.disciplines.engineering || 0,
                            public: publicData.disciplines.engineering || 0
                        },
                        { 
                            discipline: 'Fine and Applied Arts', 
                            count: (privateData.disciplines.arts || 0) + (publicData.disciplines.arts || 0),
                            private: privateData.disciplines.arts || 0,
                            public: publicData.disciplines.arts || 0
                        },
                        { 
                            discipline: 'General Programs', 
                            count: (privateData.disciplines.general || 0) + (publicData.disciplines.general || 0),
                            private: privateData.disciplines.general || 0,
                            public: publicData.disciplines.general || 0
                        },
                        { 
                            discipline: 'Home Economics', 
                            count: (privateData.disciplines.home_economics || 0) + (publicData.disciplines.home_economics || 0),
                            private: privateData.disciplines.home_economics || 0,
                            public: publicData.disciplines.home_economics || 0
                        },
                        { 
                            discipline: 'Humanities', 
                            count: (privateData.disciplines.humanities || 0) + (publicData.disciplines.humanities || 0),
                            private: privateData.disciplines.humanities || 0,
                            public: publicData.disciplines.humanities || 0
                        },
                        { 
                            discipline: 'IT-Related Disciplines', 
                            count: (privateData.disciplines.it || 0) + (publicData.disciplines.it || 0),
                            private: privateData.disciplines.it || 0,
                            public: publicData.disciplines.it || 0
                        },
                        { 
                            discipline: 'Law and Jurisprudence', 
                            count: (privateData.disciplines.law || 0) + (publicData.disciplines.law || 0),
                            private: privateData.disciplines.law || 0,
                            public: publicData.disciplines.law || 0
                        },
                        { 
                            discipline: 'Maritime', 
                            count: (privateData.disciplines.maritime || 0) + (publicData.disciplines.maritime || 0),
                            private: privateData.disciplines.maritime || 0,
                            public: publicData.disciplines.maritime || 0
                        },
                        { 
                            discipline: 'Mass Communication', 
                            count: (privateData.disciplines.mass_comm || 0) + (publicData.disciplines.mass_comm || 0),
                            private: privateData.disciplines.mass_comm || 0,
                            public: publicData.disciplines.mass_comm || 0
                        },
                        { 
                            discipline: 'Mathematics', 
                            count: (privateData.disciplines.mathematics || 0) + (publicData.disciplines.mathematics || 0),
                            private: privateData.disciplines.mathematics || 0,
                            public: publicData.disciplines.mathematics || 0
                        },
                        { 
                            discipline: 'Medical and Allied', 
                            count: (privateData.disciplines.medical || 0) + (publicData.disciplines.medical || 0),
                            private: privateData.disciplines.medical || 0,
                            public: publicData.disciplines.medical || 0
                        },
                        { 
                            discipline: 'Natural Science', 
                            count: (privateData.disciplines.natural_science || 0) + (publicData.disciplines.natural_science || 0),
                            private: privateData.disciplines.natural_science || 0,
                            public: publicData.disciplines.natural_science || 0
                        },
                        { 
                            discipline: 'Other Disciplines', 
                            count: (privateData.disciplines.other_disciplines || 0) + (publicData.disciplines.other_disciplines || 0),
                            private: privateData.disciplines.other_disciplines || 0,
                            public: publicData.disciplines.other_disciplines || 0
                        },
                        { 
                            discipline: 'Religion and Theology', 
                            count: (privateData.disciplines.religion || 0) + (publicData.disciplines.religion || 0),
                            private: privateData.disciplines.religion || 0,
                            public: publicData.disciplines.religion || 0
                        },
                        { 
                            discipline: 'Service Trades', 
                            count: (privateData.disciplines.service_trades || 0) + (publicData.disciplines.service_trades || 0),
                            private: privateData.disciplines.service_trades || 0,
                            public: publicData.disciplines.service_trades || 0
                        },
                        { 
                            discipline: 'Social and Behavioral Sciences', 
                            count: (privateData.disciplines.social_sciences || 0) + (publicData.disciplines.social_sciences || 0),
                            private: privateData.disciplines.social_sciences || 0,
                            public: publicData.disciplines.social_sciences || 0
                        }
                    ]; // Show all 21 disciplines including those with 0 enrollment
                    
                    // removed debug log
                    this.$nextTick(() => this.updateEnrollmentChart());
                    
                    // NEW: Also load enrollment overview data (for top cards and pie chart)
                    await this.loadEnrollmentOverviewData();
                    
                    // Load Executive Analysis
                    await this.loadExecutiveAnalysis();
                } catch (error) {
                    // removed error
                    this.enrollmentData = [];
                } finally {
                    this.loadingDisciplineEnrollment = false;
                }
            },

            // Load Executive Analysis from database
            async loadExecutiveAnalysis() {
                this.loadingExecutiveAnalysis = true;
                
                try {
                    const params = new URLSearchParams({
                        province: this.selectedEnrollmentProvince,
                        academic_year: this.selectedEnrollmentYear
                    });
                    
                    const response = await fetch(`/api/supply-side-analysis/show?${params}`);
                    const data = await response.json();
                    
                    if (data.success && data.data) {
                        this.executiveAnalysisText = data.data.analysis_text;
                    } else {
                        this.executiveAnalysisText = "ERROR: Unable to load analysis";
                    }
                } catch (error) {
                    // removed error
                    this.executiveAnalysisText = "ERROR: Failed to load analysis";
                } finally {
                    this.loadingExecutiveAnalysis = false;
                }
            },

            getTotalEnrollment() {
                return this.enrollmentData.reduce((sum, item) => sum + item.count, 0);
            },

            getEnrollmentChartHeight() {
                const dataCount = this.enrollmentData.length;
                return Math.max(500, dataCount * 35);
            },

            generateBlueGradientColors(count) {
                // Blue gradient: from dark blue (#1e3a8a) to light blue (#eff6ff)
                const startColor = { r: 30, g: 58, b: 138 };   // blue-900
                const endColor = { r: 239, g: 246, b: 255 };   // blue-50
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            updateEnrollmentChart() {
                const ctx = document.getElementById('disciplineEnrollmentChart');
                if (!ctx) {
                    // removed error
                    return;
                }
                
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                if (this.enrollmentChart) {
                    try {
                        this.enrollmentChart.destroy();
                    } catch (e) {
                        // removed warning
                    }
                    this.enrollmentChart = null;
                }
                
                if (this.enrollmentData.length === 0) {
                    // removed debug log
                    return;
                }
                
                // Sort by count descending (highest first)
                const sortedData = [...this.enrollmentData].sort((a, b) => b.count - a.count);
                
                const labels = sortedData.map(d => d.discipline);
                const counts = sortedData.map(d => d.count);
                const colors = this.generateBlueGradientColors(sortedData.length);
                
                setTimeout(() => {
                    try {
                        this.enrollmentChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Enrollment Count',
                                    data: counts,
                                    backgroundColor: colors,
                                    borderRadius: 8,
                                    // Remove fixed barThickness, use percentage-based spacing instead
                                }]
                            },
                            options: {
                                // ENHANCED ANIMATION OPTIONS
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        let delay = 0;
                                        if (context.type === 'data' && context.mode === 'default') {
                                            delay = context.dataIndex * 30;
                                        }
                                        return delay;
                                    }
                                },
                                animations: {
                                    x: {
                                        duration: 1500,
                                        from: 0,
                                        easing: 'easeOutQuart'
                                    }
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 400
                                        }
                                    }
                                },
                                
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: {
                                        right: 60
                                    }
                                },
                                plugins: {
                                    legend: { 
                                        display: false 
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: false,
                                        callbacks: {
                                            title: (context) => sortedData[context[0].dataIndex].discipline,
                                            label: (context) => {
                                                const data = sortedData[context.dataIndex];
                                                const count = new Intl.NumberFormat('en-US').format(data.count);
                                                return [
                                                    `Enrollment: ${count} students`,
                                                    `Academic Year: ${this.selectedEnrollmentYear}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(148, 163, 184, 0.1)',
                                            borderDash: [8, 4]
                                        },
                                        ticks: {
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            callback: function(value) {
                                                return new Intl.NumberFormat('en-US').format(value);
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'ENROLLMENT COUNT',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        grid: { 
                                            display: false 
                                        },
                                        ticks: {
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        // Match Licensure chart spacing
                                        categoryPercentage: 0.6,  // Thinner bars with more space
                                        barPercentage: 0.7,        // Bar width within category
                                        title: {
                                            display: true,
                                            text: 'DISCIPLINES',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'enrollmentValueLabels',
                                afterDatasetsDraw: function(chart) {
                                    const ctx = chart.ctx;
                                    const meta = chart.getDatasetMeta(0);
                                    
                                    if (!meta || !meta.data || meta.data.length === 0) {
                                        return;
                                    }
                                    
                                    ctx.save();
                                    
                                    meta.data.forEach((element, index) => {
                                        const count = sortedData[index].count;
                                        
                                        const base = element.base;
                                        const x = element.x;
                                        const y = element.y;
                                        
                                        // Draw count in the CENTER of the bar
                                        if (count && count > 0) {
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 13px Arial, sans-serif';
                                            
                                            const countText = new Intl.NumberFormat('en-US').format(count);
                                            const centerX = base + ((x - base) / 2);
                                            
                                            // White text with black outline for contrast
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(countText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(countText, centerX, y);
                                        }
                                    });
                                    
                                    ctx.restore();
                                }
                            }]
                        });
                        // removed debug log
                    } catch (error) {
                        // removed error
                    }
                }, 100);
            },

            getFilteredData() {
                const filtered = this.selectedSector === 'all' 
                    ? this.allData 
                    : this.allData.filter(item => item.sector === this.selectedSector);
                
                return filtered;
            },

            getAverageRate() {
                const data = this.getFilteredData();
                if (data.length === 0) return 0;
                const avg = data.reduce((sum, item) => sum + item.passing_rate, 0) / data.length;
                return avg.toFixed(1);
            },

            getHighestRate() {
                const data = this.getFilteredData();
                if (data.length === 0) return 0;
                return Math.max(...data.map(item => item.passing_rate)).toFixed(2);
            },

            getChartHeight() {
                // Return stored height value
                return this.chartHeight;
            },

            getExpandedChartHeight() {
                const count = this.getFilteredData().length;
                // For very few items, keep height small so bar stays thin and centered
                if (count <= 3) return 300;
                if (count <= 10) return count * 60;
                return Math.min(count * 48, 4000);
            },

            renderModalChart() {
                const filteredData = this.getFilteredData();
                const ctx = document.getElementById('licensurePassingChartModal');
                if (!ctx || filteredData.length === 0) return;

                // Destroy any existing chart on this canvas
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                const labels = filteredData.map(d => d.profession);
                const rates  = filteredData.map(d => d.passing_rate);
                const counts = filteredData.map(d => d.total_takers || 0);
                const colors = this.generateGradientColors(filteredData.length, this.selectedSector !== 'all' ? this.selectedSector : null);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Passing Rate (%)',
                            data: rates,
                            backgroundColor: colors,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 20, bottom: 20, left: 10, right: 60 }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (c) => [
                                        `Passing Rate: ${c.parsed.x.toFixed(2)}%`,
                                        `Total Takers: ${counts[c.dataIndex].toLocaleString()}`
                                    ]
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(148,163,184,0.15)' },
                                title: {
                                    display: true,
                                    text: 'PASSING RATE (%)',
                                    font: { size: 12, weight: 'bold' },
                                    color: '#475569',
                                    padding: { top: 8 }
                                },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v + '%' }
                            },
                            y: {
                                grid: { display: false },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'PROFESSIONS',
                                    font: { size: 12, weight: 'bold' },
                                    color: '#475569',
                                    padding: { bottom: 8 }
                                },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.6,
                                barPercentage: 0.7,
                                maxBarThickness: 32
                            }
                        }
                    },
                    plugins: [{
                        id: 'licensureModalValueLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            const meta = chart.getDatasetMeta(0);
                            if (!meta || !meta.data || meta.data.length === 0) return;
                            ctx.save();
                            meta.data.forEach((element, index) => {
                                const passers = filteredData[index].passers;
                                const passingRate = filteredData[index].passing_rate;
                                const base = element.base;
                                const x = element.x;
                                const y = element.y;
                                // Draw passers count centered inside the bar (white with black outline)
                                if (passers && passers > 0) {
                                    const passersText = new Intl.NumberFormat('en-US').format(passers);
                                    const centerX = base + ((x - base) / 2);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    ctx.font = 'bold 13px Arial, sans-serif';
                                    ctx.strokeStyle = '#000000';
                                    ctx.lineWidth = 3;
                                    ctx.strokeText(passersText, centerX, y);
                                    ctx.fillStyle = '#ffffff';
                                    ctx.fillText(passersText, centerX, y);
                                }
                                // Draw passing rate % outside the bar end (dark text)
                                if (passingRate) {
                                    const rateText = passingRate.toFixed(2) + '%';
                                    ctx.textAlign = 'left';
                                    ctx.textBaseline = 'middle';
                                    ctx.font = 'bold 12px Arial, sans-serif';
                                    ctx.fillStyle = '#1e293b';
                                    ctx.fillText(rateText, x + 8, y);
                                }
                            });
                            ctx.restore();
                        }
                    }]
                });
            },

            // ── Enrollment Trend Modal Chart ──────────────────────────────
            async renderEnrollmentTrendModal() {
                const ctx = document.getElementById('enrollmentTrendChartModal');
                if (!ctx) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                // Re-fetch fresh data using current filters
                let labels = [], publicData = [], privateData = [];
                try {
                    const res = await fetch(`/api/discipline-enrollment/trend?year=${encodeURIComponent(this.selectedTrendYear)}&province=${encodeURIComponent(this.selectedTrendProvince)}`);
                    const d = await res.json();
                    labels = (d.disciplines || []).map(d => this.formatDisciplineName(d));
                    publicData = (d.publicSchools || []).map(v => Number(v) || 0);
                    privateData = (d.privateSchools || []).map(v => Number(v) || 0);
                } catch(e) { return; }

                const barHeight = 36;
                const modalHeight = Math.max(labels.length * barHeight * 2 + 80, 400);
                ctx.parentElement.style.height = modalHeight + 'px';
                ctx.style.height = modalHeight + 'px';

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Public Schools', data: publicData, backgroundColor: 'rgba(37,99,235,0.85)', borderRadius: { topLeft:6, bottomLeft:6, topRight:0, bottomRight:0 }, borderSkipped: false, maxBarThickness: 32 },
                            { label: 'Private Schools', data: privateData, backgroundColor: 'rgba(125,211,252,0.85)', borderRadius: { topLeft:0, bottomLeft:0, topRight:6, bottomRight:6 }, borderSkipped: false, maxBarThickness: 32 }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 80, top: 10, bottom: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)', padding: 12, cornerRadius: 8,
                                callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()}` }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true, beginAtZero: true,
                                grid: { color: 'rgba(148,163,184,0.1)' },
                                title: { display: true, text: 'ENROLLMENT COUNT', font: { size: 12, weight: 'bold' }, color: '#475569', padding: { top: 8 } },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v.toLocaleString() }
                            },
                            y: {
                                stacked: true, grid: { display: false },
                                title: { display: window.innerWidth >= 640, text: 'DISCIPLINE', font: { size: 12, weight: 'bold' }, color: '#475569' },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.6, barPercentage: 0.7
                            }
                        }
                    },
                    plugins: [{
                        id: 'modalValueLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                if (!meta || !meta.data || meta.data.length === 0) return;
                                ctx.save();
                                ctx.font = 'bold 12px Arial, sans-serif';
                                meta.data.forEach((element, index) => {
                                    const value = dataset.data[index];
                                    if (value && value > 0) {
                                        const barWidth = Math.abs(element.x - element.base);
                                        const valueText = value.toLocaleString();
                                        if (barWidth > 40) {
                                            const centerX = element.base + (barWidth / 2);
                                            const y = element.y;
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(valueText, centerX, y);
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(valueText, centerX, y);
                                        }
                                    }
                                });
                                ctx.restore();
                            });
                        }
                    }]
                });
            },

            // ── Discipline Enrollment Modal Chart ─────────────────────────
            renderDisciplineEnrollmentModal() {
                const ctx = document.getElementById('disciplineEnrollmentChartModal');
                if (!ctx || this.enrollmentData.length === 0) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                const sorted = [...this.enrollmentData].sort((a, b) => b.count - a.count);
                const labels = sorted.map(d => d.discipline);
                const counts = sorted.map(d => d.count);
                const colors = this.generateBlueGradientColors(sorted.length);

                new Chart(ctx, {
                    type: 'bar',
                    data: { labels, datasets: [{ label: 'Enrollment Count', data: counts, backgroundColor: colors, borderRadius: 6, maxBarThickness: 32 }] },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 80, top: 10, bottom: 10, left: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.9)', padding: 12, cornerRadius: 8,
                                callbacks: {
                                    title: (c) => sorted[c[0].dataIndex].discipline,
                                    label: (c) => `Enrollment: ${sorted[c.dataIndex].count.toLocaleString()} students`
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148,163,184,0.1)' },
                                title: { display: true, text: 'ENROLLMENT COUNT', font: { size: 12, weight: 'bold' }, color: '#475569', padding: { top: 8 } },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v.toLocaleString() }
                            },
                            y: {
                                grid: { display: false },
                                title: { display: window.innerWidth >= 640, text: 'DISCIPLINE', font: { size: 12, weight: 'bold' }, color: '#475569' },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.9, barPercentage: 0.95
                            }
                        }
                    },
                    plugins: [{
                        id: 'disciplineModalValueLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                if (!meta || !meta.data || meta.data.length === 0) return;
                                ctx.save();
                                ctx.font = 'bold 12px Arial, sans-serif';
                                meta.data.forEach((element, index) => {
                                    const value = dataset.data[index];
                                    if (value && value > 0) {
                                        const barWidth = Math.abs(element.x - element.base);
                                        const valueText = value.toLocaleString();
                                        if (barWidth > 40) {
                                            const centerX = element.base + (barWidth / 2);
                                            const y = element.y;
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(valueText, centerX, y);
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(valueText, centerX, y);
                                        }
                                    }
                                });
                                ctx.restore();
                            });
                        }
                    }]
                });
            },

            // ── Pie / Doughnut Modal Chart ─────────────────────────────────
            renderPieModal() {
                const isMobile = window.innerWidth < 640;
                const canvasId = isMobile ? 'disciplineMarketShareChartModalMobile' : 'disciplineMarketShareChartModal';
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                // ── Sort highest → lowest, fixed Deep Blue per discipline ──
                const sortedEntries = Object.entries(this.disciplineShares)
                    .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]));
                const rawLabelsModal = sortedEntries.map(e => e[0]);
                const labels        = sortedEntries.map(e => this.formatDisciplineName(e[0]));
                const data          = sortedEntries.map(e => parseFloat(e[1]));
                const colors        = rawLabelsModal.map(d => getDeepBlueForDiscipline(d));
                const top5Indices   = [0,1,2,3,4].filter(i => i < data.length);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)', padding: 12, cornerRadius: 8,
                                callbacks: { label: (c) => `${c.label}: ${c.parsed.toFixed(1)}%` }
                            },
                            datalabels: {
                                color: '#fff',
                                font: { weight: 'bold', size: 13 },
                                formatter: (value, context) => {
                                    if (top5Indices.includes(context.dataIndex)) {
                                        return value.toFixed(1) + '%';
                                    }
                                    return '';
                                },
                                anchor: 'center',
                                align: 'center',
                                offset: 0
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            },

            generateGradientColors(count, sector = null) {
                let startColor, endColor;
                
                if (sector && sector !== 'all' && this.sectorColors[sector]) {
                    const baseColor = this.sectorColors[sector];
                    const rgb = this.hexToRgb(baseColor);
                    
                    startColor = { 
                        r: Math.round(rgb.r * 0.5),
                        g: Math.round(rgb.g * 0.5), 
                        b: Math.round(rgb.b * 0.5) 
                    };
                    endColor = { 
                        r: Math.min(255, Math.round(rgb.r + (255 - rgb.r) * 0.6)),
                        g: Math.min(255, Math.round(rgb.g + (255 - rgb.g) * 0.6)), 
                        b: Math.min(255, Math.round(rgb.b + (255 - rgb.b) * 0.6)) 
                    };
                } else {
                    startColor = { r: 30, g: 41, b: 59 };
                    endColor = { r: 241, g: 245, b: 249 };
                }
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            hexToRgb(hex) {
                hex = hex.replace('#', '');
                
                return {
                    r: parseInt(hex.substring(0, 2), 16),
                    g: parseInt(hex.substring(2, 4), 16),
                    b: parseInt(hex.substring(4, 6), 16)
                };
            },

            getSectorGradient() {
                if (this.selectedSector === 'all' || !this.sectorColors[this.selectedSector]) {
                    return '#1e293b, #f1f5f9';
                }
                
                const baseColor = this.sectorColors[this.selectedSector];
                const rgb = this.hexToRgb(baseColor);
                
                const darkR = Math.round(rgb.r * 0.5);
                const darkG = Math.round(rgb.g * 0.5);
                const darkB = Math.round(rgb.b * 0.5);
                
                const lightR = Math.min(255, Math.round(rgb.r + (255 - rgb.r) * 0.6));
                const lightG = Math.min(255, Math.round(rgb.g + (255 - rgb.g) * 0.6));
                const lightB = Math.min(255, Math.round(rgb.b + (255 - rgb.b) * 0.6));
                
                return `rgb(${darkR}, ${darkG}, ${darkB}), rgb(${rgb.r}, ${rgb.g}, ${rgb.b}), rgb(${lightR}, ${lightG}, ${lightB})`;
            },

            updateChart() {
                const filteredData = this.getFilteredData();
                
                const ctx = document.getElementById('licensurePassingChart');
                if (!ctx) {
                    // removed error
                    return;
                }
                
                // IMPROVED: Properly destroy existing chart using Chart.js registry
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                // Also destroy our stored reference
                if (this.chart) {
                    try {
                        this.chart.destroy();
                    } catch (e) {
                        // removed warning
                    }
                    this.chart = null;
                }
                
                if (filteredData.length === 0) {
                    // removed debug log
                    return;
                }
                
                // Calculate and store height based on data count
                // For visibility: fewer items = bigger bars
                const dataCount = filteredData.length;
                if (dataCount === 1) {
                    this.chartHeight = 200;
                } else if (dataCount <= 5) {
                    this.chartHeight = dataCount * 80;
                } else if (dataCount <= 10) {
                    this.chartHeight = dataCount * 60;
                } else {
                    this.chartHeight = Math.max(600, dataCount * 40);
                }
                
                filteredData.sort((a, b) => b.passing_rate - a.passing_rate);

                const labels = filteredData.map(d => d.profession);
                const rates = filteredData.map(d => d.passing_rate);
                const colors = this.generateGradientColors(filteredData.length, this.selectedSector);
                
                // Create chart after a small delay
                setTimeout(() => {
                    try {
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Passing Rate',
                                    data: rates,
                                    backgroundColor: colors,
                                    borderRadius: 8,
                                    barThickness: 28,
                                }]
                            },
                            options: {
                                // ENHANCED ANIMATION OPTIONS - Same as enrollment chart
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        let delay = 0;
                                        if (context.type === 'data' && context.mode === 'default') {
                                            delay = context.dataIndex * 30;
                                        }
                                        return delay;
                                    }
                                },
                                animations: {
                                    x: {
                                        duration: 1500,
                                        from: 0,
                                        easing: 'easeOutQuart'
                                    }
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 400
                                        }
                                    }
                                },
                                
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: {
                                        right: 60
                                    }
                                },
                                plugins: {
                                    legend: { 
                                        display: false 
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: false,
                                        callbacks: {
                                            title: (context) => filteredData[context[0].dataIndex].profession,
                                            label: (context) => {
                                                const data = filteredData[context.dataIndex];
                                                const takers = data.takers ? new Intl.NumberFormat('en-US').format(data.takers) : 'N/A';
                                                const passers = data.passers ? new Intl.NumberFormat('en-US').format(data.passers) : 'N/A';
                                                const passingRate = data.passing_rate ? data.passing_rate.toFixed(2) + '%' : 'N/A';
                                                
                                                return [
                                                    `Takers: ${takers}`,
                                                    `Passers: ${passers}`,
                                                    `Passing Rate: ${passingRate}`,
                                                    `Sector: ${data.sector}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        max: 100,
                                        grid: {
                                            color: 'rgba(148, 163, 184, 0.1)',
                                            borderDash: [8, 4]
                                        },
                                        ticks: {
                                            callback: v => v + '%',
                                            stepSize: 20,
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b'
                                        },
                                        title: {
                                            display: true,
                                            text: 'PASSING RATE (%)',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        grid: { display: false },
                                        ticks: {
                                            font: { size: window.innerWidth < 640 ? 10 : 13, weight: 'bold' },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        title: {
                                            display: window.innerWidth >= 640,
                                            text: 'PROFESSIONS',
                                            font: { size: 13, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'valueLabels',
                                afterDatasetsDraw: function(chart) {
                                    const ctx = chart.ctx;
                                    const meta = chart.getDatasetMeta(0);
                                    
                                    if (!meta || !meta.data || meta.data.length === 0) {
                                        return;
                                    }
                                    
                                    ctx.save();
                                    
                                    meta.data.forEach((element, index) => {
                                        const passers = filteredData[index].passers;
                                        const passingRate = filteredData[index].passing_rate;
                                        
                                        const base = element.base;
                                        const x = element.x;
                                        const y = element.y;
                                        
                                        if (passers && passers > 0) {
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 13px Arial, sans-serif';
                                            
                                            const passersText = new Intl.NumberFormat('en-US').format(passers);
                                            const centerX = base + ((x - base) / 2);
                                            
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(passersText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(passersText, centerX, y);
                                        }
                                        
                                        if (passingRate) {
                                            ctx.textAlign = 'left';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 12px Arial, sans-serif';
                                            
                                            const rateText = passingRate.toFixed(2) + '%';
                                            const endX = x + 8;
                                            
                                            ctx.fillStyle = '#1e293b';
                                            ctx.fillText(rateText, endX, y);
                                        }
                                    });
                                    
                                    ctx.restore();
                                }
                            }]
                        });
                        // removed debug log
                    } catch (error) {
                        // removed error
                    }
                }, 50);
            },

            generatePurpleGradientColors(count) {
                // Purple gradient: from dark purple (#581c87) to light purple (#faf5ff)
                const startColor = { r: 88, g: 28, b: 135 };   // purple-900
                const endColor = { r: 250, g: 245, b: 255 };   // purple-50
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            // ==================== GRADUATE FUNCTIONS WITH ANIMATIONS ====================
            

            initOtherCharts() {
                // Other charts initialization can go here if needed
            },

            initDisciplineMarketShareChart() {
                // === PREVENT DOUBLE INITIALIZATION ===
                if (this.chartInitialized.disciplineMarketShare) {
                    // removed debug log
                    return;
                }
                
                const ctx = document.getElementById('disciplineMarketShareChart');
                if (!ctx) {
                    // removed debug log
                    return;
                }

                const data = {
                    labels: ['Business & Admin', 'Education', 'Engineering & Tech', 'IT & Related', 'Medical & Allied', 'Agri & Forestry'],
                    datasets: [{
                        data: [26.4, 21.3, 17.1, 14.7, 15.8, 4.8],
                        backgroundColor: [
                            'rgb(59, 130, 246)',   // blue
                            'rgb(34, 197, 94)',    // green
                            'rgb(249, 115, 22)',   // orange
                            'rgb(239, 68, 68)',    // red
                            'rgb(168, 85, 247)',   // purple
                            'rgb(20, 184, 166)'    // teal
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                };

                this.disciplineMarketShareChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
                
                // === MARK AS INITIALIZED ===
                this.chartInitialized.disciplineMarketShare = true;
                // removed debug log
            },

            // ── Shared builder: fetch data + (re)build the Enrollment Trend chart ──
            async buildEnrollmentTrendChart() {
                const ctx = document.getElementById('enrollmentTrendChart');
                if (!ctx) return;

                // Destroy existing chart
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();
                if (this.enrollmentTrendChart) {
                    try { this.enrollmentTrendChart.destroy(); } catch(e) {}
                    this.enrollmentTrendChart = null;
                }

                this.loadingEnrollmentTrend = true;
                try {
                    const response = await fetch(
                        `/api/discipline-enrollment/trend?year=${encodeURIComponent(this.selectedTrendYear)}&province=${encodeURIComponent(this.selectedTrendProvince)}`
                    );
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const apiData = await response.json();

                    // Update stats
                    this.enrollmentTrendTotals = apiData.totals;
                    // Sanitize data — map raw DB keys to full display names
                    const cleanLabels  = Array.isArray(apiData.disciplines)   ? apiData.disciplines.map(d => this.formatDisciplineName(d)) : [];
                    const cleanPublic  = Array.isArray(apiData.publicSchools)  ? apiData.publicSchools.map(v  => Number(v)  || 0)     : [];
                    const cleanPrivate = Array.isArray(apiData.privateSchools) ? apiData.privateSchools.map(v => Number(v) || 0)      : [];

                    // Update dynamic height via Alpine state
                    this.trendDataCount = cleanLabels.length;

                    // ── Build trendTableData for the mobile inline bar table ──
                    const maxTotal = Math.max(...cleanLabels.map((_, i) => (cleanPublic[i] || 0) + (cleanPrivate[i] || 0)), 1);
                    this.trendTableData = cleanLabels.map((label, i) => {
                        const pub   = cleanPublic[i]  || 0;
                        const priv  = cleanPrivate[i] || 0;
                        const total = pub + priv;
                        return {
                            label,
                            publicPct:        Math.round((pub  / maxTotal) * 1000) / 10,
                            privatePct:       Math.round((priv / maxTotal) * 1000) / 10,
                            publicFormatted:  pub.toLocaleString(),
                            privateFormatted: priv.toLocaleString(),
                            totalFormatted:   total.toLocaleString(),
                        };
                    }).sort((a, b) => (b.publicPct + b.privatePct) - (a.publicPct + a.privatePct));

                    // Wait one tick so Alpine resizes the container before Chart.js measures it
                    await this.$nextTick();

                    setTimeout(() => {
                        this.enrollmentTrendChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: cleanLabels,
                                datasets: [
                                    {
                                        label: 'Public Schools',
                                        data: cleanPublic,
                                        backgroundColor: 'rgba(37, 99, 235, 0.8)',
                                        borderColor: 'rgb(29, 78, 216)',
                                        borderWidth: 0,
                                        borderRadius: { topLeft: 8, topRight: 0, bottomLeft: 8, bottomRight: 0 },
                                        borderSkipped: false
                                    },
                                    {
                                        label: 'Private Schools',
                                        data: cleanPrivate,
                                        backgroundColor: 'rgba(125, 211, 252, 0.8)',
                                        borderColor: 'rgb(56, 189, 248)',
                                        borderWidth: 0,
                                        borderRadius: { topLeft: 0, topRight: 8, bottomLeft: 0, bottomRight: 8 },
                                        borderSkipped: false
                                    }
                                ]
                            },
                            options: {
                                // Same animation as Enrollment by Discipline
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        if (context.type === 'data' && context.mode === 'default') {
                                            return context.dataIndex * 30;
                                        }
                                        return 0;
                                    }
                                },
                                animations: {
                                    x: { duration: 1500, from: 0, easing: 'easeOutQuart' }
                                },
                                transitions: {
                                    active: { animation: { duration: 400 } }
                                },
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: { padding: { right: 100 } },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            font: { size: 13, weight: 'bold' },
                                            color: '#334155',
                                            padding: 15,
                                            usePointStyle: true,
                                            pointStyle: 'rect'
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: true,
                                        callbacks: {
                                            label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()} students`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true,
                                        beginAtZero: true,
                                        grid: { color: 'rgba(148, 163, 184, 0.1)', borderDash: [8, 4] },
                                        ticks: {
                                            font: { size: 14, weight: 'bold' },
                                            color: '#1e293b',
                                            callback: (v) => v.toLocaleString()
                                        },
                                        title: {
                                            display: true,
                                            text: 'NUMBER OF STUDENTS',
                                            font: { size: 15, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        stacked: true,
                                        grid: { display: false },
                                        ticks: {
                                            font: { size: window.innerWidth < 640 ? 10 : 13, weight: 'bold' },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        // Same thickness as Enrollment by Discipline
                                        categoryPercentage: 0.9,
                                        barPercentage: 0.80,
                                        title: {
                                            display: window.innerWidth >= 640,
                                            text: 'DISCIPLINES',
                                            font: { size: 13, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'enrollmentTrendValueLabels',
                                afterDatasetsDraw(chart) {
                                    const ctx = chart.ctx;
                                    chart.data.datasets.forEach((dataset, datasetIndex) => {
                                        const meta = chart.getDatasetMeta(datasetIndex);
                                        if (!meta?.data?.length) return;
                                        if (meta.hidden) return; // skip hidden datasets
                                        ctx.save();
                                        ctx.font = 'bold 13px Arial, sans-serif';
                                        meta.data.forEach((element, index) => {
                                            const value = dataset.data[index];
                                            if (value && value > 0) {
                                                const barWidth = Math.abs(element.x - element.base);
                                                if (barWidth > 40) {
                                                    const centerX = element.base + (barWidth / 2);
                                                    ctx.textAlign = 'center';
                                                    ctx.textBaseline = 'middle';
                                                    ctx.strokeStyle = '#000000';
                                                    ctx.lineWidth = 3;
                                                    ctx.strokeText(value.toLocaleString(), centerX, element.y);
                                                    ctx.fillStyle = '#ffffff';
                                                    ctx.fillText(value.toLocaleString(), centerX, element.y);
                                                }
                                            }
                                        });
                                        ctx.restore();
                                    });
                                }
                            }]
                        });
                    }, 50);

                } catch (error) {
                    // Fall back silently
                } finally {
                    this.loadingEnrollmentTrend = false;
                }
            },

            async initEnrollmentTrendChart() {
                if (this.chartInitialized.enrollmentTrend) return;
                await this.buildEnrollmentTrendChart();
                this.chartInitialized.enrollmentTrend = true;
            },

            // NEW: Load Enrollment Overview Data (for top cards and pie chart)
            async loadEnrollmentOverviewData() {
                if (!this.selectedEnrollmentYear || !this.selectedEnrollmentProvince) {
                    // removed warning
                    return;
                }

                this.loadingPieChart = true;
                try {
                    // removed debug log
                    
                    // Smart fetch: uses Private+Public for all provinces including Davao Region
                    const province = this.selectedEnrollmentProvince;
                    const { privateData, publicData } = await this.fetchEnrollmentByProvince(this.selectedEnrollmentYear, province);

                    // Calculate totals and discipline shares
                    this.calculateEnrollmentMetrics(privateData, publicData);
                    
                    // Update pie chart
                    this.updateDisciplineMarketShareChart();
                    
                } catch (error) {
                    // removed error
                    // Reset to defaults
                    this.totalEnrollees = 0;
                    this.disciplineShares = {};
                    // Still update the chart even with zero data
                    this.updateDisciplineMarketShareChart();
                } finally {
                    this.loadingPieChart = false;
                }
            },

            calculateEnrollmentMetrics(privateData, publicData) {
                // Calculate total enrollees and graduates from discipline data
                let totalEnrolled = 0;
                let totalGraduates = 0;
                
                // Use total_enrolled_sy if available, otherwise sum disciplines
                if (privateData.total_enrolled_sy !== undefined || publicData.total_enrolled_sy !== undefined) {
                    totalEnrolled = (parseInt(privateData.total_enrolled_sy) || 0) + (parseInt(publicData.total_enrolled_sy) || 0);
                } else {
                    // Sum all discipline values as enrollment count
                    Object.values(privateData.disciplines || {}).forEach(count => totalEnrolled += (parseInt(count) || 0));
                    Object.values(publicData.disciplines || {}).forEach(count => totalEnrolled += (parseInt(count) || 0));
                }
                
                // Use total_graduates if available
                if (privateData.total_graduates !== undefined || publicData.total_graduates !== undefined) {
                    totalGraduates = (parseInt(privateData.total_graduates) || 0) + (parseInt(publicData.total_graduates) || 0);
                } else {
                    // Use total enrolled as fallback for projected graduates
                    totalGraduates = totalEnrolled;
                }
                
                this.totalEnrollees = totalEnrolled;

                // Combine private and public discipline data (NO GROUPING - show all individual disciplines)
                const combinedDisciplines = {};
                const allDisciplineKeys = new Set([
                    ...Object.keys(privateData.disciplines || {}),
                    ...Object.keys(publicData.disciplines || {})
                ]);

                allDisciplineKeys.forEach(key => {
                    combinedDisciplines[key] = (privateData.disciplines[key] || 0) + (publicData.disciplines[key] || 0);
                });

                // Calculate percentages for ALL individual disciplines
                const grandTotal = Object.values(combinedDisciplines).reduce((a, b) => a + b, 0);
                
                // Store all disciplines with their percentages (exact values, no rounding)
                this.disciplineShares = {};
                if (grandTotal > 0) {
                    Object.entries(combinedDisciplines).forEach(([discipline, count]) => {
                        // Store the exact percentage without rounding for accurate chart rendering
                        this.disciplineShares[discipline] = (count / grandTotal) * 100;
                    });
                }
            },

            updateDisciplineMarketShareChart() {
                const ctx = document.getElementById('disciplineMarketShareChart');
                if (!ctx) {
                    // removed error
                    return;
                }

                // Destroy existing chart if it exists
                if (this.disciplineMarketShareChart) {
                    this.disciplineMarketShareChart.destroy();
                    this.disciplineMarketShareChart = null;
                }
                // Belt-and-suspenders: destroy any chart Chart.js still has on this canvas
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                // ── Sort highest → lowest, fixed Deep Blue per discipline ──
                const sortedEntries = Object.entries(this.disciplineShares)
                    .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]));
                const rawLabels   = sortedEntries.map(e => e[0]);
                const labels      = sortedEntries.map(e => this.formatDisciplineName(e[0]));
                const data        = sortedEntries.map(e => parseFloat(e[1]));
                const colors      = rawLabels.map(d => getDeepBlueForDiscipline(d));
                const top5Indices = [0,1,2,3,4].filter(i => i < data.length);

                const chartData = {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                };

                this.disciplineMarketShareChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed.toFixed(1)}%`;
                                    }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    // Only show label for top 5
                                    if (top5Indices.includes(context.dataIndex)) {
                                        return value.toFixed(1) + '%';
                                    }
                                    return ''; // Hide label for others
                                },
                                anchor: 'center',
                                align: 'center',
                                offset: 0
                            }
                        },
                        cutout: '60%'
                    },
                    plugins: [ChartDataLabels] // Enable the plugin
                });

                // removed debug log
            },

            async updateProvincialChart() {
                // removed debug log
                // removed debug log
                // removed debug log
                
                try {
                    // Fetch updated data from API
                    const response = await fetch(
                        `/api/provincial-progression?year=${encodeURIComponent(this.selectedProgressionYear)}&province=${encodeURIComponent(this.selectedProgressionProvince)}`
                    );
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    // removed debug log
                    
                    // Update chart if it exists
                    if (this.provincialProgressionChart) {
                        // === SANITIZE DATA - Create clean copies to avoid circular references ===
                        const cleanLabels = Array.isArray(data.disciplines) ? [...data.disciplines] : [];
                        const cleanEnrolled = Array.isArray(data.enrolled) ? data.enrolled.map(val => Number(val) || 0) : [];
                        const cleanProjected = Array.isArray(data.projected) ? data.projected.map(val => Number(val) || 0) : [];
                        
                        // Update data
                        this.provincialProgressionChart.data.labels = cleanLabels;
                        this.provincialProgressionChart.data.datasets[0].data = cleanEnrolled;  // Placeholder
                        this.provincialProgressionChart.data.datasets[1].data = cleanProjected;  // Actual graduates
                        
                        // Trigger update with animation
                        this.provincialProgressionChart.update('active');
                        
                        // removed debug log
                    } else {
                        // removed warning
                        return;  // === PREVENT INFINITE RECURSION - Don't call init again ===
                    }
                    
                    // Update stats totals
                    this.progressionTotals = data.totals || { enrolled: 0, projected: 0 };
                    
                } catch (error) {
                    // removed error
                    alert(`Failed to load provincial progression data: ${error.message}`);
                }
            },

            async updateTrendChart() {
                // Full rebuild so dynamic height + animations always fire
                await this.buildEnrollmentTrendChart();
            }
        }));
    });

// ─── Chart Manager (Block 2) ──────────────────────────────────────────────────
// ═══════════════════════════════════════════════════════════════════════════
// CHART MANAGER - Clean implementation without Alpine.js proxy issues
// ═══════════════════════════════════════════════════════════════════════════
(function() {
    'use strict';
    
    window.cleanChartManager = {
        charts: {},
        
        // Deep clone to remove ALL proxies
        clone(data) {
            if (!data) return null;
            try {
                return JSON.parse(JSON.stringify(data));
            } catch (e) {
                return null;
            }
        },
        
        // Safe array sanitization
        toNumbers(arr) {
            if (!arr) return [];
            const clean = this.clone(arr);
            return Array.isArray(clean) ? clean.map(n => Number(n) || 0) : [];
        },
        
        // Destroy a chart safely
        destroy(chartName) {
            if (this.charts[chartName]) {
                try {
                    this.charts[chartName].destroy();
                } catch (e) {
                    // removed warning
                }
                this.charts[chartName] = null;
            }
        },
        
        // Enrollment Trend Chart
        async enrollmentTrend(year, province) {
            // removed debug log
            
            const canvas = document.getElementById('enrollmentTrendChart');
            if (!canvas) {
                // removed error
                return { public: 0, private: 0, combined: 0 };
            }
            
            this.destroy('enrollmentTrend');
            
            try {
                const res = await fetch(`/api/discipline-enrollment/trend?year=${encodeURIComponent(year)}&province=${encodeURIComponent(province)}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const raw = await res.json();
                const labels = this.clone(raw.disciplines) || [];
                const publicData = this.toNumbers(raw.publicSchools);
                const privateData = this.toNumbers(raw.privateSchools);
                
                this.charts.enrollmentTrend = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Public Schools',
                            data: publicData,
                            backgroundColor: 'rgba(37,99,235,0.8)',
                            borderColor: 'rgb(29,78,216)',
                            borderWidth: 0,
                            borderRadius: {
                                topLeft: 8,
                                topRight: 0,
                                bottomLeft: 8,
                                bottomRight: 0
                            },
                            borderSkipped: false
                        }, {
                            label: 'Private Schools',
                            data: privateData,
                            backgroundColor: 'rgba(125,211,252,0.8)',
                            borderColor: 'rgb(56,189,248)',
                            borderWidth: 0,
                            borderRadius: {
                                topLeft: 0,
                                topRight: 8,
                                bottomLeft: 0,
                                bottomRight: 8
                            },
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { 
                            duration: 750,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: { 
                                stacked: true, 
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                                ticks: {
                                    font: { size: 14, weight: 'bold' },
                                    color: '#1e293b'
                                }
                            },
                            y: { 
                                stacked: true, 
                                grid: { display: false },
                                ticks: {
                                    font: { size: 14, weight: 'bold' },
                                    color: '#1e293b',
                                    autoSkip: false,
                                    padding: 8
                                },
                                // Make bars wider (similar to Discipline Enrollment)
                                categoryPercentage: 0.6,  // Use 90% of space = less gap
                                barPercentage: 0.7        // Bar is 95% of category = wider bars
                            }
                        }
                    },
                    plugins: [{
                        id: 'valueLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            
                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                
                                if (!meta || !meta.data || meta.data.length === 0) {
                                    return;
                                }
                                
                                ctx.save();
                                ctx.font = 'bold 12px Arial, sans-serif';
                                
                                meta.data.forEach((element, index) => {
                                    const value = dataset.data[index];
                                    
                                    if (value && value > 0) {
                                        const barWidth = Math.abs(element.x - element.base);
                                        const valueText = value.toLocaleString();
                                        const textWidth = ctx.measureText(valueText).width;
                                        
                                        // Only show label if there's enough space (at least 40px)
                                        if (barWidth > 40) {
                                            const centerX = element.base + (barWidth / 2);
                                            const y = element.y;
                                            
                                            // Draw value in center of bar segment
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            
                                            // White text with black outline for contrast
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(valueText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(valueText, centerX, y);
                                        }
                                    }
                                });
                                
                                ctx.restore();
                            });
                        }
                    }]
                });
                
                // removed debug log
                return raw.totals || { public: 0, private: 0, combined: 0 };
                
            } catch (e) {
                // removed error
                return { public: 0, private: 0, combined: 0 };
            }
        }
    };
    
    // removed debug log
})();

// ─── Alpine Chart Override (Block 3) ─────────────────────────────────────────
// ═══════════════════════════════════════════════════════════════════════════
// OVERRIDE Alpine.js chart functions to use the clean manager
// ═══════════════════════════════════════════════════════════════════════════
document.addEventListener('alpine:init', () => {
    // Wait for Alpine to be ready, then override the functions
    setTimeout(() => {
        const alpineComponent = Alpine.$data(document.querySelector('[x-data="licensureChartData()"]'));
        if (alpineComponent) {
            // removed debug log
            
            // initEnrollmentTrendChart and updateTrendChart now use buildEnrollmentTrendChart()
            // No override needed — cleanChartManager is no longer used for enrollment trend.
            
            // removed debug log
        } else {
            // removed error
        }
    }, 500);
});

// ─── Global Exports (required because Vite wraps modules in a private scope) ──
// licensureChartData is registered via Alpine.data() inside block 1, so Alpine
// handles it internally. The only function referenced outside the module scope
// that needs window. exposure is defined inside the IIFE/Alpine blocks.
// Expose the chart manager for any inline usage.
window.cleanChartManager = window.cleanChartManager || {};