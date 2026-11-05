// ─── Area selection (Regional / Province) shared by every section ───────────
// Set by areaSelector(); every /api call below sends it as ?province=...
window.currentProvince = 'Regional';
function provinceQuery() {
    return 'province=' + encodeURIComponent(window.currentProvince);
}

// ─── Labor Force vs Employment Rate: swipeable on phones ─────────────────────
// Phones + more than 8 quarters → the chart gets ~40px per quarter and scrolls
// sideways (opening on the latest quarter); both axes stay pinned on screen.
// Value labels are hidden on phones; tapping a bar shows both values.
const LABOR_SWIPE_MIN_POINTS = 8;
const LABOR_SWIPE_PX_PER_POINT = 56;          // phones: room for every bar value and rate label
const LABOR_DESKTOP_PX_PER_POINT = 64;        // desktop/tablet when scrolling kicks in (room for every value)
const LABOR_DESKTOP_MIN_PX_PER_POINT = 56;    // desktop/tablet: scroll when a quarter gets less room than this

function laborIsPhone() {
    return window.innerWidth < 640;
}

// Smaller tooltips on phones (all charts); desktop keeps the usual size
if (window.Chart) {
    const tt = Chart.defaults.plugins.tooltip;
    tt.titleFont = () => ({ size: laborIsPhone() ? 11 : 12, weight: 'bold' });
    tt.bodyFont  = () => ({ size: laborIsPhone() ? 10 : 12 });
    tt.padding   = () => (laborIsPhone() ? 6 : 6);
    tt.boxWidth  = () => (laborIsPhone() ? 8 : 12);
    tt.boxHeight = () => (laborIsPhone() ? 8 : 12);
}

// Copies the axis strips of the chart onto small pinned canvases (swipe mode)
function makeStickyAxesPlugin(leftId, rightId) {
  return {
    id: 'stickyAxes_' + leftId,
    afterRender(chart) {
        const left  = document.getElementById(leftId);
        const right = rightId ? document.getElementById(rightId) : null;
        if (!left) return;

        const on      = !!chart.$lmiSwipe && !!chart.chartArea;
        const rightOn = !!right && on && chart.options.scales.y1 && chart.options.scales.y1.display !== false;
        left.style.display  = on ? 'block' : 'none';
        if (right) right.style.display = rightOn ? 'block' : 'none';
        if (!on) return;

        const dpr  = chart.currentDevicePixelRatio || window.devicePixelRatio || 1;
        const top  = 0;                                                                   // from the very top: nothing passes above the strip
        const h    = chart.height - top;                                                  // full height: nothing peeks underneath
        const axisH = Math.min(h, Math.ceil(chart.chartArea.bottom - top + 6));           // copy only the axis numbers
        const draw = (cv, sx, w) => {
            if (w <= 0) return;
            cv.width = Math.round(w * dpr);
            cv.height = Math.round(h * dpr);
            cv.style.width = w + 'px';
            cv.style.height = h + 'px';
            cv.style.top = top + 'px';
            const ctx = cv.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, cv.width, cv.height);
            ctx.drawImage(chart.canvas, sx * dpr, top * dpr, w * dpr, axisH * dpr, 0, 0, cv.width, Math.round(axisH * dpr));
        };
        draw(left, 0, Math.ceil(chart.chartArea.left));
        if (rightOn) draw(right, Math.floor(chart.chartArea.right), Math.ceil(chart.width - chart.chartArea.right));
    }
  };
}
const laborStickyAxesPlugin = makeStickyAxesPlugin('laborAxisLeft', 'laborAxisRight');
const unempStickyAxesPlugin = makeStickyAxesPlugin('unempAxisLeft', null);

// Swipe mode: tooltip as a floating box above the pinned axes, kept inside the visible area
function makeFloatingTooltip(scrollId, tipId) {
  return function ({ chart, tooltip }) {
    const scroll = document.getElementById(scrollId);
    const box    = scroll?.parentElement;
    if (!box) return;

    let el = document.getElementById(tipId);
    if (!el) {
        el = document.createElement('div');
        el.id = tipId;
        el.style.cssText = 'position:absolute; z-index:30; pointer-events:none; background:rgba(15,23,42,0.94); color:#fff;'
            + 'border-radius:6px; padding:4px 6px; font:500 10px Inter, system-ui, sans-serif; line-height:1.4;'
            + 'white-space:nowrap; box-shadow:0 4px 12px rgba(0,0,0,0.25); display:none;';
        box.appendChild(el);
    }

    if (!tooltip || tooltip.opacity === 0) { el.style.display = 'none'; return; }

    const swatch = c => `<span style="display:inline-block; width:9px; height:9px; border-radius:2px; margin-right:5px; vertical-align:-1px; background:${c}"></span>`;
    const lines = (tooltip.body || []).map((b, i) => {
        const col = tooltip.labelColors?.[i]?.backgroundColor || '#fff';
        return `<div>${swatch(col)}${b.lines.join(' ')}</div>`;
    }).join('');
    el.innerHTML = `<div style="font-weight:700; margin-bottom:2px;">${(tooltip.title || []).join(' ')}</div>${lines}`;
    el.style.display = 'block';

    // keep it between the two pinned axes, just above the chart area
    const leftPin  = Math.ceil(chart.chartArea.left);
    const rightPin = Math.ceil(chart.width - chart.chartArea.right);
    const visibleX = tooltip.caretX - scroll.scrollLeft;
    const w = el.offsetWidth;
    const minX = leftPin + 4;
    const maxX = box.clientWidth - rightPin - w - 4;
    el.style.left = Math.max(minX, Math.min(maxX, visibleX - w / 2)) + 'px';
    el.style.top  = Math.max(0, chart.chartArea.top + 4) + 'px';
}

// Swipe mode: legend outside the scrolling area so it never scrolls away.
// Tapping an item hides/shows that series, like the normal legend.
function laborRenderSwipeLegend(chart, show, elId = 'laborSwipeLegend') {
    const el = document.getElementById(elId);
    if (!el) return;
    el.style.display = show ? 'flex' : 'none';
    if (!show) return;

    el.innerHTML = '';
    chart.data.datasets.forEach((ds, i) => {
        const visible = chart.isDatasetVisible(i);
        const color = typeof ds.borderColor === 'string' && ds.type === 'line' ? ds.borderColor
                    : (typeof ds.backgroundColor === 'string' ? ds.backgroundColor : '#1e3a5f');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'flex items-center gap-1.5 focus:outline-none';
        btn.style.opacity = visible ? '1' : '0.4';
        btn.style.textDecoration = visible ? 'none' : 'line-through';
        btn.setAttribute('aria-pressed', String(visible));
        btn.innerHTML = `<span style="width:10px; height:10px; border-radius:50%; background:${color}; display:inline-block;"></span>${ds.label}`;
        btn.addEventListener('click', () => {
            chart.setDatasetVisibility(i, !chart.isDatasetVisible(i));
            chart.update();
            laborRenderSwipeLegend(chart, true, elId);
        });
        el.appendChild(btn);
    });
  };
}
const laborFloatingTooltip = makeFloatingTooltip('laborChartScroll', 'laborTooltip');
const unempFloatingTooltip = makeFloatingTooltip('unempChartScroll', 'unempTooltip');

function laborHideFloatingTooltip() {
    const el = document.getElementById('laborTooltip');
    if (el) el.style.display = 'none';
}

// ─── Expanded "Labor Force vs Employment Rate" helpers ───────────────────────
const LABOR_MANY_POINTS = 16;   // above this: year-only axis labels + key rate labels only

// Rate labels on long ranges: only first, latest, highest and lowest point
function laborKeyRateIndexes(data) {
    if (!data.length) return new Set();
    let hi = 0, lo = 0;
    data.forEach((v, i) => { if (v > data[hi]) hi = i; if (v < data[lo]) lo = i; });
    return new Set([0, data.length - 1, hi, lo]);
}

// Main chart, long range (desktop/tablet, not swipe mode): key-point labels + year-only axis
function laborIsLongRange(chart) {
    return !chart.$lmiSwipe && chart.data.labels.length > LABOR_MANY_POINTS;
}
function laborIsKeyRate(ctx) {
    return laborKeyRateIndexes(ctx.chart.data.datasets[0].data).has(ctx.dataIndex);
}
function laborYearOnlyTick(value, index) {
    const label = String(this.getLabelForValue(value));
    if (!laborIsLongRange(this.chart)) return label;
    const year = label.split(' ')[0];
    const prev = index > 0 ? String(this.getLabelForValue(index - 1)).split(' ')[0] : null;
    return year !== prev ? year : '';
}


// Expanded window (phones): same swipe behaviour as the main chart
const expandedStickyAxesPlugin = makeStickyAxesPlugin('expandedAxisLeft', null);
const expandedFloatingTooltip  = makeFloatingTooltip('expandedChartScroll', 'expandedTooltip');

const EXPANDED_DESKTOP_PX_PER_POINT = 80;   // expanded window on desktop/tablet: bigger representation

// Does the expanded chart need to scroll? Same rule as the normal view.
function expandedNeedsSwipe(n, phonePx, minPx) {
    const scroll = document.getElementById('expandedChartScroll');
    if (!scroll || !scroll.clientWidth) return false;
    return laborIsPhone() ? n * phonePx > scroll.clientWidth - 50
                          : (scroll.clientWidth - 110) / n < minPx;
}

// Widen the expanded chart for scrolling (before it is created)
function expandedPrepareSwipe(swipe, n, phonePx) {
    const inner = document.getElementById('expandedChartInner');
    if (swipe && inner) inner.style.width = (n * (laborIsPhone() ? phonePx : EXPANDED_DESKTOP_PX_PER_POINT) + 110) + 'px';
}

// After creating an expanded chart: pinned axis + legend, open at the start, text sizes
function expandedFinish(chart, swipe, sizes) {
    const scroll = document.getElementById('expandedChartScroll');
    const n = chart.data.labels.length;
    chart.$lmiSwipe = swipe;
    laborRenderSwipeLegend(chart, swipe, 'expandedLegend');
    if (sizes) sizes(chart, n);
    chart.update('none');
    if (scroll) {
        scroll.scrollLeft = 0;
        if (!scroll.$lmiScrollHide) {
            scroll.addEventListener('scroll', () => { const t = document.getElementById('expandedTooltip'); if (t) t.style.display = 'none'; }, { passive: true });
            scroll.$lmiScrollHide = true;
        }
    }
}

// Back to a normal, non-scrolling expanded chart (used before drawing any expanded chart / on close)
function expandedResetSwipe() {
    const inner = document.getElementById('expandedChartInner');
    const scroll = document.getElementById('expandedChartScroll');
    if (inner) inner.style.width = '100%';
    if (scroll) scroll.scrollLeft = 0;
    ['expandedAxisLeft', 'expandedTooltip'].forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
    const legend = document.getElementById('expandedLegend');
    if (legend) legend.style.display = 'none';
}

// ─── Text size follows the screen AND how many quarters are shown ─────────────
// Few quarters → more room → bigger text. Recalculated on every update/resize.
function chartTextScale(n) {
    return n <= 2 ? 1.35 : n <= 4 ? 1.25 : n <= 8 ? 1.1 : 1;
}
function chartFontSize(phone, tablet, desktop, n) {
    const w = window.innerWidth;
    const base = w < 640 ? phone : w < 1024 ? tablet : desktop;
    return Math.round(base * chartTextScale(n));
}
function setFontSize(holder, key, size) {
    if (!holder) return;
    const cur = holder[key];
    holder[key] = Object.assign({}, (cur && typeof cur === 'object') ? cur : {}, { size });
}

// Turns swipe mode on/off for the current data and screen size
function laborChartLayout() {
    const chart  = window.laborChart;
    const inner  = document.getElementById('laborChartInner');
    const scroll = document.getElementById('laborChartScroll');
    const hint   = document.getElementById('laborSwipeHint');
    if (!chart || !inner || !scroll) return;
    if (scroll.clientWidth === 0) return;   // chart on a hidden tab: decide once it is visible (see onResize)

    const n        = chart.data.labels.length;
    const phone    = laborIsPhone();
    // desktop/tablet: scrollable when the quarters don't have room for their values (same idea as phones)
    const tooDense = !phone && (scroll.clientWidth - 110) / n < LABOR_DESKTOP_MIN_PX_PER_POINT;
    const swipe    = (phone && n * LABOR_SWIPE_PX_PER_POINT > scroll.clientWidth - 50) || tooDense;
    chart.$lmiSwipe = swipe;

    const pxPer = phone ? LABOR_SWIPE_PX_PER_POINT : LABOR_DESKTOP_PX_PER_POINT;
    inner.style.width = swipe ? (n * pxPer + 110) + 'px' : '100%';
    if (hint) {
        hint.style.display = swipe ? 'block' : 'none';
        hint.textContent = phone
            ? 'Swipe the chart to see later quarters · tap a bar for values'
            : 'Scroll the chart sideways to see later quarters · hover a bar for values';
    }
    const valueHint = document.getElementById('laborValueHint');
    if (valueHint) {
        valueHint.style.display = swipe ? 'none' : 'block';          // swipe hint already says "tap a bar"
        valueHint.textContent = laborIsPhone() ? 'Tap a bar to see its exact values'
                                               : 'Hover over a bar to see its exact values';
    }

    const sc    = chart.options.scales;
    if (sc.y?.title)    sc.y.title.display  = !phone;

    const longRange = !swipe && n > LABOR_MANY_POINTS;
    const xt = chart.options.scales.x.ticks;
    xt.callback      = laborYearOnlyTick;
    xt.autoSkip      = !(swipe || longRange);
    xt.maxTicksLimit = (swipe || longRange) ? n : (laborIsPhone() ? 3 : window.innerWidth < 1024 ? 6 : 12);
    xt.maxRotation   = (swipe || longRange) ? 0 : 90;
    chart.options.scales.x.grid = longRange
        ? { display: true, drawOnChartArea: false, drawTicks: true, tickLength: 6, color: '#cbd5e1' }   // small quarter tick marks
        : { display: false };
    chart.options.plugins.legend.align   = 'center';
    chart.options.plugins.legend.display = !swipe;          // swipe mode uses the pinned legend above the chart
    laborRenderSwipeLegend(chart, swipe);

    // swipe mode → floating tooltip above the pinned axes; otherwise the normal one
    chart.options.plugins.tooltip.enabled  = !swipe;
    chart.options.plugins.tooltip.external = swipe ? laborFloatingTooltip : undefined;
    laborHideFloatingTooltip();
    if (!scroll.$lmiScrollHide) {
        scroll.addEventListener('scroll', laborHideFloatingTooltip, { passive: true });
        scroll.$lmiScrollHide = true;
    }

    // text sizes: screen + number of quarters
    setFontSize(chart.data.datasets[0].datalabels, 'font', chartFontSize(9, 10, 12, n));    // rate labels
    setFontSize(chart.data.datasets[1].datalabels, 'font', chartFontSize(10, 13, 17, n));   // bar values
    setFontSize(chart.options.scales.x.ticks, 'font', chartFontSize(9, 11, 11, n));
    setFontSize(chart.options.scales.y.ticks, 'font', chartFontSize(9, 11, 11, n));
    setFontSize(chart.options.plugins.legend.labels, 'font', chartFontSize(10, 12, 12, n));

    chart.resize();
    chart.update('none');
    scroll.scrollLeft = 0;   // swipe charts open at the start (earliest quarter)
}

// ─── Key Employment Indicators chart on phones ───────────────────────────────
// The built-in legend (4 rows) is replaced by a compact 2×2 legend under the chart,
// value labels are hidden (tap shows them) and the quarter labels stay straight.
const UNEMP_PX_PER_POINT = 64;   // phones: room for the four value labels of each quarter

function unempChartLayout() {
    const chart  = window.unempChart;
    if (!chart) return;
    const scroll = document.getElementById('unempChartScroll');
    const inner  = document.getElementById('unempChartInner');
    if (scroll && scroll.clientWidth === 0) return;   // hidden tab: decide once visible
    const phone = laborIsPhone();
    const n     = chart.data.labels.length;

    // any screen: if the quarters don't fit at 64px each (room for every value), the chart scrolls sideways
    const swipe = !!(scroll && inner && n * UNEMP_PX_PER_POINT > scroll.clientWidth - 40);
    chart.$lmiSwipe = swipe;
    if (inner) inner.style.width = swipe ? (n * UNEMP_PX_PER_POINT + 60) + 'px' : '100%';

    chart.options.plugins.legend.display = !phone && !swipe;     // otherwise the pinned legend under the chart
    if (chart.options.scales.y?.title) chart.options.scales.y.title.display = !phone;
    // half a quarter of space before the first point, so its labels never cover the axis numbers
    chart.options.scales.x.offset = true;
    const xt = chart.options.scales.x.ticks;
    xt.autoSkip      = !swipe;
    xt.maxRotation   = (phone || swipe) ? 0 : 90;
    xt.maxTicksLimit = swipe ? n : (phone ? 4 : (window.innerWidth < 1024 ? 6 : 12));

    chart.options.plugins.tooltip.enabled  = !swipe;
    chart.options.plugins.tooltip.external = swipe ? unempFloatingTooltip : undefined;
    const tip = document.getElementById('unempTooltip');
    if (tip) tip.style.display = 'none';
    if (scroll && !scroll.$lmiScrollHide) {
        scroll.addEventListener('scroll', () => { const t = document.getElementById('unempTooltip'); if (t) t.style.display = 'none'; }, { passive: true });
        scroll.$lmiScrollHide = true;
    }

    const legend = document.getElementById('unempLegend');
    const hint   = document.getElementById('unempValueHint');
    if (hint) {
        hint.style.display = (phone || swipe) ? 'block' : 'none';
        hint.textContent = !swipe ? 'Tap the chart to see exact values'
                         : phone  ? 'Swipe the chart to see later quarters · tap for details'
                                  : 'Scroll the chart sideways to see later quarters · hover for details';
    }
    if (legend) {
        const showLegend = phone || swipe;
        legend.style.display = showLegend ? 'grid' : 'none';
        legend.style.gridTemplateColumns = phone ? 'repeat(2, minmax(0, 1fr))' : 'repeat(4, auto)';
        legend.style.justifyContent = phone ? '' : 'center';
        if (showLegend) {
            legend.innerHTML = '';
            chart.data.datasets.forEach((ds, i) => {
                const visible = chart.isDatasetVisible(i);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'flex items-center gap-1.5 text-left focus:outline-none';
                btn.style.opacity = visible ? '1' : '0.4';
                btn.style.textDecoration = visible ? 'none' : 'line-through';
                btn.setAttribute('aria-pressed', String(visible));
                btn.innerHTML = `<span style="width:9px; height:9px; border-radius:50%; flex-shrink:0; background:${ds.borderColor};"></span>`
                    + `<span>${String(ds.label).toLowerCase().replace(/\b\w/g, c => c.toUpperCase())}</span>`;
                btn.addEventListener('click', () => {
                    chart.setDatasetVisibility(i, !chart.isDatasetVisible(i));
                    chart.update();
                    unempChartLayout();
                });
                legend.appendChild(btn);
            });
        }
    }
    // text sizes: screen + number of quarters
    chart.data.datasets.forEach(ds => setFontSize(ds.datalabels, 'font', chartFontSize(8, 10, 11, n)));
    setFontSize(chart.options.plugins.datalabels, 'font', chartFontSize(8, 10, 11, n));
    setFontSize(chart.options.scales.x.ticks, 'font', chartFontSize(9, 11, 12, n));
    setFontSize(chart.options.scales.y.ticks, 'font', chartFontSize(9, 11, 12, n));
    setFontSize(chart.options.plugins.legend.labels, 'font', chartFontSize(9, 11, 11, n));

    chart.resize();
    chart.update('none');
    if (scroll) scroll.scrollLeft = 0;   // swipe charts open at the start (earliest quarter)
}
let unempLayoutTimer = null;

let laborLayoutTimer = null;
window.addEventListener('resize', () => {
    clearTimeout(laborLayoutTimer);
    laborLayoutTimer = setTimeout(laborChartLayout, 150);
    clearTimeout(unempLayoutTimer);
    unempLayoutTimer = setTimeout(unempChartLayout, 150);
}, { passive: true });

function statsFilter() {
            return {
                tableExpanded: false,
                province: 'Regional',
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

                // Area changed (Regional / Province): reload like a first visit
                async onProvinceChanged(province) {
                    this.province = province;
                    this.startYear = '';
                    this.endYear = '';
                    this.filteredData = [];
                    await this.init();
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch(`/api/available-years?${provinceQuery()}`);
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
                        if ((stat.province ?? 'Regional') !== this.province) return false;
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

                // Export names follow the selected area:
                //   Regional   → "DAVAO REGIONAL LABOR MARKET SITUATION", davao-labor-statistics-…
                //   Davao City → "DAVAO CITY LABOR MARKET SITUATION",     davao-city-labor-statistics-…
                exportNames() {
                    const isRegional = this.province === 'Regional';
                    const slug = isRegional ? 'davao' : this.province.toLowerCase().replace(/\s+/g, '-');
                    return {
                        title:    isRegional ? 'DAVAO REGIONAL LABOR MARKET SITUATION' : `${this.province.toUpperCase()} LABOR MARKET SITUATION`,
                        subtitle: isRegional ? 'Consolidated Regional Employment Statistics' : `Consolidated ${this.province} Employment Statistics`,
                        file:     `${slug}-labor-statistics-${this.startYear}-${this.endYear}`,
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
                        a.download = `${this.exportNames().file}.csv`;
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
                    setCell(0, 0, this.exportNames().title, 's', titleStyle);
                    for (let c = 1; c < COLS; c++) setCell(0, c, '', 's', titleStyle);

                    // ── ROW 1: Sub-title (merged A2:I2) ──────────────────────────────────
                    setCell(1, 0, `${this.exportNames().subtitle}  |  ${this.startYear} – ${this.endYear}  |  In Thousands`, 's', subTitleStyle);
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
                    XLSX.writeFile(wb, `${this.exportNames().file}.xlsx`);

                    showToast(`Export complete: ${this.exportNames().file}.xlsx`, 'success', 3500);
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
                province: 'Regional',

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

                // Area changed (Regional / Province): reload like a first visit
                async onProvinceChanged(province) {
                    this.province = province;
                    this.allPeriods = [];
                    this.availableYears = [];
                    this.availableMonths = [];
                    this.selectedYear = '';
                    this.selectedMonth = '';
                    this.pendingYear = '';
                    this.pendingMonth = '';
                    await this.init();
                },

                // For a province, "The Region's" / "The country's" in the analysis
                // text becomes e.g. "Davao City's". Stored templates are not changed.
                areaText(text) {
                    if (!text || this.province === 'Regional') return text;
                    return text.replace(
                        /\b(?:the\s+)?(?:davao\s+)?(?:region|country)(?:['’]|&#39;|&rsquo;)s\b/gi,
                        `${this.province}'s`
                    );
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
                        const response = await fetch(`/api/kpi-cards/periods?${provinceQuery()}`);
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
                        const response = await fetch(`/api/kpi-cards?${provinceQuery()}`);
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
                            `/api/kpi-cards?year=${this.selectedYear}&month=${this.selectedMonth}&${provinceQuery()}`
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
                    // "April 2025" for quarters, just "2025" for annual (provincial) data
                    const per = (y) => String(this.selectedMonth) === '0' ? `${y}` : `${monthName} ${y}`;

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
                                        const prevRes = await fetch(`/api/kpi-cards?year=${prevYear}&month=${this.selectedMonth}&${provinceQuery()}`);
                                        const prevResult = await prevRes.json();
                                        if (prevResult.success) prev = prevResult.data;
                                    } catch (_) {}

                                    // If no previous year data, skip templates entirely — show standalone sentences
                                    if (!prev) {
                                        this.analysis.employment      = `The employment rate in ${b(per(currentYear))} was estimated at ${b(cur.employment_rate.rate)}.`;
                                        this.analysis.underemployment = `The underemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.underemployment_rate.rate)}.`;
                                        this.analysis.unemployment    = `The unemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.unemployment_rate.rate)}.`;
                                        this.analysis.lfpr            = `The country's labor force participation rate (LFPR) in ${b(per(currentYear))} was recorded at ${b(cur.participation_rate.rate)}.`;
                                        return;
                                    }

                                    const replacePlaceholders = (text, currentRate, previousRate, trendWord) => {
                                        return text
                                            .replace(/\{current_period\}/g,  `<strong>${per(currentYear)}</strong>`)
                                            .replace(/\{previous_period\}/g, `<strong>${per(prevYear)}</strong>`)
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
                        const response = await fetch(`/api/kpi-cards?year=${prevYear}&month=${this.selectedMonth}&${provinceQuery()}`);
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
                                `The employment rate in ${b(per(currentYear))} was estimated at ${b(cur.employment_rate.rate)}. This was ${trendBold(empWord)} than the recorded rate in ${b(per(prevYear))} of ${b(prev.employment_rate.rate)}.`;

                            // 2. Underemployment Rate Analysis
                            let underHigher = parseFloat(cur.underemployment_rate.raw_value) >= parseFloat(prev
                                .underemployment_rate.raw_value);
                            let underWord = underHigher ? 'went up' : 'went down';
                            this.analysis.underemployment =
                                `The underemployment rate in ${b(per(currentYear))} ${trendBold(underWord)} to ${b(cur.underemployment_rate.rate)}, from ${b(prev.underemployment_rate.rate)} in ${b(per(prevYear))}.`;

                            // 3. Unemployment Rate Analysis
                            let unempHigher = parseFloat(cur.unemployment_rate.raw_value) >= parseFloat(prev
                                .unemployment_rate.raw_value);
                            let unempWord = unempHigher ? 'rose' : 'dropped';
                            this.analysis.unemployment =
                                `The unemployment rate ${trendBold(unempWord)} to ${b(cur.unemployment_rate.rate)} in ${b(per(currentYear))}, from its rate in ${b(per(prevYear))} of ${b(prev.unemployment_rate.rate)}.`;

                            // 4. LFPR Analysis
                            let lfprHigher = parseFloat(cur.participation_rate.raw_value) >= parseFloat(prev
                                .participation_rate.raw_value);
                            let lfprWord = lfprHigher ? 'higher' : 'lower';
                            this.analysis.lfpr =
                                `The country's labor force participation rate (LFPR) in ${b(per(currentYear))} was recorded at ${b(cur.participation_rate.rate)}, ${trendBold(lfprWord)} than the estimated LFPR in ${b(per(prevYear))} at ${b(prev.participation_rate.rate)}.`;

                        } else {
                            // No previous year data — show standalone analysis without comparison
                            const cur = this.kpiData;
                            const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;

                            this.analysis.employment =
                                `The employment rate in ${b(per(currentYear))} was estimated at ${b(cur.employment_rate.rate)}.`;

                            this.analysis.underemployment =
                                `The underemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.underemployment_rate.rate)}.`;

                            this.analysis.unemployment =
                                `The unemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.unemployment_rate.rate)}.`;

                            this.analysis.lfpr =
                                `The country's labor force participation rate (LFPR) in ${b(per(currentYear))} was recorded at ${b(cur.participation_rate.rate)}.`;
                        }
                    } catch (e) {
                        // Network error fetching prev year — fall back to standalone analysis
                        const cur = this.kpiData;
                        const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;

                        this.analysis.employment =
                            `The employment rate in ${b(per(currentYear))} was estimated at ${b(cur.employment_rate.rate)}.`;

                        this.analysis.underemployment =
                            `The underemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.underemployment_rate.rate)}.`;

                        this.analysis.unemployment =
                            `The unemployment rate in ${b(per(currentYear))} was recorded at ${b(cur.unemployment_rate.rate)}.`;

                        this.analysis.lfpr =
                            `The country's labor force participation rate (LFPR) in ${b(per(currentYear))} was recorded at ${b(cur.participation_rate.rate)}.`;
                    }
                },

                updatePeriodLabel() {
                    const quarterMonths = {
                        '0': 'Annual',
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
                        Q4: 'Oct',
                        Annual: ''        // provincial annual data: label is just the year
                    };
                    return map[q] ?? q;
                },

                province: 'Regional',   // provinces have annual data only (no quarters)
                laborLoading: false,    // loading overlays on the two charts
                unempLoading: false,

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

                // Area changed (Regional / Province): back to first-load defaults, redraw
                async onProvinceChanged(province) {
                    this.province = province;
                    this.laborStartQuarter = 'Q1';
                    this.laborEndQuarter = 'Q4';
                    this.unempStartQuarter = 'Q1';
                    this.unempEndQuarter = 'Q4';
                    await this.fetchAvailableYears();
                    await this.updateLaborChart();
                    await this.updateUnempChart();
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
                    expandedResetSwipe();
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
                    const allLabels = originalChart.data.labels;
                    const many = allLabels.length > LABOR_MANY_POINTS;
                    const keyRates = laborKeyRateIndexes(originalChart.data.datasets[0].data);

                    // same rule as the normal view: scroll when the quarters don't have room for their values
                    expandedResetSwipe();
                    const swipe = expandedNeedsSwipe(allLabels.length, LABOR_SWIPE_PX_PER_POINT, LABOR_DESKTOP_MIN_PX_PER_POINT);
                    expandedPrepareSwipe(swipe, allLabels.length, LABOR_SWIPE_PX_PER_POINT);

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        plugins: [expandedStickyAxesPlugin],
                        data: {
                            labels: originalChart.data.labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: originalChart.data.datasets[0].data,
                                    borderColor: '#3B82F6',
                                    backgroundColor: '#3B82F6',
                                    tension: 0.3,
                                    borderWidth: isMobile ? 2.5 : 4,
                                    // phones + long range: dots only on the labelled key points, so the line stays continuous
                                    pointRadius: (c) => isMobile ? ((many && !swipe && !keyRates.has(c.dataIndex)) ? 0 : 4) : 7,
                                    pointHoverRadius: isMobile ? 5 : 9,
                                    pointBackgroundColor: '#3B82F6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: isMobile ? 2 : 3,
                                    fill: false,
                                    yAxisID: 'y1',
                                    datalabels: {
                                        display: true,
                                        anchor: 'end',
                                        align: 'top',
                                        offset: isMobile ? 4 : 8,
                                        color: '#3B82F6',
                                        backgroundColor: '#ffffff',
                                        borderColor: '#3B82F6',
                                        borderWidth: 1.5,
                                        borderRadius: 4,
                                        padding: isMobile ? 2 : 5,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: isMobile ? 9 : 13
                                        },
                                        formatter: (value, context) => {
                                            if (many && !swipe && !keyRates.has(context.dataIndex)) return null;
                                            return value.toFixed(1) + '%';
                                        }
                                    }
                                },
                                {
                                    type: 'bar',
                                    label: 'Labor Force (thousands)',
                                    data: originalChart.data.datasets[1].data,
                                    backgroundColor: '#1e3a5f',
                                    borderWidth: 0,
                                    borderRadius: { topLeft: 4, topRight: 4 },
                                    borderSkipped: 'bottom',
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: () => swipe || !many,   // hidden only in the desktop long-range overview
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
                                            const step = total > 16 ? 3 : total > 12 ? 2 : 1;
                                            if (swipe) return new Intl.NumberFormat('en-US').format(value);   // scrolling: every bar value
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
                                padding: { top: isMobile ? 16 : 24, bottom: 4, left: 5, right: isMobile ? 28 : 10 }
                            },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: {
                                    display: !swipe,
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
                                    enabled: !swipe,
                                    external: swipe ? expandedFloatingTooltip : undefined,
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
                                        autoSkip: !many,
                                        maxTicksLimit: many ? allLabels.length : (isMobile ? 3 : allLabels.length),
                                        maxRotation: many ? 0 : 90,
                                        minRotation: 0,
                                        // long ranges: show each year once, under its first quarter
                                        callback: function (value, index) {
                                            const label = String(this.getLabelForValue(value));
                                            if (!many || swipe) return label;
                                            const year = label.split(' ')[0];
                                            const prevYear = index > 0 ? String(this.getLabelForValue(index - 1)).split(' ')[0] : null;
                                            return year !== prevYear ? year : '';
                                        },
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: isMobile ? 9 : 13,
                                            weight: '600'
                                        },
                                        padding: 8
                                    },
                                    // small tick marks under every quarter (no vertical lines in the chart)
                                    grid: many
                                        ? { display: true, drawOnChartArea: false, drawTicks: true, tickLength: 6, color: '#cbd5e1' }
                                        : { display: false },
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
                                        stepSize: 0.5,          // 0K, 500K, 1,000K, 1,500K …
                                        maxTicksLimit: 12,
                                        callback: (value) => {
                                            const num = value * 1000;
                                            return new Intl.NumberFormat('en-US').format(num) + (num >= 1000 ? 'K' : '');   // 0, 500, 1,000K, 1,500K …
                                        }
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                },
                                y1: { display: false, position: 'right', suggestedMin: 90, max: 100 }
                            }
                        }
                    });

                    expandedFinish(window.expandedChartInstance, swipe, (c, n) => {
                        // bigger than the normal view; still follows the number of quarters
                        setFontSize(c.data.datasets[0].datalabels, 'font', chartFontSize(9, 12, 13, n));   // rate labels
                        setFontSize(c.data.datasets[1].datalabels, 'font', chartFontSize(11, 15, 19, n));  // bar values
                        setFontSize(c.options.scales.x.ticks, 'font', chartFontSize(9, 12, 13, n));
                        setFontSize(c.options.scales.y.ticks, 'font', chartFontSize(9, 12, 13, n));
                        setFontSize(c.options.plugins.legend.labels, 'font', chartFontSize(10, 13, 14, n));
                    });
                },

                drawExpandedUnemploymentChart() {
                    const ctx = document.getElementById('expandedChart');
                    if (!ctx) return;
                    expandedResetSwipe();

                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                    }

                    const originalChart = window.unempChart;
                    if (!originalChart) return;

                    const isMobile = window.innerWidth < 640;
                    const nPts = originalChart.data.labels.length;
                    // same rule as the normal view: scroll when the quarters don't have room for their values
                    const swipe = expandedNeedsSwipe(nPts, UNEMP_PX_PER_POINT, UNEMP_PX_PER_POINT);
                    expandedPrepareSwipe(swipe, nPts, UNEMP_PX_PER_POINT);

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
                                if (swipe) return value.toFixed(1) + '%';   // scrolling: every value
                                const total = context.chart.data.labels.length;
                                const step = total > 12 ? 3 : total > 6 ? 2 : 1;
                                return context.dataIndex % step === 0 ? value.toFixed(1) + '%' : null;
                            }
                        }
                    }));

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        plugins: [expandedStickyAxesPlugin],
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
                                tooltip: {
                                    enabled: !swipe,
                                    external: swipe ? expandedFloatingTooltip : undefined
                                },
                                legend: {
                                    display: !swipe,                 // scrolling: pinned legend above the chart
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
                                    offset: true,                    // first/last labels clear of the axis
                                    ticks: {
                                        autoSkip: !swipe,
                                        maxTicksLimit: swipe ? nPts : (isMobile ? 3 : 12),
                                        maxRotation: swipe ? 0 : 90,
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

                    expandedFinish(window.expandedChartInstance, swipe, (c, n) => {
                        c.data.datasets.forEach(ds => setFontSize(ds.datalabels, 'font', chartFontSize(8, 11, 12, n)));
                        setFontSize(c.options.scales.x.ticks, 'font', chartFontSize(9, 12, 13, n));
                        setFontSize(c.options.scales.y.ticks, 'font', chartFontSize(9, 12, 13, n));
                        setFontSize(c.options.plugins.legend.labels, 'font', chartFontSize(9, 12, 13, n));
                    });
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch(`/api/available-years?${provinceQuery()}`);

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
                    this.laborYearRange = this.province !== 'Regional'
                        ? `${this.laborStartYear} - ${this.laborEndYear}`
                        : `${this.laborStartYear} ${startMonth} - ${this.laborEndYear} ${endMonth}`;
                },

                updateUnempYearRange() {
                    const startMonth = this.quarterToMonth(this.unempStartQuarter);
                    const endMonth = this.quarterToMonth(this.unempEndQuarter);
                    this.unempYearRange = this.province !== 'Regional'
                        ? `${this.unempStartYear} - ${this.unempEndYear}`
                        : `${this.unempStartYear} ${startMonth} - ${this.unempEndYear} ${endMonth}`;
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

                // Shows the chart's loading overlay if fetching takes longer than 200 ms
                async withLoading(flag, fn) {
                    const timer = setTimeout(() => { this[flag] = true; }, 200);
                    try { return await fn(); }
                    finally { clearTimeout(timer); this[flag] = false; }
                },

                initializeLaborChart() { return this.withLoading('laborLoading', () => this._initializeLaborChart()); },
                updateLaborChart()     { return this.withLoading('laborLoading', () => this._updateLaborChart()); },
                initializeUnempChart() { return this.withLoading('unempLoading', () => this._initializeUnempChart()); },
                updateUnempChart()     { return this.withLoading('unempLoading', () => this._updateUnempChart()); },

                async _initializeLaborChart() {
                    const laborCtx = document.getElementById('laborEmploymentChart');
                    if (!laborCtx) return;

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}?${provinceQuery()}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            labels.push(window.innerWidth < 640 ? `'${String(year).slice(2)} ${this.quarterToMonth(item.quarter)}` : `${year} ${this.quarterToMonth(item.quarter)}`);
                            laborData.push(parseFloat(item.labor_force_thousands) || 0);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                        });
                    }

                    window.laborChart = new Chart(laborCtx.getContext('2d'), {
                        plugins: [laborStickyAxesPlugin],
                        data: {
                            labels: labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: empRateData,
                                    borderColor: '#3B82F6',
                                    backgroundColor: '#3B82F6',
                                    tension: 0.3,
                                    borderWidth: 3,
                                    pointRadius: (c) => laborIsLongRange(c.chart) && !laborIsKeyRate(c) ? 0 : (window.innerWidth < 640 ? 3 : 6),
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
                                        color: '#3B82F6',
                                        backgroundColor: '#ffffff',
                                        borderColor: '#3B82F6',
                                        borderWidth: 1.5,
                                        borderRadius: 4,
                                        padding: 3,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: window.innerWidth < 480 ? 8 : window.innerWidth < 768 ? 9 : 12
                                        },
                                        formatter: (value, context) => {
                                            if (context.chart.$lmiSwipe) return value.toFixed(1) + '%';
                                            if (laborIsLongRange(context.chart)) return laborIsKeyRate(context) ? value.toFixed(1) + '%' : null;
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
                                    backgroundColor: '#1e3a5f',
                                    borderWidth: 0,
                                    borderRadius: { topLeft: 4, topRight: 4 },
                                    borderSkipped: 'bottom',
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: (ctx) => !laborIsLongRange(ctx.chart),
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
                                            if (context.chart.$lmiSwipe) return new Intl.NumberFormat('en-US').format(value);
                                            return context.dataIndex % step === 0 ? new Intl.NumberFormat('en-US').format(value) : null;
                                        }
                                    }
                                }

                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            // re-check scroll mode when the chart becomes visible or changes size
                            onResize: () => {
                                clearTimeout(laborLayoutTimer);
                                laborLayoutTimer = setTimeout(laborChartLayout, 150);
                            },
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
                                        stepSize: 0.5,          // 0K, 500K, 1,000K, 1,500K …
                                        maxTicksLimit: 12,
                                        callback: (value) => {
                                            const num = value * 1000;
                                            return new Intl.NumberFormat('en-US').format(num) + (num >= 1000 ? 'K' : '');   // 0, 500, 1,000K, 1,500K …
                                        }
                                    },
                                    grid: { color: '#f1f5f9', lineWidth: 1 },
                                    border: { display: false }
                                },
                                y1: { display: false, position: 'right', suggestedMin: 90, max: 100 }
                            }
                        }
                    });

                    laborChartLayout();
                },

                async _updateLaborChart() {
                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);
                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.laborStartQuarter);
                    const endQ = quarterToNum(this.laborEndQuarter);

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}?${provinceQuery()}`);
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
                    laborChartLayout();
                },

                async _initializeUnempChart() {
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
                        const response = await fetch(`/api/quarterly/${year}?${provinceQuery()}`);
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
                        plugins: [unempStickyAxesPlugin],
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
                                            if (context.chart.$lmiSwipe) return value.toFixed(1) + '%';   // swipe mode: every value
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
                                            if (context.chart.$lmiSwipe) return value.toFixed(1) + '%';   // swipe mode: every value
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
                                            if (context.chart.$lmiSwipe) return value.toFixed(1) + '%';   // swipe mode: every value
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
                                            if (context.chart.$lmiSwipe) return value.toFixed(1) + '%';   // swipe mode: every value
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
                            onResize: () => {
                                clearTimeout(unempLayoutTimer);
                                unempLayoutTimer = setTimeout(unempChartLayout, 150);
                            },
                            plugins: {
                                tooltip: { enabled: true },
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

                    unempChartLayout();
                },

                async _updateUnempChart() {
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
                        const response = await fetch(`/api/quarterly/${year}?${provinceQuery()}`);
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
                    unempChartLayout();
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
// ─── Regional / Provinces selector (above the Labor Force Survey bar) ───────
function areaSelector() {
    return {
        mode: 'regional',          // 'regional' | 'provinces'
        province: 'Davao City',    // default when "Provinces" is picked
        apply() {
            const area = this.mode === 'regional' ? 'Regional' : this.province;
            if (area === window.currentProvince) return;
            window.currentProvince = area;
            window.dispatchEvent(new CustomEvent('province-changed', { detail: area }));
        }
    };
}

window.areaSelector     = areaSelector;
window.statsFilter      = statsFilter;
window.kpiPeriodFilter  = kpiPeriodFilter;
window.chartFilters     = chartFilters;
window.showToast        = showToast;
window.positionDropdown = positionDropdown;
