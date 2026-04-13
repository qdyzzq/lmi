function statsFilter() {
            return {
                tableExpanded: false,
                allData: window.AppData.regionalStats,
                filteredData: [],
                startYear: '',
                endYear: '',
                availableYears: [],
                loading: false,

                appliedRange: 'Select Range',

                get displayRange() {
                    return this.appliedRange;
                },

                async init() {
                    await this.fetchAvailableYears();

                    if (this.availableYears.length >= 2) {
                        this.endYear = String(this.availableYears[0]);
                        this.startYear = String(this.availableYears[1]);
                    } else if (this.availableYears.length === 1) {
                        this.endYear = String(this.availableYears[0]);
                        this.startYear = String(this.availableYears[0]);
                    }


                    if (this.startYear && this.endYear) {
                        this.appliedRange = `${this.startYear} — ${this.endYear}`;
                    }

                    this.$nextTick(() => {
                        this._applyFilterSilent();
                    });
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/available-years');
                        const result = await response.json();

                        if (result.success) {
                            this.availableYears = result.data;
                        } else {
                            console.error('Failed to fetch years:', result.message);
                            const currentYear = new Date().getFullYear();
                            this.availableYears = [currentYear, currentYear - 1];
                        }
                    } catch (error) {
                        console.error('Error fetching available years:', error);
                        const currentYear = new Date().getFullYear();
                        this.availableYears = [currentYear, currentYear - 1];
                    }
                },

                // ✅ Silent version used on init (no alert, no label update)
                _applyFilterSilent() {
                    if (!this.startYear || !this.endYear) return;

                    this.filteredData = this.allData.filter(stat => {
                        const yearMatch = stat.period.match(/\d{4}/);
                        if (!yearMatch) return false;
                        const year = parseInt(yearMatch[0]);
                        return year >= parseInt(this.startYear) && year <= parseInt(this.endYear);
                    });

                    this.filteredData.sort((a, b) => {
                        const yearA = parseInt(a.period.match(/\d{4}/)[0]);
                        const yearB = parseInt(b.period.match(/\d{4}/)[0]);
                        return yearB - yearA;
                    });
                },


                applyFilter() {
                    if (!this.startYear || !this.endYear) {
                        showToast('Please select both start and end years.', 'warning');
                        return;
                    }

                    if (parseInt(this.startYear) > parseInt(this.endYear)) {
                        showToast('Start year cannot be greater than end year.', 'error');
                        return;
                    }


                    this.appliedRange = `${this.startYear} — ${this.endYear}`;

                    this.loading = true;
                    this._applyFilterSilent();
                    this.loading = false;
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('en-US').format(value);
                },

                formatRate(value) {
                    return parseFloat(value).toFixed(1) + '%';
                },

                formatPeriod(period) {
                    const parts = period.split(/[\s\n]+/);
                    if (parts.length >= 2) {
                        return {
                            month: parts[0],
                            year: parts[1]
                        };
                    }
                    return {
                        month: period,
                        year: ''
                    };
                },

                exportCSV() {
                    // ── Styled XLSX export (SheetJS already loaded in <head>) ──────────────
                    // Falls back to plain CSV if SheetJS is somehow unavailable.
                    if (typeof XLSX === 'undefined') {
                        // ── Plain-CSV fallback ────────────────────────────────────────────
                        const headers = ["Period","Labor Force ('000)","Employed ('000)","Unemployed ('000)","Underemployed ('000)","Emp. Rate","Unemp. Rate","Underemp. Rate","Particip. Rate"];
                        const csvContent = [
                            headers.join(','),
                            ...this.filteredData.map(stat => [
                                `"${stat.period.replace(/\n/g,' ')}"`,
                                stat.labor_force, stat.employed, stat.unemployed, stat.underemployed,
                                parseFloat(stat.emp_rate).toFixed(1)+'%',
                                parseFloat(stat.unemp_rate).toFixed(1)+'%',
                                parseFloat(stat.underemp_rate).toFixed(1)+'%',
                                parseFloat(stat.particip_rate).toFixed(1)+'%'
                            ].join(','))
                        ].join('\n');
                        const blob = new Blob([csvContent], { type: 'text/csv' });
                        const url  = window.URL.createObjectURL(blob);
                        const a    = document.createElement('a');
                        a.href = url;
                        a.download = `regional-statistics-${this.startYear}-${this.endYear}.csv`;
                        a.click();
                        window.URL.revokeObjectURL(url);
                        return;
                    }

                    // ── SheetJS (XLSX) styled export ──────────────────────────────────────
                    const wb  = XLSX.utils.book_new();
                    const ws  = {};
                    const range = { s: { r: 0, c: 0 }, e: { r: 0, c: 0 } };

                    // ── Helper: write a cell ──────────────────────────────────────────────
                    const setCell = (r, c, v, t, s) => {
                        const addr = XLSX.utils.encode_cell({ r, c });
                        ws[addr]   = { v, t: t || 's', s };
                        if (r > range.e.r) range.e.r = r;
                        if (c > range.e.c) range.e.c = c;
                    };

                    // ── Shared style building blocks ──────────────────────────────────────
                    const borderThin = { style: 'thin', color: { rgb: 'CBD5E1' } };
                    const allBorders = { top: borderThin, bottom: borderThin, left: borderThin, right: borderThin };

                    const titleStyle = {
                        font:      { name: 'Calibri', sz: 14, bold: true, color: { rgb: 'FFFFFF' } },
                        fill:      { fgColor: { rgb: '0F172A' }, patternType: 'solid' },
                        alignment: { horizontal: 'center', vertical: 'center', wrapText: true },
                        border:    allBorders
                    };
                    const subTitleStyle = {
                        font:      { name: 'Calibri', sz: 10, italic: true, color: { rgb: '94A3B8' } },
                        fill:      { fgColor: { rgb: '1E293B' }, patternType: 'solid' },
                        alignment: { horizontal: 'center', vertical: 'center' },
                        border:    allBorders
                    };
                    const headerStyle = {
                        font:      { name: 'Calibri', sz: 10, bold: true, color: { rgb: 'FFFFFF' } },
                        fill:      { fgColor: { rgb: '1E3A5F' }, patternType: 'solid' },
                        alignment: { horizontal: 'center', vertical: 'center', wrapText: true },
                        border:    allBorders
                    };
                    const periodStyle = {
                        font:      { name: 'Calibri', sz: 10, bold: true, color: { rgb: '0F172A' } },
                        fill:      { fgColor: { rgb: 'F8FAFC' }, patternType: 'solid' },
                        alignment: { horizontal: 'center', vertical: 'center' },
                        border:    allBorders
                    };
                    const numStyle = {
                        font:      { name: 'Calibri', sz: 10, color: { rgb: '334155' } },
                        fill:      { fgColor: { rgb: 'FFFFFF' }, patternType: 'solid' },
                        alignment: { horizontal: 'right', vertical: 'center' },
                        border:    allBorders,
                        numFmt:    '#,##0'
                    };
                    const numAltStyle = { ...numStyle, fill: { fgColor: { rgb: 'F8FAFC' }, patternType: 'solid' } };
                    const rateStyle = {
                        font:      { name: 'Calibri', sz: 10, bold: true, color: { rgb: '1E40AF' } },
                        fill:      { fgColor: { rgb: 'EFF6FF' }, patternType: 'solid' },
                        alignment: { horizontal: 'center', vertical: 'center' },
                        border:    allBorders,
                        numFmt:    '0.0"%"'
                    };
                    const rateAltStyle = { ...rateStyle, fill: { fgColor: { rgb: 'DBEAFE' }, patternType: 'solid' } };
                    const footerStyle = {
                        font:      { name: 'Calibri', sz: 9, italic: true, color: { rgb: '64748B' } },
                        fill:      { fgColor: { rgb: 'F1F5F9' }, patternType: 'solid' },
                        alignment: { horizontal: 'left', vertical: 'center' },
                        border:    allBorders
                    };

                    const COLS = 9; // A–I

                    // ── ROW 0: Main title (merged A1:I1) ──────────────────────────────────
                    setCell(0, 0, 'DAVAO REGIONAL LABOR MARKET SITUATION', 's', titleStyle);
                    for (let c = 1; c < COLS; c++) setCell(0, c, '', 's', titleStyle);

                    // ── ROW 1: Sub-title (merged A2:I2) ──────────────────────────────────
                    setCell(1, 0, `Consolidated Regional Employment Statistics  |  ${this.startYear} – ${this.endYear}  |  In Thousands`, 's', subTitleStyle);
                    for (let c = 1; c < COLS; c++) setCell(1, c, '', 's', subTitleStyle);

                    // ── ROW 2: blank spacer ───────────────────────────────────────────────
                    for (let c = 0; c < COLS; c++) setCell(2, c, '', 's', {});

                    // ── ROW 3: Column headers ─────────────────────────────────────────────
                    const headers = [
                        'Period',
                        "Labor Force\n",
                        "Employed\n",
                        "Unemployed\n",
                        "Underemployed\n",
                        'Employment\nRate (%)',
                        'Underemployment\nRate (%)',
                        'Unemployment\nRate (%)',
                        'Participation\nRate (%)'
                    ];
                    headers.forEach((h, c) => setCell(3, c, h, 's', headerStyle));

                    // ── ROW 4+: Data rows (alternating shading) ───────────────────────────
                    this.filteredData.forEach((stat, i) => {
                        const r    = 4 + i;
                        const isAlt = i % 2 === 1;
                        const nSty  = isAlt ? numAltStyle  : numStyle;
                        const rSty  = isAlt ? rateAltStyle : rateStyle;
                        const pSty  = isAlt
                            ? { ...periodStyle, fill: { fgColor: { rgb: 'F1F5F9' }, patternType: 'solid' } }
                            : periodStyle;

                        // Period: normalise multiline to "Month Year"
                        const period = stat.period.replace(/[\n\r]+/g, ' ').trim();
                        setCell(r, 0, period,                              's', pSty);
                        setCell(r, 1, parseFloat(stat.labor_force) || 0,   'n', nSty);
                        setCell(r, 2, parseFloat(stat.employed)    || 0,   'n', nSty);
                        setCell(r, 3, parseFloat(stat.unemployed)  || 0,   'n', nSty);
                        setCell(r, 4, parseFloat(stat.underemployed) || 0, 'n', nSty);
                        setCell(r, 5, parseFloat(stat.emp_rate)    || 0,   'n', rSty);
                        setCell(r, 6, parseFloat(stat.underemp_rate) || 0, 'n', rSty);
                        setCell(r, 7, parseFloat(stat.unemp_rate)  || 0,   'n', rSty);
                        setCell(r, 8, parseFloat(stat.particip_rate) || 0, 'n', rSty);
                    });

                    // ── Footer row ────────────────────────────────────────────────────────
                    const footerRow = 4 + this.filteredData.length;
                    setCell(footerRow, 0, 'Source: Philippine Statistics Authority – Labor Force Survey', 's', footerStyle);
                    for (let c = 1; c < COLS; c++) setCell(footerRow, c, '', 's', footerStyle);

                    // ── Sheet range & merges ──────────────────────────────────────────────
                    ws['!ref'] = XLSX.utils.encode_range(range);
                    ws['!merges'] = [
                        { s: { r: 0, c: 0 }, e: { r: 0, c: COLS - 1 } }, // title
                        { s: { r: 1, c: 0 }, e: { r: 1, c: COLS - 1 } }, // subtitle
                        { s: { r: 2, c: 0 }, e: { r: 2, c: COLS - 1 } }, // spacer
                        { s: { r: footerRow, c: 0 }, e: { r: footerRow, c: COLS - 1 } } // footer
                    ];

                    // ── Column widths ─────────────────────────────────────────────────────
                    ws['!cols'] = [
                        { wch: 18 }, // Period
                        { wch: 14 }, // Labor Force
                        { wch: 14 }, // Employed
                        { wch: 14 }, // Unemployed
                        { wch: 16 }, // Underemployed
                        { wch: 14 }, // Emp Rate
                        { wch: 18 }, // Underemp Rate
                        { wch: 16 }, // Unemp Rate
                        { wch: 16 }, // Particip Rate
                    ];

                    // ── Row heights ───────────────────────────────────────────────────────
                    ws['!rows'] = [
                        { hpt: 30 }, // title
                        { hpt: 20 }, // subtitle
                        { hpt: 6  }, // spacer
                        { hpt: 36 }, // header (wrapped text)
                    ];

                    // ── Write & download ──────────────────────────────────────────────────
                    XLSX.utils.book_append_sheet(wb, ws, 'Employment Statistics');
                    XLSX.writeFile(wb, `davao-labor-statistics-${this.startYear}-${this.endYear}.xlsx`);

                    showToast(`Export complete: davao-labor-statistics-${this.startYear}-${this.endYear}.xlsx`, 'success', 3500);
                }
            }
        }

        function kpiPeriodFilter() {
            return {
                availableYears: [],
                availableMonths: [],  // months available for the selected year
                allPeriods: [],       // full list of { year, month } from API
                selectedMonth: '',
                selectedYear: '',
                // ── Pending values: bound to the dropdowns in the UI.
                // Only committed to selectedMonth/selectedYear when Apply Filter is pressed.
                pendingMonth: '',
                pendingYear: '',
                selectedPeriodLabel: 'Loading...',
                loading: false,

                analysis: {
                    employment: '',
                    underemployment: '',
                    unemployment: '',
                    lfpr: ''
                },

                // KPI Data structure
                kpiData: {
                    employment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    unemployment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    underemployment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    participation_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    }
                },

                async init() {
                    await this.fetchAvailableYears();

                    if (this.selectedYear && this.selectedMonth) {
                        await this.applyPeriodFilter();
                    } else {
                        await this.loadLatestKpiData();
                        await this.generateAnalysis();
                    }
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/kpi-cards/periods');
                        const result = await response.json();

                        if (result.success && result.data && result.data.length > 0) {
                            // Store all periods so we can filter months by selected year
                            this.allPeriods = result.data;

                            this.availableYears = [...new Set(result.data.map(p => p.year))].sort((a, b) => b - a);

                            // Set the latest year first, then compute its available months
                            const latest = result.data[0];
                            this.selectedYear  = latest.year.toString();
                            this.pendingYear   = this.selectedYear;
                            this.updateAvailableMonths();

                            // Default to the latest month for that year
                            this.selectedMonth = latest.month.toString();
                            this.pendingMonth  = this.selectedMonth;
                            this.updatePeriodLabel();
                        }
                    } catch (e) {
                        console.error("Failed to load available periods:", e);
                    }
                },

                // Recompute months available for the currently selected year
                updateAvailableMonths() {
                    const year = parseInt(this.pendingYear);
                    this.availableMonths = this.allPeriods
                        .filter(p => p.year === year)
                        .map(p => p.month)
                        .sort((a, b) => a - b);

                    // If the current pendingMonth is not available in the new year, reset to first available
                    if (!this.availableMonths.includes(parseInt(this.pendingMonth))) {
                        this.pendingMonth = this.availableMonths.length
                            ? this.availableMonths[0].toString()
                            : '';
                    }
                },

                async loadLatestKpiData() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/kpi-cards');
                        const result = await response.json();
                        if (result.success) {
                            this.kpiData = result.data;
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                async applyPeriodFilter() {
                    // Commit the pending dropdown values to the applied state
                    this.selectedMonth = this.pendingMonth;
                    this.selectedYear  = this.pendingYear;

                    if (!this.selectedMonth || !this.selectedYear) return;

                    this.updatePeriodLabel();
                    this.loading = true;

                    try {
                        const response = await fetch(
                            `/api/kpi-cards?year=${this.selectedYear}&month=${this.selectedMonth}`
                        );
                        const result = await response.json();

                        if (result.success) {
                            this.kpiData = result.data;
                            await this.generateAnalysis();
                        }
                    } catch (e) {
                        console.error("Error applying period filter:", e);
                    } finally {
                        this.loading = false;
                    }
                },

                async generateAnalysis() {
                    const currentYear = parseInt(this.selectedYear);
                    const prevYear = currentYear - 1;
                    const monthName = this.selectedPeriodLabel.split(' ')[0];

                    // ── Step 1: Try to load saved/published templates first ──
                    try {
                        const tplResponse = await fetch(`/api/analysis-templates?year=${this.selectedYear}&month=${this.selectedMonth}`);
                        if (tplResponse.ok) {
                            const tplResult = await tplResponse.json();
                            if (tplResult.success && tplResult.data) {
                                const d = tplResult.data;
                                // Extract template_text from each key (matches template editor structure)
                                const lfprText       = d.lfpr?.template_text           || '';
                                const employmentText = d.employment?.template_text     || '';
                                const underempText   = d.underemployment?.template_text || '';
                                const unempText      = d.unemployment?.template_text   || '';

                                // Only use saved templates if at least one has content
                                if (lfprText || employmentText || underempText || unempText) {
                                    const cur = this.kpiData;
                                    const b   = (val) => `<span class="font-bold text-slate-900">${val}</span>`;

                                    // Fetch previous year data to resolve {previous_rate} and {trend}
                                    let prev = null;
                                    try {
                                        const prevRes = await fetch(`/api/kpi-cards?year=${prevYear}&month=${this.selectedMonth}`);
                                        const prevResult = await prevRes.json();
                                        if (prevResult.success) prev = prevResult.data;
                                    } catch (_) {}

                                    // If no previous year data, skip templates entirely — show standalone sentences
                                    if (!prev) {
                                        this.analysis.employment      = `The employment rate in ${b(monthName + ' ' + currentYear)} was estimated at ${b(cur.employment_rate.rate)}.`;
                                        this.analysis.underemployment = `The underemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.underemployment_rate.rate)}.`;
                                        this.analysis.unemployment    = `The unemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.unemployment_rate.rate)}.`;
                                        this.analysis.lfpr            = `The country's labor force participation rate (LFPR) in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.participation_rate.rate)}.`;
                                        return;
                                    }

                                    const replacePlaceholders = (text, currentRate, previousRate, trendWord) => {
                                        return text
                                            .replace(/\{current_period\}/g,  `<strong>${monthName} ${currentYear}</strong>`)
                                            .replace(/\{previous_period\}/g, `<strong>${monthName} ${prevYear}</strong>`)
                                            .replace(/\{current_rate\}/g,    `<strong>${currentRate}</strong>`)
                                            .replace(/\{previous_rate\}/g,   `<strong>${previousRate}</strong>`)
                                            .replace(/\{trend\}/g,           `<strong>${trendWord}</strong>`);
                                    };

                                    if (employmentText) {
                                        const empHigher = parseFloat(cur.employment_rate.raw_value) >= parseFloat(prev.employment_rate.raw_value);
                                        this.analysis.employment = replacePlaceholders(
                                            employmentText,
                                            cur.employment_rate.rate,
                                            prev.employment_rate.rate,
                                            empHigher ? 'higher' : 'lower'
                                        );
                                    }

                                    if (underempText) {
                                        const underHigher = parseFloat(cur.underemployment_rate.raw_value) >= parseFloat(prev.underemployment_rate.raw_value);
                                        this.analysis.underemployment = replacePlaceholders(
                                            underempText,
                                            cur.underemployment_rate.rate,
                                            prev.underemployment_rate.rate,
                                            underHigher ? 'went up' : 'went down'
                                        );
                                    }

                                    if (unempText) {
                                        const unempHigher = parseFloat(cur.unemployment_rate.raw_value) >= parseFloat(prev.unemployment_rate.raw_value);
                                        this.analysis.unemployment = replacePlaceholders(
                                            unempText,
                                            cur.unemployment_rate.rate,
                                            prev.unemployment_rate.rate,
                                            unempHigher ? 'rose' : 'dropped'
                                        );
                                    }

                                    if (lfprText) {
                                        const lfprHigher = parseFloat(cur.participation_rate.raw_value) >= parseFloat(prev.participation_rate.raw_value);
                                        this.analysis.lfpr = replacePlaceholders(
                                            lfprText,
                                            cur.participation_rate.rate,
                                            prev.participation_rate.rate,
                                            lfprHigher ? 'higher' : 'lower'
                                        );
                                    }

                                    return; // ✅ Done — saved templates used, skip auto-generation
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('No saved template found, falling back to auto-generation.', e);
                    }

                    // ── Step 2: Fallback — auto-generate from raw KPI numbers ──
                    try {
                        const response = await fetch(`/api/kpi-cards?year=${prevYear}&month=${this.selectedMonth}`);
                        const result = await response.json();

                        if (result.success) {
                            const cur = this.kpiData;
                            const prev = result.data;

                            const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;
                            const trendBold = (text) => `<span class="font-bold text-slate-900">${text}</span>`;

                            // 1. Employment Rate Analysis
                            let empHigher = parseFloat(cur.employment_rate.raw_value) >= parseFloat(prev.employment_rate
                                .raw_value);
                            let empWord = empHigher ? 'higher' : 'lower';
                            this.analysis.employment =
                                `The employment rate in ${b(monthName + ' ' + currentYear)} was estimated at ${b(cur.employment_rate.rate)}. This was ${trendBold(empWord)} than the recorded rate in ${b(monthName + ' ' + prevYear)} of ${b(prev.employment_rate.rate)}.`;

                            // 2. Underemployment Rate Analysis
                            let underHigher = parseFloat(cur.underemployment_rate.raw_value) >= parseFloat(prev
                                .underemployment_rate.raw_value);
                            let underWord = underHigher ? 'went up' : 'went down';
                            this.analysis.underemployment =
                                `The underemployment rate in ${b(monthName + ' ' + currentYear)} ${trendBold(underWord)} to ${b(cur.underemployment_rate.rate)}, from ${b(prev.underemployment_rate.rate)} in ${b(monthName + ' ' + prevYear)}.`;

                            // 3. Unemployment Rate Analysis
                            let unempHigher = parseFloat(cur.unemployment_rate.raw_value) >= parseFloat(prev
                                .unemployment_rate.raw_value);
                            let unempWord = unempHigher ? 'rose' : 'dropped';
                            this.analysis.unemployment =
                                `The unemployment rate ${trendBold(unempWord)} to ${b(cur.unemployment_rate.rate)} in ${b(monthName + ' ' + currentYear)}, from its rate in ${b(monthName + ' ' + prevYear)} of ${b(prev.unemployment_rate.rate)}.`;

                            // 4. LFPR Analysis
                            let lfprHigher = parseFloat(cur.participation_rate.raw_value) >= parseFloat(prev
                                .participation_rate.raw_value);
                            let lfprWord = lfprHigher ? 'higher' : 'lower';
                            this.analysis.lfpr =
                                `The country's labor force participation rate (LFPR) in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.participation_rate.rate)}, ${trendBold(lfprWord)} than the estimated LFPR in ${b(monthName + ' ' + prevYear)} at ${b(prev.participation_rate.rate)}.`;

                        } else {
                            // No previous year data — show standalone analysis without comparison
                            const cur = this.kpiData;
                            const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;

                            this.analysis.employment =
                                `The employment rate in ${b(monthName + ' ' + currentYear)} was estimated at ${b(cur.employment_rate.rate)}.`;

                            this.analysis.underemployment =
                                `The underemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.underemployment_rate.rate)}.`;

                            this.analysis.unemployment =
                                `The unemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.unemployment_rate.rate)}.`;

                            this.analysis.lfpr =
                                `The country's labor force participation rate (LFPR) in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.participation_rate.rate)}.`;
                        }
                    } catch (e) {
                        // Network error fetching prev year — fall back to standalone analysis
                        const cur = this.kpiData;
                        const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;

                        this.analysis.employment =
                            `The employment rate in ${b(monthName + ' ' + currentYear)} was estimated at ${b(cur.employment_rate.rate)}.`;

                        this.analysis.underemployment =
                            `The underemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.underemployment_rate.rate)}.`;

                        this.analysis.unemployment =
                            `The unemployment rate in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.unemployment_rate.rate)}.`;

                        this.analysis.lfpr =
                            `The country's labor force participation rate (LFPR) in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.participation_rate.rate)}.`;
                    }
                },

                updatePeriodLabel() {
                    const quarterMonths = {
                        '1': 'January',
                        '4': 'April',
                        '7': 'July',
                        '10': 'October'
                    };
                    if (this.selectedMonth && this.selectedYear) {
                        this.selectedPeriodLabel = `${quarterMonths[this.selectedMonth]} ${this.selectedYear}`;
                    }
                }
            };
        }

        // Chart Filters 
        function chartFilters() {
            return {
                activeChart: 'labor',
                expandedChart: null,

                quarterToMonth(q) {
                    const map = {
                        Q1: 'Jan',
                        Q2: 'Apr',
                        Q3: 'Jul',
                        Q4: 'Oct'
                    };
                    return map[q] || q;
                },

                // Labor Chart State
                laborAvailableYears: [],
                laborStartYear: '',
                laborEndYear: '',
                laborStartQuarter: 'Q1',
                laborEndQuarter: 'Q4',
                laborYearRange: 'Loading...',
                laborOpen: false,

                // Unemployment Chart State
                unempAvailableYears: [],
                unempStartYear: '',
                unempEndYear: '',
                unempStartQuarter: 'Q1',
                unempEndQuarter: 'Q4',
                unempYearRange: 'Loading...',
                unempOpen: false,

                async init() {
                    await this.fetchAvailableYears();
                    await this.initializeLaborChart();
                    await this.initializeUnempChart();
                },

                // Modal 
                openChartModal(chartType) {
                    this.expandedChart = chartType;
                    document.body.classList.add('overflow-hidden');
                    const navbar = document.querySelector('nav');
                    if (navbar) { navbar.style.zIndex = '-1'; navbar.style.visibility = 'hidden'; }
                    this.$nextTick(() => {
                        if (chartType === 'labor') {
                            this.drawExpandedLaborChart();
                        } else if (chartType === 'unemployment') {
                            this.drawExpandedUnemploymentChart();
                        }
                    });
                },

                closeChartModal() {
                    this.expandedChart = null;
                    document.body.classList.remove('overflow-hidden');
                    const navbar = document.querySelector('nav');
                    if (navbar) { navbar.style.zIndex = ''; navbar.style.visibility = ''; }
                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                        window.expandedChartInstance = null;
                    }
                },

                drawExpandedLaborChart() {
                    const ctx = document.getElementById('expandedChart');
                    if (!ctx) return;

                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                    }

                    const originalChart = window.laborChart;
                    if (!originalChart) return;

                    const isMobile = window.innerWidth < 640;

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        data: {
                            labels: originalChart.data.labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: originalChart.data.datasets[0].data,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    borderWidth: isMobile ? 2 : 4,
                                    pointRadius: isMobile ? 4 : 7,
                                    pointHoverRadius: isMobile ? 6 : 9,
                                    pointBackgroundColor: '#3B82F6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 3,
                                    fill: false,
                                    yAxisID: 'y1',
                                    datalabels: {
                                        display: true,
                                        anchor: 'end',
                                        align: 'top',
                                        offset: isMobile ? 4 : 8,
                                        color: '#1e293b',
                                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                        borderRadius: 4,
                                        padding: isMobile ? 2 : 5,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: isMobile ? 9 : 13
                                        },
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const step = total > 12 ? 3 : total > 6 ? 2 : 1;
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                },
                                {
                                    type: 'bar',
                                    label: 'Labor Force (thousands)',
                                    data: originalChart.data.datasets[1].data,
                                    backgroundColor: function(context) {
                                        const chart = context.chart;
                                        const { ctx, chartArea } = chart;
                                        if (!chartArea) return '#182337';
                                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                        gradient.addColorStop(0, '#3B5175');
                                        gradient.addColorStop(0.5, '#2A3F5F');
                                        gradient.addColorStop(1, '#182337');
                                        return gradient;
                                    },
                                    borderColor: '#4A6FA5',
                                    borderWidth: 1.5,
                                    borderRadius: 4,
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: !isMobile,
                                        anchor: 'center',
                                        align: 'center',
                                        color: '#FFFFFF',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: 'bold',
                                            size: isMobile ? 10 : 14
                                        },
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const step = total > 12 ? 3 : total > 6 ? 2 : 1;
                                            return context.dataIndex % step === 0 ? new Intl.NumberFormat('en-US').format(value) : null;
                                        }
                                    }
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: { top: isMobile ? 25 : 40, bottom: 10, left: 5, right: 10 }
                            },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 10,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: isMobile ? 10 : 14,
                                            weight: '600'
                                        },
                                        color: '#1e293b',
                                        padding: isMobile ? 10 : 20
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.dataset.yAxisID === 'y') {
                                                label += new Intl.NumberFormat('en-US').format(Math.round(context.parsed.y * 1000));
                                            } else {
                                                label += context.parsed.y.toFixed(1) + '%';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: isMobile ? 3 : 12,
                                        maxRotation: 90,
                                        minRotation: 0,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: isMobile ? 9 : 13,
                                            weight: '600'
                                        },
                                        padding: 8
                                    },
                                    grid: { display: false },
                                    border: { color: '#e2e8f0', width: 2 }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: !isMobile,
                                        text: 'Labor Force (thousands)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 13,
                                            weight: '600'
                                        },
                                        padding: 10
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: isMobile ? 9 : 13,
                                            weight: '500'
                                        },
                                        padding: 8,
                                        maxTicksLimit: isMobile ? 4 : 6,
                                        callback: (value) => {
                                            const num = value * 1000;
                                            return isMobile ? (num/1000).toFixed(0)+'k' : new Intl.NumberFormat('en-US').format(num);
                                        }
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                },
                                y1: { display: false, position: 'right', min: 80, max: 100 }
                            }
                        }
                    });
                },

                drawExpandedUnemploymentChart() {
                    const ctx = document.getElementById('expandedChart');
                    if (!ctx) return;

                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                    }

                    const originalChart = window.unempChart;
                    if (!originalChart) return;

                    const isMobile = window.innerWidth < 640;

                    // Alternate top/bottom to prevent label collisions between lines
                    const datasetConfigs = [{
                            label: 'LABOR FORCE PARTICIPATION RATE',
                            color: '#023E8A',
                            dataIndex: 0,
                            align: 'top',
                            offset: isMobile ? 4 : 8
                        },
                        {
                            label: 'EMPLOYMENT RATE',
                            color: '#006400',
                            dataIndex: 1,
                            align: 'bottom',
                            offset: isMobile ? 4 : 8
                        },
                        {
                            label: 'UNDEREMPLOYMENT RATE',
                            color: '#FF8C00',
                            dataIndex: 2,
                            align: 'top',
                            offset: isMobile ? 4 : 8
                        },
                        {
                            label: 'UNEMPLOYMENT RATE',
                            color: '#D30000',
                            dataIndex: 3,
                            align: 'bottom',
                            offset: isMobile ? 4 : 8
                        },
                    ];

                    const datasets = datasetConfigs.map(cfg => ({
                        label: cfg.label,
                        data: originalChart.data.datasets[cfg.dataIndex].data,
                        borderColor: cfg.color,
                        backgroundColor: cfg.color,
                        borderWidth: isMobile ? 2 : 3,
                        pointRadius: isMobile ? 3 : 5,
                        pointBackgroundColor: cfg.color,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0.3,
                        datalabels: {
                            display: true,
                            align: cfg.align,
                            anchor: cfg.align === 'top' ? 'end' : 'start',
                            offset: cfg.offset,
                            color: '#1e293b',
                            backgroundColor: 'rgba(255,255,255,0.92)',
                            borderRadius: 4,
                            padding: isMobile ? { top: 2, bottom: 2, left: 3, right: 3 } : { top: 3, bottom: 3, left: 5, right: 5 },
                            font: {
                                family: 'Inter, system-ui, -apple-system, sans-serif',
                                size: isMobile ? 8 : 12,
                                weight: 'bold'
                            },
                            formatter: (value, context) => {
                                const total = context.chart.data.labels.length;
                                const step = total > 12 ? 3 : total > 6 ? 2 : 1;
                                return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                            }
                        }
                    }));

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: originalChart.data.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: { top: isMobile ? 20 : 40, bottom: isMobile ? 10 : 30, left: 5, right: 10 }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    align: 'center',
                                    labels: {
                                        padding: isMobile ? 10 : 20,
                                        boxWidth: isMobile ? 10 : 14,
                                        boxHeight: isMobile ? 10 : 14,
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: isMobile ? 9 : 12,
                                            weight: '600'
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                datalabels: { display: true }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: isMobile ? 3 : 12,
                                        maxRotation: 90,
                                        minRotation: 0,
                                        padding: isMobile ? 6 : 12,
                                        color: '#475569',
                                        font: {
                                            size: isMobile ? 9 : 13,
                                            weight: '600'
                                        }
                                    },
                                    grid: { display: false },
                                    border: { color: '#e2e8f0', width: 2 }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        padding: isMobile ? 6 : 12,
                                        maxTicksLimit: isMobile ? 4 : 6,
                                        stepSize: 20,
                                        color: '#64748b',
                                        font: {
                                            size: isMobile ? 9 : 13,
                                            weight: '500'
                                        }
                                    },
                                    title: {
                                        display: !isMobile,
                                        text: 'Rate (%)',
                                        color: '#1e293b',
                                        font: { size: 13, weight: '600' },
                                        padding: 10
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/available-years');

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const result = await response.json();

                        if (result.success && result.data) {
                            this.laborAvailableYears = result.data;
                            this.unempAvailableYears = result.data;

                            if (result.data.length >= 2) {
                                this.laborEndYear = result.data[0].toString();
                                this.laborStartYear = result.data[1].toString();
                                this.unempEndYear = result.data[0].toString();
                                this.unempStartYear = result.data[1].toString();
                            } else if (result.data.length === 1) {
                                this.laborEndYear = result.data[0].toString();
                                this.laborStartYear = result.data[0].toString();
                                this.unempEndYear = result.data[0].toString();
                                this.unempStartYear = result.data[0].toString();
                            }

                            this.updateLaborYearRange();
                            this.updateUnempYearRange();
                        } else {
                            console.error('Invalid response format:', result);
                            const currentYear = new Date().getFullYear();
                            this.laborAvailableYears = [currentYear, currentYear - 1];
                            this.unempAvailableYears = [currentYear, currentYear - 1];
                            this.laborEndYear = currentYear.toString();
                            this.laborStartYear = (currentYear - 1).toString();
                            this.unempEndYear = currentYear.toString();
                            this.unempStartYear = (currentYear - 1).toString();
                            this.updateLaborYearRange();
                            this.updateUnempYearRange();
                        }
                    } catch (error) {
                        console.error('Error fetching available years:', error);
                        const currentYear = new Date().getFullYear();
                        this.laborAvailableYears = [currentYear, currentYear - 1];
                        this.unempAvailableYears = [currentYear, currentYear - 1];
                        this.laborEndYear = currentYear.toString();
                        this.laborStartYear = (currentYear - 1).toString();
                        this.unempEndYear = currentYear.toString();
                        this.unempStartYear = (currentYear - 1).toString();
                        this.updateLaborYearRange();
                        this.updateUnempYearRange();
                    }
                },

                updateLaborYearRange() {
                    const startMonth = this.quarterToMonth(this.laborStartQuarter);
                    const endMonth = this.quarterToMonth(this.laborEndQuarter);
                    this.laborYearRange =
                        `${this.laborStartYear} ${startMonth} - ${this.laborEndYear} ${endMonth}`;
                },

                updateUnempYearRange() {
                    const startMonth = this.quarterToMonth(this.unempStartQuarter);
                    const endMonth = this.quarterToMonth(this.unempEndQuarter);
                    this.unempYearRange =
                        `${this.unempStartYear} ${startMonth} - ${this.unempEndYear} ${endMonth}`;
                },

                async applyLaborFilter() {
                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);

                    if (!this.laborStartYear || !this.laborEndYear) {
                        showToast('Please select both start and end years.', 'warning');
                        return;
                    }

                    if (startYear > endYear) {
                        showToast('Start year cannot be greater than end year.', 'error');
                        return;
                    }

                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.laborStartQuarter);
                    const endQ = quarterToNum(this.laborEndQuarter);

                    if (startYear === endYear && startQ > endQ) {
                        showToast('Start quarter cannot be greater than end quarter in the same year.', 'error');
                        return;
                    }

                    this.updateLaborYearRange();
                    this.laborOpen = false;
                    await this.updateLaborChart();
                },

                async applyUnempFilter() {
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);

                    if (!this.unempStartYear || !this.unempEndYear) {
                        showToast('Please select both start and end years.', 'warning');
                        return;
                    }

                    if (startYear > endYear) {
                        showToast('Start year cannot be greater than end year.', 'error');
                        return;
                    }

                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.unempStartQuarter);
                    const endQ = quarterToNum(this.unempEndQuarter);

                    if (startYear === endYear && startQ > endQ) {
                        showToast('Start quarter cannot be greater than end quarter in the same year.', 'error');
                        return;
                    }

                    this.updateUnempYearRange();
                    this.unempOpen = false;
                    await this.updateUnempChart();
                },

                async initializeLaborChart() {
                    const laborCtx = document.getElementById('laborEmploymentChart');
                    if (!laborCtx) return;

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                            laborData.push(parseFloat(item.labor_force_thousands) || 0);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                        });
                    }

                    window.laborChart = new Chart(laborCtx.getContext('2d'), {
                        data: {
                            labels: labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: empRateData,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: window.innerWidth < 640 ? 3 : 6,
                                    pointHoverRadius: window.innerWidth < 640 ? 5 : 8,
                                    pointBackgroundColor: '#3B82F6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    yAxisID: 'y1',

                                    datalabels: {
                                        display: true,
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 4,
                                        color: '#1e293b',
                                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                        borderRadius: 4,
                                        padding: 3,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: window.innerWidth < 480 ? 8 : window.innerWidth < 768 ? 9 : 12
                                        },
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                },
                                {
                                    type: 'bar',
                                    label: 'Labor Force (thousands)',
                                    data: laborData,
                                    backgroundColor: function(context) {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return '#182337';

                                        const gradient = ctx.createLinearGradient(0, chartArea.bottom,
                                            0, chartArea.top);
                                        gradient.addColorStop(0, '#3B5175');
                                        gradient.addColorStop(0.5, '#2A3F5F');
                                        gradient.addColorStop(1, '#182337');
                                        return gradient;
                                    },
                                    borderColor: '#22324D',
                                    borderWidth: 1,
                                    borderRadius: 2,
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: true,
                                        anchor: 'center',
                                        align: 'center',
                                        color: "#FFFFFF",
                                        font: {
                                            weight: 'bold',
                                            size: window.innerWidth < 480 ? 8 : window.innerWidth < 640 ? 9 : window.innerWidth < 1024 ? 13 : 18
                                        },
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? new Intl.NumberFormat('en-US').format(value) : null;
                                        }
                                    }
                                }

                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: window.innerWidth < 640 ? 6 : 12,
                                        font: {
                                            size: window.innerWidth < 640 ? 9 : 12
                                        }
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.dataset.yAxisID === 'y') {
                                                const actualValue = Math.round(context.parsed.y * 1000);
                                                label += new Intl.NumberFormat('en-US').format(actualValue);
                                            } else {
                                                label += context.parsed.y.toFixed(1) + '%';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: window.innerWidth < 640 ? 3 : window.innerWidth < 1024 ? 6 : 12,
                                        maxRotation: 90,
                                        minRotation: 0,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: window.innerWidth < 640 ? 8 : 11,
                                            weight: '600',
                                        },
                                        padding: 4
                                    },
                                    grid: { display: false },
                                    border: { color: '#e2e8f0', width: 2 }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: window.innerWidth >= 640,
                                        text: 'Labor Force (thousands)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 11,
                                            weight: '600',
                                        },
                                        padding: 8
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: window.innerWidth < 640 ? 8 : 11,
                                            weight: '500'
                                        },
                                        padding: 4,
                                        maxTicksLimit: window.innerWidth < 640 ? 4 : 6,
                                        callback: (value) => {
                                            const num = value * 1000;
                                            if (window.innerWidth < 640) {
                                                return num >= 1000 ? (num/1000).toFixed(0)+'k' : num;
                                            }
                                            return new Intl.NumberFormat('en-US').format(num);
                                        }
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                },
                                y1: {
                                    display: false,
                                    position: 'right',
                                    min: 80,
                                    max: 100,
                                }
                            }
                        }
                    });
                },

                async updateLaborChart() {
                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);
                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.laborStartQuarter);
                    const endQ = quarterToNum(this.laborEndQuarter);

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            const itemQ = quarterToNum(item.quarter);

                            if (year > startYear && year < endYear) {
                                labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                                laborData.push(parseFloat(item.labor_force_thousands) ||
                                    0);
                                empRateData.push(parseFloat(item.employment_rate) || 0);
                                return;
                            }

                            if (year === startYear && itemQ < startQ) return;
                            if (year === endYear && itemQ > endQ) return;

                            labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                            laborData.push(parseFloat(item.labor_force_thousands) || 0);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                        });
                    }

                    window.laborChart.data.labels = labels;
                    window.laborChart.data.datasets[1].data = laborData;
                    window.laborChart.data.datasets[0].data = empRateData;
                    window.laborChart.update();
                },

                async initializeUnempChart() {
                    const unempCtx = document.getElementById('unemploymentChart');
                    if (!unempCtx) return;

                    let labels = [];
                    let lfprData = [],
                        empRateData = [],
                        underempData = [],
                        unempRateData = [];
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                            lfprData.push(parseFloat(item.lfpr) || 0);
                            underempData.push(parseFloat(item
                                .underemployment_rate) || 0);
                            unempRateData.push(parseFloat(item.unemployment_rate) ||
                                0);
                        });
                    }

                    window.unempChart = new Chart(unempCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'LABOR FORCE PARTICIPATION RATE',
                                    data: lfprData,
                                    borderColor: '#023E8A',
                                    backgroundColor: '#023E8A',
                                    borderWidth: window.innerWidth < 640 ? 2 : 3,
                                    pointRadius: window.innerWidth < 640 ? 2 : 5,
                                    pointBackgroundColor: '#023E8A',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 6,
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                },
                                {
                                    label: 'EMPLOYMENT RATE',
                                    data: empRateData,
                                    borderColor: '#006400',
                                    backgroundColor: '#006400',
                                    borderWidth: window.innerWidth < 640 ? 2 : 3,
                                    pointRadius: window.innerWidth < 640 ? 2 : 5,
                                    pointBackgroundColor: '#006400',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 6,
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                },
                                {
                                    label: 'UNDEREMPLOYMENT RATE',
                                    data: underempData,
                                    borderColor: '#FF8C00',
                                    backgroundColor: '#FF8C00',
                                    borderWidth: window.innerWidth < 640 ? 2 : 3,
                                    pointRadius: window.innerWidth < 640 ? 2 : 5,
                                    pointBackgroundColor: '#FF8C00',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 6,
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                },
                                {
                                    label: 'UNEMPLOYMENT RATE',
                                    data: unempRateData,
                                    borderColor: '#D30000',
                                    backgroundColor: '#D30000',
                                    borderWidth: window.innerWidth < 640 ? 2 : 3,
                                    pointRadius: window.innerWidth < 640 ? 2 : 5,
                                    pointBackgroundColor: '#D30000',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'bottom',
                                        offset: 6,
                                        formatter: (value, context) => {
                                            const total = context.chart.data.labels.length;
                                            const isMobile = window.innerWidth < 480;
                                            const step = isMobile
                                                ? (total > 10 ? 4 : total > 6 ? 2 : 1)
                                                : (total > 20 ? 4 : total > 10 ? 3 : 1);
                                            return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                                        }
                                    }
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: window.innerWidth < 640 ? 9 : 11,
                                            weight: '600'
                                        },
                                        padding: window.innerWidth < 640 ? 8 : 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    color: '#1e293b',
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    borderRadius: 4,
                                    padding: {
                                        top: window.innerWidth < 480 ? 2 : 4,
                                        bottom: window.innerWidth < 480 ? 2 : 4,
                                        left: window.innerWidth < 480 ? 3 : 6,
                                        right: window.innerWidth < 480 ? 3 : 6
                                    },
                                    font: {
                                        family: 'Inter, system-ui, -apple-system, sans-serif',
                                        size: window.innerWidth < 480 ? 7 : window.innerWidth < 640 ? 9 : 11,
                                        weight: '700'
                                    },
                                    formatter: (value) => value.toFixed(1)
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: window.innerWidth < 640 ? 3 : window.innerWidth < 1024 ? 6 : 12,
                                        maxRotation: 90,
                                        minRotation: 0,
                                        padding: window.innerWidth < 640 ? 4 : 12,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: window.innerWidth < 640 ? 8 : 12,
                                            weight: '600'
                                        }
                                    },
                                    grid: { display: false },
                                    border: { color: '#e2e8f0', width: 2 }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        padding: window.innerWidth < 640 ? 4 : 12,
                                        maxTicksLimit: window.innerWidth < 640 ? 4 : 6,
                                        stepSize: 20,
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: window.innerWidth < 640 ? 8 : 12,
                                            weight: '500'
                                        }
                                    },
                                    title: {
                                        display: window.innerWidth >= 640,
                                        text: 'Rate (%)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 11,
                                            weight: '600'
                                        },
                                        padding: 8
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                },

                async updateUnempChart() {
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);
                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.unempStartQuarter);
                    const endQ = quarterToNum(this.unempEndQuarter);

                    let labels = [];
                    let lfpr = [],
                        emp = [],
                        under = [],
                        unemp = [];
                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;
                        const data = await response.json();

                        data.forEach(item => {
                            const itemQ = quarterToNum(item.quarter);
                            if (year === startYear && itemQ < startQ) return;
                            if (year === endYear && itemQ > endQ) return;

                            labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                            emp.push(parseFloat(item.employment_rate) || 0);
                            lfpr.push(parseFloat(item.lfpr) || 0);
                            under.push(parseFloat(item.underemployment_rate) ||
                                0);
                            unemp.push(parseFloat(item.unemployment_rate) || 0);
                        });
                    }

                    window.unempChart.data.labels = labels;
                    window.unempChart.data.datasets[0].data = lfpr;
                    window.unempChart.data.datasets[1].data = emp;
                    window.unempChart.data.datasets[2].data = under;
                    window.unempChart.data.datasets[3].data = unemp;
                    window.unempChart.update();
                }
            }
        }

        function showToast(message, type = 'error', duration = 4000) {
            const icons = {
                error:   `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                warning: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
                success: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                info:    `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            };
            const titles = { error: 'Error', warning: 'Warning', success: 'Success', info: 'Info' };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                ${icons[type] || icons.info}
                <div class="toast-body">
                    <div class="toast-title">${titles[type] || 'Notice'}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" aria-label="Close">&times;</button>
            `;
            const container = document.getElementById('toast-container');
            container.appendChild(toast);
            const remove = () => {
                toast.classList.add('removing');
                toast.addEventListener('animationend', () => toast.remove(), { once: true });
            };
            toast.querySelector('.toast-close').addEventListener('click', remove);
            setTimeout(remove, duration);
        }

        function positionDropdown(triggerBtn, dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;
            requestAnimationFrame(() => {
                const rect = triggerBtn.getBoundingClientRect();
                const gap = 6, margin = 16;
                if (window.innerWidth < 640) {
                    const w = window.innerWidth - (margin * 2);
                    dropdown.style.width = w + 'px';
                    dropdown.style.left  = margin + 'px';
                    dropdown.style.right = 'auto';
                    dropdown.style.top   = (rect.bottom + gap) + 'px';
                } else {
                    dropdown.style.width = '';
                    const dropW = dropdown.offsetWidth || 320;
                    let left = rect.right - dropW;
                    if (left < margin) left = margin;
                    if (left + dropW > window.innerWidth - margin) left = window.innerWidth - dropW - margin;
                    dropdown.style.left  = left + 'px';
                    dropdown.style.right = 'auto';
                    dropdown.style.top   = (rect.bottom + gap) + 'px';
                }
            });
        }

        window.addEventListener('scroll', () => {
            ['labor', 'unemp', 'kpi-period', 'table-year'].forEach(key => {
                const dd = document.getElementById(key + '-dropdown');
                if (dd && dd.style.display !== 'none' && dd.offsetParent !== null) {
                    dd.style.display = 'none';
                }
            });
            document.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        }, { passive: true });

        window.addEventListener('resize', () => {
            ['labor', 'unemp', 'kpi-period', 'table-year'].forEach(key => {
                const btn = document.getElementById(key + '-filter-btn') || document.getElementById(key + '-btn');
                const dd  = document.getElementById(key + '-dropdown');
                if (btn && dd && dd.style.display !== 'none' && dd.offsetParent !== null) {
                    positionDropdown(btn, key + '-dropdown');
                }
            });
        }, { passive: true });

// ─── Global Exports (required for Alpine x-data and inline @click handlers) ──
window.statsFilter      = statsFilter;
window.kpiPeriodFilter  = kpiPeriodFilter;
window.chartFilters     = chartFilters;
window.showToast        = showToast;
window.positionDropdown = positionDropdown;