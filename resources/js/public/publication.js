// ─── LMI Publication — Public JS ────────────────────────────────────────────
// Blade PHP values are injected via window._publicationData (set inline in the blade).

document.addEventListener('alpine:init', () => {
    Alpine.data('communicatorSection', () => {
        const { issues, weeklyIssues } = window._publicationData;

        return {
            // ─── State ────────────────────────────────────────────────────────
            activeGroups: {},   // open/closed per group id
            groupYears:   {},   // active CY year per group id
            selectedMonth: '',
            zoomImage:    null, // holds the imageUrl currently shown in lightbox
            imgScale:     0.3,  // zoom level for lightbox image
            panX:         0,    // horizontal pan offset (px)
            panY:         0,    // vertical pan offset (px)
            isDragging:   false,
            dragStartX:   0,
            dragStartY:   0,

            // ─── Data (injected from blade) ───────────────────────────────────
            issues,
            weeklyIssues,

            // ─── Helpers ──────────────────────────────────────────────────────

            // Returns all years that have data for a given group (sorted descending)
            getYearsForGroup(groupId) {
                const fromIssues = this.issues.filter(i => i.groupId === groupId).map(i => i.year);
                const fromWeekly = this.weeklyIssues.filter(i => i.groupId === groupId).map(i => i.year);
                return [...new Set([...fromIssues, ...fromWeekly])].sort((a, b) => String(b).localeCompare(String(a)));
            },

            // Get the currently active year for a group (defaults to most recent)
            getGroupYear(groupId) {
                if (this.groupYears[groupId] !== undefined) return this.groupYears[groupId];
                const years = this.getYearsForGroup(groupId);
                return years.length > 0 ? years[0] : '';
            },

            // Set active year for a specific group
            setGroupYear(groupId, year) {
                this.groupYears[groupId] = year;
                this.groupYears = { ...this.groupYears };
                this.selectedMonth = '';
            },

            // Toggle accordion open/close, initialise group year on first open
            toggleGroup(groupId) {
                this.activeGroups = { ...this.activeGroups, [groupId]: !this.activeGroups[groupId] };
                if (this.activeGroups[groupId] && this.groupYears[groupId] === undefined) {
                    const years = this.getYearsForGroup(groupId);
                    if (years.length > 0) this.setGroupYear(groupId, years[0]);
                }
            },

            getIssues(year, groupId) {
                return this.issues.filter(i => i.year === year && i.groupId === groupId);
            },

            getMonthByYear(year, groupId) {
                const filtered = this.weeklyIssues.filter(i => String(i.year) === String(year) && i.groupId === groupId);
                const monthMap = {};
                filtered.forEach(issue => {
                    if (!monthMap[issue.month]) {
                        monthMap[issue.month] = { month: issue.month, order: issue.monthOrder, issues: [] };
                    }
                    monthMap[issue.month].issues.push(issue);
                });
                return Object.values(monthMap).sort((a, b) => a.order - b.order);
            },

            // Groups weekly issues by month, returns array of { month, issues[] }
            getWeeklyByMonth(year, groupId) {
                const filtered = this.weeklyIssues.filter(i => String(i.year) === String(year) && i.groupId === groupId);
                const monthMap = {};
                filtered.forEach(issue => {
                    if (!monthMap[issue.month]) {
                        monthMap[issue.month] = { month: issue.month, order: issue.monthOrder, issues: [] };
                    }
                    monthMap[issue.month].issues.push(issue);
                });
                return Object.values(monthMap).sort((a, b) => a.order - b.order);
            },

            driveThumbnailUrl(fileId) {
                if (!fileId || fileId.startsWith('REPLACE')) return '';
                return `https://drive.google.com/thumbnail?id=${fileId}&sz=s500`;
            },

            driveViewUrl(fileId) {
                return `https://drive.google.com/file/d/${fileId}/view?usp=sharing`;
            },

            driveDownloadUrl(fileId) {
                return `https://drive.google.com/uc?export=download&id=${fileId}`;
            },

            // Returns the most recent issue for a group (used for card header text)
            getFirstIssue(groupId) {
                const years = this.getYearsForGroup(groupId);
                if (years.length === 0) return null;
                return this.issues.find(i => i.groupId === groupId && i.year === years[0]) || null;
            },

            // Auto-picks the most recent issue's Drive thumbnail for the group banner.
            // For weekly groups, falls back to the most recent week's imageUrl.
            getGroupBannerUrl(groupId, frequency) {
                const years = this.getYearsForGroup(groupId);
                if (years.length === 0) return '';
                const latestYear = years[0];
                if (frequency !== 'Weekly') {
                    const issue = this.issues.find(i => i.groupId === groupId && i.year === latestYear);
                    return issue ? this.driveThumbnailUrl(issue.driveFileId) : '';
                } else {
                    const weeklies = this.weeklyIssues
                        .filter(i => i.groupId === groupId && i.year === latestYear && i.imageUrl)
                        .sort((a, b) => b.monthOrder - a.monthOrder);
                    return weeklies.length > 0 ? weeklies[0].imageUrl : '';
                }
            },
        };
    });
});