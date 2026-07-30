// ─── Job Market Demands — Public JS ─────────────────────────────────────────
// All Blade PHP values are injected via window._jobMarketData (set inline in the blade).

// Toggle role details function
function toggleRoleDetails(submissionId, index) {
    const details = document.getElementById(`role-details-${submissionId}-${index}`);
    const icon = details.previousElementSibling.querySelector('.expand-icon');
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        details.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Prepare comparison data (initial load from server)
let comparisonData = window._jobMarketData.comparisonData;
let currentSelectedYear = window._jobMarketData.selectedYear;

// Build and render the main chart
let mainChart = null;
let expandedChart = null;

function renderMainChart() {
    const ctx = document.getElementById('highVolumeHorizontalChart');
    if (!ctx || !comparisonData.length) return;
    if (mainChart) mainChart.destroy();
    mainChart = new Chart(ctx, buildChartConfig(comparisonData));
}

function buildChartConfig(data, axisSize = null) {
    const isMobile = window.innerWidth < 640;
    const isTablet = window.innerWidth < 1024;
    axisSize = axisSize ?? (isMobile ? 9 : isTablet ? 11 : 12);

    const labels      = data.map(d => d.title);
    const currentData = data.map(d => d.current_count);
    const prevData    = data.map(d => d.previous_count);
    const hasPrev     = prevData.some(v => v && v > 0);

    const datasets = [
        ...(hasPrev ? [{
            label: String(currentSelectedYear - 1),
            data: prevData,
            backgroundColor: 'rgba(16, 185, 129, 0.85)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: isMobile ? 8 : 14,
        }] : []),
        {
            label: String(currentSelectedYear),
            data: currentData,
            backgroundColor: 'rgba(99, 102, 241, 0.9)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: isMobile ? 8 : 14,
        }
    ];

    return {
        type: 'bar',
        data: { labels, datasets },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { right: isMobile ? 8 : 16 }
            },
            plugins: {
                legend: {
                    display: true, position: 'top', align: 'end',
                    labels: { boxWidth: 10, boxHeight: 10, font: { size: axisSize, weight: '500' }, padding: isMobile ? 8 : 15, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)', padding: 12,
                    titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12 },
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: function(context) {
                            let label = (context.dataset.label || '') + ': ';
                            label += context.parsed.x.toLocaleString();
                            if (context.datasetIndex === 1 && comparisonData[context.dataIndex]) {
                                const { change, is_new } = comparisonData[context.dataIndex];
                                if (is_new) label += ' (NEW)';
                                else if (change !== 0) label += ` (${change > 0 ? '+' : ''}${change}%)`;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.03)' },
                    ticks: {
                        font: { size: axisSize },
                        maxTicksLimit: isMobile ? 4 : 6,
                        callback: v => v >= 1000 ? (v/1000)+'k' : v
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: axisSize, weight: '500' },
                        color: '#374151',
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            if (isMobile && label && label.length > 22) {
                                return label.substring(0, 20) + '…';
                            }
                            return label;
                        }
                    }
                }
            },
            interaction: { mode: 'y', intersect: false }
        }
    };
}

// Fetch new chart data when year changes — no page reload
async function updateChart(year) {
    try {
        const res  = await fetch(`/api/job-market/chart-data?year=${year}`);
        const json = await res.json();

        comparisonData      = json.comparison_data;
        currentSelectedYear = json.selected_year;

        // Update subtitle - show only when previous data exists
        const subtitle     = document.getElementById('chartSubtitle');
        const prevLabel    = document.getElementById('prevYearLabel');
        const currentLabel = document.getElementById('currentYearLabel');

        if (prevLabel)    prevLabel.textContent    = json.previous_year;
        if (currentLabel) currentLabel.textContent = json.selected_year;
        if (subtitle)     subtitle.style.display   = json.has_previous_data ? '' : 'none';

        renderMainChart();
    } catch (e) {
        console.error('Chart update failed:', e);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    renderMainChart();
});

// Expand chart function
function expandChart() {
    const modal = document.getElementById('chartModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    // Hide navbar so it doesn't overlap the modal
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '-1';
        navbar.style.visibility = 'hidden';
    }

    if (expandedChart) {
        expandedChart.destroy();
    }
    
    const expandedCtx = document.getElementById('highVolumeExpandedChart');
    if (expandedCtx && comparisonData && comparisonData.length > 0) {
        const expandedSize = window.innerWidth < 640 ? 10 : window.innerWidth < 1024 ? 12 : 14;
        expandedChart = new Chart(expandedCtx, buildChartConfig(comparisonData, expandedSize));
    }
}

// Close chart function
function closeChart() {
    const modal = document.getElementById('chartModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Restore navbar
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '';
        navbar.style.visibility = '';
    }

    if (expandedChart) {
        expandedChart.destroy();
        expandedChart = null;
    }
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeChart();
    }
});

// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {

    // Main application state
const appState = {
    showLmiMatrix: false
};

// Modal functionality
const lmiMatrixModal = document.getElementById('lmi-matrix-modal');
const showLmiMatrixBtn = document.getElementById('show-lmi-matrix-btn');
const closeModalBtn = document.getElementById('close-modal-btn');
const modalBackdrop = document.getElementById('modal-backdrop');
const mainContent = document.getElementById('main-content');

// Confirmation and Success Modals
const confirmationModal = document.getElementById('confirmation-modal');
const successModal = document.getElementById('success-modal');
const lmiForm = document.getElementById('lmi-form');
const cancelSubmitBtn = document.getElementById('cancel-submit-btn');
const confirmSubmitBtn = document.getElementById('confirm-submit-btn');
const closeSuccessBtn = document.getElementById('close-success-btn');

// LMI Matrix Modal Functions
function showModal() {
    lmiMatrixModal.classList.remove('hidden');
    //mainContent.classList.add('blur-sm');
    appState.showLmiMatrix = true;
    document.body.style.overflow = 'hidden';
    
    // Hide navbar by setting z-index lower than modal
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '-1';
        navbar.style.visibility = 'hidden';
    }
    
    // ADD THIS: Initialize autocomplete when modal opens
    setTimeout(() => {

        initializeAllAutocompletes();

    }, 200);
}

function hideModal() {

    lmiMatrixModal.classList.add('hidden');
    
    // Remove blur if mainContent exists
    if (mainContent) {
        mainContent.classList.remove('blur-sm');
    }
    
    // CRITICAL: Restore scrolling

    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('overflow-y');
    document.documentElement.style.removeProperty('overflow');
    
    // Double-check after a tiny delay
    setTimeout(() => {
        if (document.body.style.overflow === 'hidden') {
            console.warn('⚠️ Body still has overflow:hidden! Forcing fix...');
            document.body.style.overflow = 'auto';
        }

    }, 50);
    
    // Show navbar by restoring z-index
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '';
        navbar.style.visibility = '';

    }
    
    appState.showLmiMatrix = false;

}

// Function to hide confirmation modal
function hideConfirmationModal() {
    confirmationModal.classList.add('hidden');
}

// Function to show success modal (NO BLUR)
function showSuccessModal() {
    successModal.classList.remove('hidden');
    // Don't blur the LMI form - keep it clear behind the modal
}

// Function to hide success modal AND close the LMI form
function hideSuccessModal() {
    // First hide the success modal
    successModal.classList.add('hidden');
    
    // Then close the LMI Matrix modal after a brief delay for smooth transition
    setTimeout(() => {
        hideModal();
    }, 300);
}

// Intercept form submission to show confirmation modal instead
lmiForm.addEventListener('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    // Run Step 4 validation before showing the confirmation modal
    if (!validateStep(3)) return;
    confirmationModal.classList.remove('hidden');
});

// Cancel button in confirmation modal
cancelSubmitBtn.addEventListener('click', hideConfirmationModal);

// Confirm submission button
confirmSubmitBtn.addEventListener('click', async () => {
    // Hide confirmation modal
    hideConfirmationModal();
    
    // Validate form
    const consentCheckbox = document.querySelector('.consent-checkbox');
    if (!consentCheckbox || !consentCheckbox.checked) {
        alert('Please consent to submit this data for labor market intelligence purposes.');
        consentCheckbox.focus();
        return;
    }
    
    // Populate contact number carrier before collecting FormData
    const contactTypeVal = document.getElementById('contact_type_input')?.value;
    const carrier = document.getElementById('contact_number_carrier');
    if (carrier) {
        if (contactTypeVal === 'mobile') {
            const mob = document.getElementById('mobile-input');
            carrier.value = mob ? selectedCountry.dial + mob.value : '';
        } else {
            const tel = document.getElementById('telephone-input');
            carrier.value = tel ? tel.value : '';
        }
    }

    // Gather form data
    const formData = new FormData(lmiForm);
    
    // Show loading state
    const originalText = confirmSubmitBtn.textContent;
    confirmSubmitBtn.textContent = 'Submitting...';
    confirmSubmitBtn.disabled = true;
    
    try {
        // Log what we're sending (for debugging)

        // Submit via AJAX
        const response = await fetch(lmiForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        });
        
        // Try to get the response text for debugging
        const responseText = await response.text();

        if (response.ok) {
            // Show success modal
            showSuccessModal();
            // Reset the form
            lmiForm.reset();
            
            // Reset all dropdowns to placeholder state
            resetFormDropdowns();

            // Reset step back to step 1
            showStep(0);
            
        } else {
            // Try to parse as JSON for error messages
            try {
                const errorData = JSON.parse(responseText);
                throw new Error(errorData.message || 'Submission failed with status: ' + response.status);
            } catch (e) {
                throw new Error('Submission failed with status: ' + response.status);
            }
        }
    } catch (error) {
        console.error('Full submission error:', error);
        alert('There was an error submitting the form. Please try again. Error: ' + error.message);
    } finally {
        // Reset button state
        confirmSubmitBtn.textContent = originalText;
        confirmSubmitBtn.disabled = false;
    }
});

// Close success modal button - closes both modals
closeSuccessBtn.addEventListener('click', hideSuccessModal);

// Helper function to reset all form dropdowns
function resetFormDropdowns() {
    // Reset industry dropdown
    const industryText = document.getElementById('industry-selected-text');
    const industryInput = document.getElementById('industry-selector-input');
    if (industryText && industryInput) {
        industryText.textContent = 'Please select your primary operation';
        industryText.classList.add('text-gray-400');
        industryText.classList.remove('text-gray-600');
        industryInput.value = '';
    }
    
    // Reset company size dropdown
    const companySizeText = document.getElementById('company-size-selected-text');
    const companySizeInput = document.getElementById('company-size-input');
    if (companySizeText && companySizeInput) {
        companySizeText.textContent = 'Select company size';
        companySizeText.classList.add('text-gray-400');
        companySizeText.classList.remove('text-gray-600');
        companySizeInput.value = '';
    }
    
    // Reset all job entry dropdowns
    document.querySelectorAll('.job-entry').forEach(entry => {
        const classText = entry.querySelector('.job-classification-text');
        const classInput = entry.querySelector('.job-classification-input');
        if (classText && classInput) {
            classText.textContent = 'Select job classification';
            classText.classList.add('text-gray-400');
            classText.classList.remove('text-gray-600');
            classInput.value = '';
        }
        
        const durationText = entry.querySelector('.duration-text');
        const durationInput = entry.querySelector('.duration-input');
        if (durationText && durationInput) {
            durationText.textContent = 'Select duration';
            durationText.classList.add('text-gray-400');
            durationText.classList.remove('text-gray-600');
            durationInput.value = '';
        }
        
        // Clear skill tags — call reset() to also clear the internal tags array in the closure,
        // so old values do not ghost back when the user starts typing in a new session.
        const techTagsContainer = entry.querySelector('.technical-tags-container');
        if (techTagsContainer) {
            if (techTagsContainer._tagSystem) {
                techTagsContainer._tagSystem.reset();
            } else {
                techTagsContainer.innerHTML = '';
            }
        }
        
        const softTagsContainer = entry.querySelector('.soft-tags-container');
        if (softTagsContainer) {
            if (softTagsContainer._tagSystem) {
                softTagsContainer._tagSystem.reset();
            } else {
                softTagsContainer.innerHTML = '';
            }
        }
        
        // Uncheck and hide detail sections
        const techCheckbox = entry.querySelector('.technical-checkbox');
        const techDetails = entry.querySelector('.technical-details');
        if (techCheckbox && techDetails) {
            techCheckbox.checked = false;
            techDetails.classList.add('hidden');
            techCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
            techCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
        }
        
        const softCheckbox = entry.querySelector('.soft-checkbox');
        const softDetails = entry.querySelector('.soft-details');
        if (softCheckbox && softDetails) {
            softCheckbox.checked = false;
            softDetails.classList.add('hidden');
            softCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
            softCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
        }
    });
    
    // Remove all additional job entries (keep only the first one)
    const jobEntries = document.querySelectorAll('.job-entry');
    jobEntries.forEach((entry, index) => {
        if (index > 0) {
            entry.remove();
        }
    });

    // FIX 1: Reset salary range dropdown for every job entry
    document.querySelectorAll('.job-entry').forEach(entry => {
        const salaryText = entry.querySelector('.salary-range-text');
        const salaryInput = entry.querySelector('.salary-range-input');
        const salaryBtn = entry.querySelector('.salary-range-btn');
        const salaryArrow = entry.querySelector('.salary-range-arrow');
        const below30kContainer = entry.querySelector('.below-30k-input-container');
        const below30kInput = entry.querySelector('.below-30k-salary-input');
        if (salaryText) {
            salaryText.textContent = 'Select salary range';
            salaryText.classList.add('text-gray-400');
            salaryText.classList.remove('text-gray-700');
        }
        if (salaryInput) salaryInput.value = '';
        if (salaryBtn) salaryBtn.classList.remove('border-red-500');
        if (salaryArrow) salaryArrow.classList.remove('rotate-180');
        if (below30kContainer) below30kContainer.classList.add('hidden');
        if (below30kInput) { below30kInput.value = ''; below30kInput.required = false; }
    });

    // FIX 2 (part of): Reset lmi-feature checkboxes: uncheck, re-enable, restore opacity/cursor
    document.querySelectorAll('.lmi-feature-checkbox').forEach(cb => {
        cb.checked = false;
        cb.disabled = false;
        const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
        if (wrapper) { wrapper.style.opacity = ''; wrapper.style.cursor = ''; }
    });

    // Hide the "Other" text input if visible
    const lmiOtherInput = document.querySelector('.lmi-other-input');
    if (lmiOtherInput) {
        lmiOtherInput.classList.add('hidden');
        const otherTextField = lmiOtherInput.querySelector('textarea[name="lmi_features_other"]');
        if (otherTextField) otherTextField.value = '';
    }
}

// Make sure these elements exist before adding event listeners
if (showLmiMatrixBtn) {
    showLmiMatrixBtn.addEventListener('click', showModal);
}

if (closeModalBtn) {
    closeModalBtn.addEventListener('click', hideModal);
}

if (modalBackdrop) {
    modalBackdrop.addEventListener('click', hideModal);
}

// Also close modals when clicking on backdrop
confirmationModal.addEventListener('click', (e) => {
    if (e.target === confirmationModal || e.target.classList.contains('absolute')) {
        hideConfirmationModal();
    }
});

successModal.addEventListener('click', (e) => {
    if (e.target === successModal || e.target.classList.contains('absolute')) {
        hideSuccessModal();
    }
});

// Close modals with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!confirmationModal.classList.contains('hidden')) {
            hideConfirmationModal();
        } else if (!successModal.classList.contains('hidden')) {
            hideSuccessModal();
        } else if (appState.showLmiMatrix) {
            hideModal();
        }
    }
});

    // Dropdown functionality
    function createDropdown(buttonId, menuId, selectedTextId, hiddenInputId, optionsSelector, arrowId = null) {
        const button = document.getElementById(buttonId);
        const menu = document.getElementById(menuId);
        const selectedText = document.getElementById(selectedTextId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const arrow = arrowId ? document.getElementById(arrowId) : null;
        const options = menu.querySelectorAll(optionsSelector);

        function positionMenu() {
            const rect = button.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom - 12;
            menu.style.top = (rect.bottom + 8) + 'px';
            menu.style.left = rect.left + 'px';
            menu.style.width = rect.width + 'px';
            menu.style.maxHeight = Math.min(384, spaceBelow) + 'px';
        }

        function toggleMenu() {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
            if (isHidden) {
                positionMenu();
                menu.scrollTop = 0;
            }
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                    if (otherArrow) {
                        otherArrow.classList.remove('rotate-180');
                    }
                }
            });
        }

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                selectedText.textContent = value;
                selectedText.classList.remove('text-gray-400');
                selectedText.classList.add('text-gray-600');
                hiddenInput.value = value;
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            }
        });

        // Reposition on scroll or resize
        window.addEventListener('scroll', () => { if (!menu.classList.contains('hidden')) positionMenu(); }, true);
        window.addEventListener('resize', () => { if (!menu.classList.contains('hidden')) positionMenu(); });

        return { button, menu, selectedText, hiddenInput };
    }

    // Initialize dropdowns
    const industryDropdown = createDropdown(
        'industry-dropdown-btn',
        'industry-dropdown-menu',
        'industry-selected-text',
        'industry-selector-input',
        '.industry-option',
        'industry-dropdown-arrow'
    );

    const companySizeDropdown = createDropdown(
        'company-size-btn',
        'company-size-dropdown',
        'company-size-selected-text',
        'company-size-input',
        '.company-size-option',
        'company-size-arrow'
    );

    // Job entry functionality
    function createJobEntryDropdown(button, menu, textElement, inputElement, arrowElement, optionsSelector) {
        function toggleMenu() {
            menu.classList.toggle('hidden');
            arrowElement.classList.toggle('rotate-180');
            
            // Close other dropdowns in the same job entry
            const jobEntry = button.closest('.job-entry');
            jobEntry.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                    if (otherArrow) {
                        otherArrow.classList.remove('rotate-180');
                    }
                }
            });
        }

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        const options = menu.querySelectorAll(optionsSelector);
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                textElement.textContent = value;
                textElement.classList.remove('text-gray-400');
                textElement.classList.add('text-gray-600');
                inputElement.value = value;
                menu.classList.add('hidden');
                arrowElement.classList.remove('rotate-180');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                arrowElement.classList.remove('rotate-180');
            }
        });
    }

    // Skill tags functionality
    function createSkillTagSystem(container, addButton, input, hiddenInput, tagsContainer) {
        const tags = [];
        
        function updateTags() {
            tagsContainer.innerHTML = '';
            tags.forEach((tag, index) => {
                const tagElement = document.createElement('span');
                tagElement.className = 'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
                tagElement.innerHTML = `
                    <span>${tag}</span>
                    <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5" data-index="${index}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                tagsContainer.appendChild(tagElement);
            });
            
            // Update hidden input
            hiddenInput.value = tags.join(', ');
            
            // Bug fix: stopPropagation so clicking remove does not bubble up to the
            // parent <label> and accidentally toggle the checkbox.
            tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const index = parseInt(e.target.closest('.remove-tag').getAttribute('data-index'));
                    tags.splice(index, 1);
                    updateTags();
                });
            });
        }
        
        function addTag() {
            const tag = input.value.trim();
            // Bug fix: case-insensitive duplicate check so "Apple" and "APPLE" are treated as the same.
            if (tag && !tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
                tags.push(tag);
                input.value = '';
                updateTags();
            } else {
                input.value = '';
            }
        }
        
        // Bug fix: expose reset() so resetFormDropdowns can clear the internal tags array,
        // not just the DOM — otherwise old tags ghost back when the user types in a new session.
        function reset() {
            tags.length = 0;
            tagsContainer.innerHTML = '';
            hiddenInput.value = '';
            input.value = '';
        }
        
        addButton.addEventListener('click', addTag);
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                // ← NEW: check if autocomplete has a highlighted item
                const autocompleteDropdown = input.parentElement
                    ? input.parentElement.querySelector('.autocomplete-suggestions')
                    : null;
                const hasHighlighted = autocompleteDropdown
                    && !autocompleteDropdown.classList.contains('hidden')
                    && autocompleteDropdown.querySelector('.bg-teal-100');
                if (hasHighlighted) return; // ← let autocomplete handle it
                addTag();
            }
        });
        return { tags, updateTags, addTag, reset };
    }

    // Checkbox show/hide functionality
    function setupCheckboxToggle(checkbox, targetElement) {
        checkbox.addEventListener('change', () => {
            // Outer wrapper is now a <div> (not a <label>) so we target the
            // nearest element that carries the border/bg classes.
            const wrapper = checkbox.closest('.technical-skills-label, .soft-skills-label');
            if (checkbox.checked) {
                targetElement.classList.remove('hidden');
                wrapper?.classList.add('border-teal-500', 'bg-teal-50');
                wrapper?.classList.remove('border-gray-200');
            } else {
                targetElement.classList.add('hidden');
                wrapper?.classList.remove('border-teal-500', 'bg-teal-50');
                wrapper?.classList.add('border-gray-200');

                // FIX 2: Clear all tags when the skill checkbox is unchecked
                const tagsContainer = targetElement.querySelector('.technical-tags-container, .soft-tags-container');
                if (tagsContainer) {
                    if (tagsContainer._tagSystem) {
                        tagsContainer._tagSystem.reset();
                    } else {
                        tagsContainer.innerHTML = '';
                    }
                }
                // Also clear the hidden input that holds the JSON array
                const hiddenInput = targetElement.querySelector('.technical-skills-input, .soft-skills-input');
                if (hiddenInput) hiddenInput.value = '';

                // Also clear the text input field
                const textInput = targetElement.querySelector('.technical-skill-input, .soft-skill-input');
                if (textInput) textInput.value = '';
            }
        });
    }

    // Initialize first job entry
    function initializeJobEntry(jobEntry) {
        // Classification dropdown
        const classBtn = jobEntry.querySelector('.job-classification-btn');
        const classMenu = jobEntry.querySelector('.job-classification-menu');
        const classText = jobEntry.querySelector('.job-classification-text');
        const classInput = jobEntry.querySelector('.job-classification-input');
        const classArrow = jobEntry.querySelector('.job-classification-arrow');
        
        if (classBtn && classMenu) {
            createJobEntryDropdown(
                classBtn,
                classMenu,
                classText,
                classInput,
                classArrow,
                '.job-classification-option'
            );
        }
        
        // Duration dropdown
        const durationBtn = jobEntry.querySelector('.duration-btn');
        const durationMenu = jobEntry.querySelector('.duration-menu');
        const durationText = jobEntry.querySelector('.duration-text');
        const durationInput = jobEntry.querySelector('.duration-input');
        const durationArrow = jobEntry.querySelector('.duration-arrow');
        
        if (durationBtn && durationMenu) {
            createJobEntryDropdown(
                durationBtn,
                durationMenu,
                durationText,
                durationInput,
                durationArrow,
                '.duration-option'
            );
        }
        
        // Technical skills
        const techCheckbox = jobEntry.querySelector('.technical-checkbox');
        const techDetails = jobEntry.querySelector('.technical-details');
        const techAddBtn = jobEntry.querySelector('.add-technical-skill');
        const techInput = jobEntry.querySelector('.technical-skill-input');
        const techHiddenInput = jobEntry.querySelector('.technical-skills-input');
        const techTagsContainer = jobEntry.querySelector('.technical-tags-container');
        
        if (techCheckbox && techDetails) {
            setupCheckboxToggle(techCheckbox, techDetails);
            
            if (techAddBtn && techInput && techHiddenInput && techTagsContainer) {
                const techTagSystem = createSkillTagSystem(
                    techDetails,
                    techAddBtn,
                    techInput,
                    techHiddenInput,
                    techTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                techTagsContainer._tagSystem = techTagSystem;
            }
        }
        
        // Soft skills
        const softCheckbox = jobEntry.querySelector('.soft-checkbox');
        const softDetails = jobEntry.querySelector('.soft-details');
        const softAddBtn = jobEntry.querySelector('.add-soft-skill');
        const softInput = jobEntry.querySelector('.soft-skill-input');
        const softHiddenInput = jobEntry.querySelector('.soft-skills-input');
        const softTagsContainer = jobEntry.querySelector('.soft-tags-container');
        
        if (softCheckbox && softDetails) {
            setupCheckboxToggle(softCheckbox, softDetails);
            
            if (softAddBtn && softInput && softHiddenInput && softTagsContainer) {
                const softTagSystem = createSkillTagSystem(
                    softDetails,
                    softAddBtn,
                    softInput,
                    softHiddenInput,
                    softTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                softTagsContainer._tagSystem = softTagSystem;
            }
        }
    }
   document.addEventListener('click', function(e) {
    // Toggle salary range dropdown
    if (e.target.closest('.salary-range-btn')) {
        const btn = e.target.closest('.salary-range-btn');
        const menu = btn.nextElementSibling;
        const arrow = btn.querySelector('.salary-range-arrow');
        
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
        
        // Close other dropdowns
        document.querySelectorAll('.salary-range-menu').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
    }
    
    // Select salary range option
    
if (e.target.closest('.salary-range-option')) {
    const option = e.target.closest('.salary-range-option');
    const container = option.closest('.mb-4');
    const btn = container.querySelector('.salary-range-btn');
    const menu = container.querySelector('.salary-range-menu');
    const text = btn.querySelector('.salary-range-text');
    const arrow = btn.querySelector('.salary-range-arrow');
    const input = container.querySelector('.salary-range-input');
    const below30kContainer = container.querySelector('.below-30k-input-container');
    const below30kInput = container.querySelector('.below-30k-salary-input');

    const value = option.dataset.value;

    text.textContent = option.textContent.trim();
    text.classList.remove('text-gray-400');
    text.classList.add('text-gray-700');

    menu.classList.add('hidden');
    arrow.classList.remove('rotate-180');

    if (value === 'Below ₱30,000') {
    input.value = '__below_30k__'; // sentinel value
    below30kContainer.classList.remove('hidden');
    below30kInput.required = true;
} else {
    input.value = value;
    below30kContainer.classList.add('hidden');
    below30kInput.required = false;
    below30kInput.value = '';
}
}
    
    // Close dropdown when clicking outside
    if (!e.target.closest('.salary-range-btn') && !e.target.closest('.salary-range-menu')) {
        document.querySelectorAll('.salary-range-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.salary-range-arrow').forEach(arrow => {
            arrow.classList.remove('rotate-180');
        });
    }
});

// Limit Below 30k Salary Input to 5 digits with comma formatting
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('below-30k-salary-input')) {
        let value = e.target.value;
        
        // Remove any non-numeric characters (including existing commas)
        value = value.replace(/[^0-9]/g, '');
        
        // Limit to 5 characters (29999)
        if (value.length > 5) {
            value = value.substring(0, 5);
        }
        
        // Check if value exceeds 29999
        const numValue = parseInt(value);
        if (numValue >= 30000) {
            value = '30000';
        }
        
        // Add comma formatting (e.g., 25000 becomes 25,000)
       if (value) {
            value = parseInt(value).toLocaleString('en-US');
        }

        e.target.value = value;

        // 🔥 ADD THIS NEW CODE HERE:
        if (value) {
            const container = e.target.closest('.mb-4');
            const salaryRangeInput = container.querySelector('.salary-range-input');
            const numericValue = value.replace(/,/g, ''); // Remove comma (25,000 → 25000)
            
            if (salaryRangeInput) {
                salaryRangeInput.value = numericValue; // Replace __below_30k__ with 25000
            }
        }
    }
});

    // Initialize existing job entries
document.querySelectorAll('.job-entry').forEach(initializeJobEntry);

// Add job title functionality
const addJobTitleBtn = document.getElementById('add-job-title-btn');
const jobTitlesContainer = document.getElementById('jobTitlesContainer');

let jobEntryCounter = 1; // starts at 1 since the first static entry uses index 0

addJobTitleBtn.addEventListener('click', () => {
    const jobCount = jobTitlesContainer.querySelectorAll('.job-entry').length;
    const entryIndex = jobEntryCounter++; // always unique, even after removals
    
    const newJobEntry = document.createElement('div');
    newJobEntry.className = 'bg-white rounded-lg p-4 border border-gray-200 job-entry relative';
    
    newJobEntry.innerHTML = `
        <button type="button" 
                class="remove-job-btn absolute top-4 right-4 text-red-500 hover:text-red-700 font-medium text-sm flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Remove
        </button>

        <div class="mb-4 pb-2 border-b border-gray-200">
            <h4 class="text-sm font-bold text-teal-700">Job Entry #${jobCount + 1}</h4>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Job Title: <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="job_title[]"
                placeholder="e.g. Senior Java Developer"
                required
                class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
            />
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Standard Job Classifications / Families: <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <button type="button" class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="job-classification-text text-gray-400">Select job classification</span>
                    <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div class="job-classification-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                    <div data-value="Accounting, Finance & Banking" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Accounting, Finance & Banking
                    </div>
                    <div data-value="Administrative, HR & Office Support" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Administrative, HR & Office Support
                    </div>
                    <div data-value="Agriculture, Forestry & Agribusiness" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Agriculture, Forestry & Agribusiness
                    </div>
                    <div data-value="Construction, Engineering & Architecture" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Construction, Engineering & Architecture
                    </div>
                    <div data-value="Customer Service & BPO (Contact Center)" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Customer Service & BPO (Contact Center)
                    </div>
                    <div data-value="Education, Training & Academe" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Education, Training & Academe
                    </div>
                    <div data-value="Healthcare, Medical & Allied Services" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Healthcare, Medical & Allied Services
                    </div>
                    <div data-value="IT, Software, Data & Digital Creative" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • IT, Software, Data & Digital Creative
                    </div>
                    <div data-value="Legal, Compliance & Public Service" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Legal, Compliance & Public Service
                    </div>
                    <div data-value="Logistics, Transport & Supply Chain" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Logistics, Transport & Supply Chain
                    </div>
                    <div data-value="Manufacturing, Production & Technical" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Manufacturing, Production & Technical
                    </div>
                    <div data-value="Sales, Marketing, Retail & E-Commerce" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Sales, Marketing, Retail & E-Commerce
                    </div>
                    <div data-value="Science, Research & Laboratory" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Science, Research & Laboratory
                    </div>
                    <div data-value="Skilled Trades, Maintenance & General Services" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Skilled Trades, Maintenance & General Services
                    </div>
                    <div data-value="Tourism, Hospitality & Food Service" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Tourism, Hospitality & Food Service
                    </div>
                </div>
                
                <input type="hidden" class="job-classification-input" name="job_classification[]" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">Salary Range: <span class="text-red-500">*</span></label>
            <div class="relative">
                <button type="button" class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="salary-range-text text-gray-400">Select salary range</span>
                    <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                    <div data-value="₱30,000 - ₱59,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱30,000 - ₱59,999</div>
                    <div data-value="₱60,000 - ₱89,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱60,000 - ₱89,999</div>
                    <div data-value="₱90,000 - ₱149,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱90,000 - ₱149,999</div>
                    <div data-value="₱150,000 - ₱499,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱150,000 - ₱499,999</div>
                    <div data-value="₱500,000 and above" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱500,000 and above</div>
                    <div data-value="Below ₱30,000" class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Below ₱30,000 (please specify)</div>
                </div>
                <input type="hidden" class="salary-range-input" name="salary_range[]" >
            </div>
            
            <div class="below-30k-input-container mt-3 hidden">
                <label class="block text-gray-600 text-xs font-medium mb-2">Please specify the exact salary amount:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                    <input type="text" 
                        class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" 
                        placeholder="e.g. 25,000"
                        inputmode="numeric">
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Duration that the Vacancy is Open: <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <button type="button" class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="duration-text text-gray-400">Select duration</span>
                    <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div class="duration-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                    <div data-value="Less than 30 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        Less than 30 Days
                    </div>
                    <div data-value="30-60 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        30-60 Days
                    </div>
                    <div data-value="60-90 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        60-90 Days
                    </div>
                    <div data-value="90+ Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        90+ Days
                    </div>
                </div>
                
                <input type="hidden" class="duration-input" name="vacancy_duration[]" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                 Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
            </label>
            <div class="difficulty-reasons space-y-3">
                
                <div class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Technical / Hard Skills Missing"
                            class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Technical / Hard Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                        </div>
                    </label>
                    <div class="technical-details mt-3 hidden">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            What specific technical tools, software, or machinery knowledge is missing?
                        </label>
                        
                        <div class="technical-tags-container flex flex-wrap gap-2 mb-2"></div>
                        
                        <div class="flex gap-2 skill-input-row">
                            <input type="text" 
                                class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                placeholder="Type a skill and press Enter..."
                                enterkeyhint="done" inputmode="text"/>
                            <button type="button" 
                                    class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                    </div>
                </div>

                <div class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Soft / Employability Skills Missing"
                            class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Soft / Employability Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                        </div>
                    </label>
                    <div class="soft-details mt-3 hidden">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            What attitude or behavioral traits cause you to reject applicants?
                        </label>
                        
                        <div class="soft-tags-container flex flex-wrap gap-2 mb-2"></div>
                        
                        <div class="flex gap-2 skill-input-row">
                            <input type="text" 
                                class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                placeholder="Type a trait and press Enter..."
                                enterkeyhint="done" inputmode="text"/>
                            <button type="button" 
                                    class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
            <label class="block text-gray-800 text-sm font-semibold mb-3">
                 How much does the difficulty finding qualified applicants for this role impact your business operations? 
                <span class="text-red-500">*</span>
            </label>
            <div class="impact-level space-y-3">
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="High" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">High Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Medium" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Medium Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations continue with adjustments</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Low" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Low Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Minimal operational impact</div>
                    </div>
                </label>
            </div>
        </div>
    `;
    
    jobTitlesContainer.appendChild(newJobEntry);
    initializeJobEntry(newJobEntry);
    
    // Add remove functionality
    const removeBtn = newJobEntry.querySelector('.remove-job-btn');
    removeBtn.addEventListener('click', () => {
        newJobEntry.remove();
    });
    
    // Scroll to new entry
    newJobEntry.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

    // Other rejection reasons toggle
    const otherRejectionCheckbox = document.querySelector('.other-rejection-checkbox');
    const otherRejectionInput = document.querySelector('.other-rejection-input');
    if (otherRejectionCheckbox && otherRejectionInput) {
        otherRejectionCheckbox.addEventListener('change', () => {
            if (otherRejectionCheckbox.checked) {
                otherRejectionInput.classList.remove('hidden');
            } else {
                otherRejectionInput.classList.add('hidden');
            }
        });
    }

    // Other coordination frequency toggle
    const otherCoordinationRadio = document.querySelector('.other-coordination-radio');
    const otherCoordinationInput = document.querySelector('.other-coordination-input');
    if (otherCoordinationRadio && otherCoordinationInput) {
        otherCoordinationRadio.addEventListener('change', () => {
            if (otherCoordinationRadio.checked) {
                otherCoordinationInput.classList.remove('hidden');
            } else {
                otherCoordinationInput.classList.add('hidden');
            }
        });
    }

    // LMI Features: max 2 selections + Other text toggle
    const lmiCheckboxes = document.querySelectorAll('.lmi-feature-checkbox');
    const lmiOtherCheckbox = document.querySelector('.lmi-other-checkbox');
    const lmiOtherInput = document.querySelector('.lmi-other-input');

    lmiCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checked = document.querySelectorAll('.lmi-feature-checkbox:checked');

            // Enforce max 2
            if (checked.length > 2) {
                this.checked = false;
                return;
            }

            // Disable unchecked when 2 selected, re-enable when below 2
            if (checked.length === 2) {
                lmiCheckboxes.forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                        const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                        if (wrapper) { wrapper.style.opacity = '0.4'; wrapper.style.cursor = 'not-allowed'; }
                    }
                });
            } else {
                lmiCheckboxes.forEach(cb => {
                    cb.disabled = false;
                    const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                    if (wrapper) { wrapper.style.opacity = ''; wrapper.style.cursor = ''; }
                });
            }

            // Toggle "Other" text input
            if (lmiOtherCheckbox && lmiOtherInput) {
                lmiOtherCheckbox.checked
                    ? lmiOtherInput.classList.remove('hidden')
                    : lmiOtherInput.classList.add('hidden');
            }
        });
    });

    // Sector tabs functionality
    document.querySelectorAll('.sector-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active state from all tabs
            document.querySelectorAll('.sector-tab').forEach(t => {
                t.classList.remove('bg-purple-600', 'text-white');
                t.classList.add('border', 'text-gray-500', 'hover:bg-gray-50');
            });
            
            // Add active state to clicked tab
            tab.classList.add('bg-purple-600', 'text-white');
            tab.classList.remove('border', 'text-gray-500', 'hover:bg-gray-50');
            
            // Here you would typically filter the skill gaps based on the selected sector
            // For now, we'll just log the selection

        });
    });

    // Export analysis button
    const exportBtn = document.querySelector('.export-analysis-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            alert('Export functionality would be implemented here.');
        });
    }

}); // end DOMContentLoaded

// ─────────────────────────────────────────────────────────────────────────────
function filterSkills(sector) {
    const scrollContainer = document.getElementById('sector-filter-scroll');

    // Update active tab styling + scroll active tab into view
    document.querySelectorAll('.sector-tab').forEach(tab => {
        if (tab.getAttribute('data-sector') === sector) {
            tab.classList.add('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.remove('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');

            // Scroll the active tab into view within the scroll container
            if (scrollContainer) {
                const contRect = scrollContainer.getBoundingClientRect();
                const tabRect  = tab.getBoundingClientRect();
                // Current scroll + tab center - container center
                const targetScroll = scrollContainer.scrollLeft
                    + (tabRect.left - contRect.left)
                    - (contRect.width / 2)
                    + (tabRect.width / 2);
                scrollContainer.scrollTo({ left: targetScroll, behavior: 'smooth' });
            }
        } else {
            tab.classList.remove('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.add('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');
        }
    });

    // Filter skill tags
    document.querySelectorAll('.skill-tag').forEach(tag => {
        const tagSector = tag.getAttribute('data-sector');
        tag.style.display = (sector === 'All' || tagSector === sector) ? 'flex' : 'none';
    });

    // Re-check scroll hint visibility after filtering
    setTimeout(() => {
        if (typeof window._techScrollUpdate === 'function') window._techScrollUpdate();
        if (typeof window._softScrollUpdate === 'function') window._softScrollUpdate();
    }, 50);
}

// Arrow buttons + mouse wheel scroll for filter bar
document.addEventListener('DOMContentLoaded', function () {
    const scroll = document.getElementById('sector-filter-scroll');
    const left   = document.getElementById('filter-left');
    const right  = document.getElementById('filter-right');

    if (scroll && left && right) {
        left.addEventListener('click',  () => scroll.scrollBy({ left: -200, behavior: 'smooth' }));
        right.addEventListener('click', () => scroll.scrollBy({ left:  200, behavior: 'smooth' }));

        // Mouse wheel scrolls horizontally
        scroll.addEventListener('wheel', function (e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                scroll.scrollBy({ left: e.deltaY * 2, behavior: 'smooth' });
            }
        }, { passive: false });
    }
});

// Skills cloud scroll indicators
document.addEventListener('DOMContentLoaded', function () {
    function initScrollHint(containerId, wrapperId, hintId) {
        const container = document.getElementById(containerId);
        const wrapper   = document.getElementById(wrapperId);
        const hint      = document.getElementById(hintId);
        if (!container || !wrapper || !hint) return;

        function update() {
            const scrollable = container.scrollHeight > container.clientHeight + 4;
            hint.style.display = scrollable ? 'flex' : 'none';
            if (!scrollable) { wrapper.classList.add('at-bottom'); return; }
            const atBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 8;
            wrapper.classList.toggle('at-bottom', atBottom);
            hint.style.opacity = atBottom ? '0' : '1';
        }

        container.addEventListener('scroll', update);
        // Re-check after images/fonts settle
        setTimeout(update, 300);
        update();

        // Expose so external code (e.g. filterSkills) can trigger a re-check
        return update;
    }

    window._techScrollUpdate = initScrollHint('tech-skills-container', 'tech-skills-scroll-wrapper', 'tech-scroll-hint');
    window._softScrollUpdate = initScrollHint('soft-skills-container', 'soft-skills-scroll-wrapper', 'soft-scroll-hint');
});

// ─────────────────────────────────────────────────────────────────────────────
    // Comprehensive Autocomplete System for Job Titles and Skills
// Add this script to your blade file or separate JS file

// Store autocomplete data
let autocompleteData = {
    jobTitles: [],
    technicalSkills: [],
    softSkills: []
};

// Fetch all autocomplete data when page loads
async function fetchAutocompleteData() {
    try {
        const response = await fetch('/api/autocomplete-data');
        const data = await response.json();
        
        if (data.success) {
            autocompleteData.jobTitles = data.job_titles || [];
            autocompleteData.technicalSkills = data.technical_skills || [];
            autocompleteData.softSkills = data.soft_skills || [];
        }
    } catch (error) {
        console.error('❌ Failed to fetch autocomplete data:', error);
        // Fallback to empty arrays
        autocompleteData.jobTitles = [];
        autocompleteData.technicalSkills = [];
        autocompleteData.softSkills = [];
    }
}

// Generic autocomplete function
function createAutocomplete(inputElement, dataSource, onSelect) {
    if (inputElement.hasAttribute('data-autocomplete-initialized')) return;
    inputElement.setAttribute('data-autocomplete-initialized', 'true');
    
    // Create suggestion dropdown
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.className = 'autocomplete-suggestions absolute w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden';
    suggestionsDiv.style.zIndex = '9999';
    suggestionsDiv.style.position = 'absolute';
    suggestionsDiv.style.top = '100%';
    suggestionsDiv.style.left = '0';
    suggestionsDiv.style.right = '0';
    // Make parent relative if not already
    if (getComputedStyle(inputElement.parentElement).position === 'static') {
        inputElement.parentElement.style.position = 'relative';
    }
    inputElement.parentElement.appendChild(suggestionsDiv);
    
    // Listen for input
    inputElement.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim().toLowerCase();
        
        if (searchTerm.length < 2) {
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        // Filter matching items
        const matches = dataSource.filter(item => 
            item.toLowerCase().includes(searchTerm)
        );
        
        if (matches.length === 0) {
            // Bug fix: hide the dropdown silently when there are no matches
            // instead of showing an annoying "No result found" message.
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        // Display suggestions (limit to 10)
        suggestionsDiv.innerHTML = '';
        matches.slice(0, 10).forEach(item => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-b-0 transition';
            
            // Highlight matching text
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlightedItem = item.replace(regex, '<span class="font-semibold text-teal-600">$1</span>');
            suggestionItem.innerHTML = highlightedItem;
            
            // Click to select
            suggestionItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent click from bubbling to elements below
                
                if (onSelect) {
                    onSelect(item, inputElement);
                } else {
                    inputElement.value = item;
                }
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.classList.add('hidden');
            });
            
            suggestionsDiv.appendChild(suggestionItem);
        });
        
        suggestionsDiv.classList.remove('hidden');
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!inputElement.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });
    
    // Keyboard navigation
    inputElement.addEventListener('keydown', function(e) {
        const suggestions = suggestionsDiv.querySelectorAll('div.px-4');
        if (suggestions.length === 0) return;
        
        let currentIndex = Array.from(suggestions).findIndex(s => s.classList.contains('bg-teal-100'));
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
            currentIndex = currentIndex < suggestions.length - 1 ? currentIndex + 1 : 0;
            suggestions[currentIndex].classList.add('bg-teal-100');
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
            currentIndex = currentIndex > 0 ? currentIndex - 1 : suggestions.length - 1;
            suggestions[currentIndex].classList.add('bg-teal-100');
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && currentIndex >= 0) {
            e.preventDefault();
            suggestions[currentIndex].click();
        } else if (e.key === 'Escape') {
            suggestionsDiv.classList.add('hidden');
        }
    });
}

// Initialize Job Title Autocomplete
function initializeJobTitleAutocomplete() {
    document.querySelectorAll('.job-title-input').forEach(input => {
        createAutocomplete(input, autocompleteData.jobTitles);
    });
}

// Initialize Technical Skills Autocomplete
function initializeTechnicalSkillsAutocomplete() {
    document.querySelectorAll('.technical-skill-input').forEach(input => {
        createAutocomplete(input, autocompleteData.technicalSkills, function(selectedSkill, inputElement) {
            // When a skill is selected, add it as a tag
            inputElement.value = selectedSkill;
            
            // Trigger the add button click or Enter key
            const addButton = inputElement.parentElement.querySelector('.add-technical-skill');
            if (addButton) {
                addButton.click();
            }
            
            // Clear input after selection
            setTimeout(() => {
                inputElement.value = '';
                inputElement.focus();
            }, 100);
        });
    });
}

// Initialize Soft Skills Autocomplete
function initializeSoftSkillsAutocomplete() {
    document.querySelectorAll('.soft-skill-input').forEach(input => {
        createAutocomplete(input, autocompleteData.softSkills, function(selectedSkill, inputElement) {
            // When a skill is selected, add it as a tag
            inputElement.value = selectedSkill;
            
            // Trigger the add button click
            const addButton = inputElement.parentElement.querySelector('.add-soft-skill');
            if (addButton) {
                addButton.click();
            }
            
            // Clear input after selection
            setTimeout(() => {
                inputElement.value = '';
                inputElement.focus();
            }, 100);
        });
    });
}

// Initialize all autocompletes
function initializeAllAutocompletes() {
    initializeJobTitleAutocomplete();
    initializeTechnicalSkillsAutocomplete();
    initializeSoftSkillsAutocomplete();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchAutocompleteData().then(() => {
        initializeAllAutocompletes();
    });
    
    // Re-initialize when new fields are added (for dynamic job entries)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setTimeout(() => {
                    initializeAllAutocompletes();
                }, 100);
            }
        });
    });
    
    const container = document.getElementById('jobTitlesContainer');
    if (container) {
        observer.observe(container, { childList: true, subtree: true });
    }
});

// ===== CSV EXPORT FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportAnalysisBtn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportDashboardToCSV();
        });
    }
});

function exportDashboardToCSV() {
    const timestamp = new Date().toLocaleString();
    const dateStr   = new Date().toISOString().split('T')[0];

    // ── Collect all section data ─────────────────────────────────────────────

    // Section 1: High-Volume Job Titles
    const jobRows = [];
    if (window.jobsChart && window.jobsChart.data) {
        window.jobsChart.data.labels.forEach((label, i) => {
            jobRows.push([i + 1, label, window.jobsChart.data.datasets[0].data[i]]);
        });
    }

    // Section 2: Hard-to-Fill Roles
    const roleRows = [];
    document.querySelectorAll('.role-card').forEach(card => {
        const title      = card.querySelector('.font-bold')?.textContent?.trim() || '';
        const duration   = card.querySelector('.text-xs.text-gray-400')?.textContent?.trim() || '';
        const details    = card.querySelector('.role-details');
        let classification = '', reasons = '', techSkills = '', softSkills = '';
        if (details) {
            const classEls = details.querySelectorAll('div > p.text-slate-800');
            if (classEls.length) classification = classEls[0].textContent.trim();
            const ul = details.querySelector('ul.list-disc');
            if (ul) reasons = Array.from(ul.querySelectorAll('li')).map(li => li.textContent.trim()).join('; ');
            const techSpan = Array.from(details.querySelectorAll('span.font-medium.text-slate-600')).find(s => s.textContent.includes('Technical Skills'));
            if (techSpan) techSkills = Array.from(techSpan.parentElement.querySelectorAll('span.bg-blue-100')).map(t => t.textContent.trim()).join('; ');
            const softSpan = Array.from(details.querySelectorAll('span.font-medium.text-slate-600')).find(s => s.textContent.includes('Soft Skills'));
            if (softSpan) softSkills = Array.from(softSpan.parentElement.querySelectorAll('span.bg-purple-100')).map(t => t.textContent.trim()).join('; ');
        }
        roleRows.push([title, classification, duration, reasons, techSkills, softSkills]);
    });

    // Section 3: Critical Skill Gaps
    const skillRows = [];
    if (window.skillGapsChart && window.skillGapsChart.data) {
        window.skillGapsChart.data.labels.forEach((label, i) => {
            skillRows.push([i + 1, label, window.skillGapsChart.data.datasets[0].data[i]]);
        });
    }

    // Section 4: Employment Trends
    const trendRows = [];
    if (window.trendsChart && window.trendsChart.data) {
        window.trendsChart.data.labels.forEach((label, i) => {
            trendRows.push([label, window.trendsChart.data.datasets[0].data[i]]);
        });
    }

    // ── Build worksheet rows ─────────────────────────────────────────────────
    const sheetRows = [
        // Title
        ['Davao Employment Dashboard Analysis'],
        ['Generated on', timestamp],
        [],
        // Section 1
        ['HIGH-VOLUME JOB TITLES'],
        ['Rank', 'Job Title', 'Count'],
        ...jobRows,
        [],
        // Section 2
        ['HARD-TO-FILL ROLES'],
        ['Job Title', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills', 'Soft Skills'],
        ...roleRows,
        [],
        // Section 3
        ['CRITICAL SKILL GAPS'],
        ['Rank', 'Skill', 'Frequency'],
        ...skillRows,
        [],
        // Section 4
        ['EMPLOYMENT TRENDS (Last 6 Months)'],
        ['Month', 'Job Postings'],
        ...trendRows,
    ];

    const ws = XLSX.utils.aoa_to_sheet(sheetRows);

    // ── Styles ───────────────────────────────────────────────────────────────
    const border     = { style: 'thin', color: { rgb: 'C5D0DE' } };
    const cellBorder = { top: border, bottom: border, left: border, right: border };
    const cenAlign   = { horizontal: 'center', vertical: 'center' };
    const leftAlign  = { horizontal: 'left',   vertical: 'center' };
    const numAlign   = { horizontal: 'right',  vertical: 'center' };

    // Section header rows (detect by known labels)
    const sectionTitles  = new Set(['HIGH-VOLUME JOB TITLES', 'HARD-TO-FILL ROLES', 'CRITICAL SKILL GAPS', 'EMPLOYMENT TRENDS (Last 6 Months)']);
    const columnHeaders  = new Set(['Rank', 'Job Title', 'Count', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills', 'Soft Skills', 'Skill', 'Frequency', 'Month', 'Job Postings']);

    // Determine max columns for merges
    const maxCols = sheetRows.reduce((m, r) => Math.max(m, r.length), 0);

    // Merge title row + section title rows across all columns
    ws['!merges'] = [];

    // Auto column widths
    const colWidths = Array(maxCols).fill(10);
    sheetRows.forEach(row => {
        row.forEach((cell, ci) => {
            const len = String(cell ?? '').length;
            if (len + 4 > colWidths[ci]) colWidths[ci] = len + 4;
        });
    });
    ws['!cols'] = colWidths.map(w => ({ wch: w }));

    // Apply cell styles row by row
    sheetRows.forEach((row, r) => {
        const firstCell = String(row[0] ?? '');
        const isTitle        = r === 0;
        const isGenerated    = r === 1;
        const isSectionTitle = sectionTitles.has(firstCell);
        const isColHeader    = row.length > 0 && columnHeaders.has(firstCell);

        if (isSectionTitle || isTitle) {
            ws['!merges'].push({ s: { r, c: 0 }, e: { r, c: maxCols - 1 } });
        }

        row.forEach((_, c) => {
            const addr = XLSX.utils.encode_cell({ r, c });
            if (!ws[addr]) return;

            if (isTitle) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '0D2137' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 14, name: 'Calibri' },
                    alignment: cenAlign,
                };
            } else if (isGenerated) {
                ws[addr].s = {
                    font:      { italic: true, color: { rgb: '64748B' }, sz: 10, name: 'Calibri' },
                    alignment: leftAlign,
                };
            } else if (isSectionTitle) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '1E3A5F' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 12, name: 'Calibri' },
                    alignment: leftAlign,
                };
            } else if (isColHeader) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '334155' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 10, name: 'Calibri' },
                    alignment: cenAlign,
                    border:    cellBorder,
                };
            } else if (row.length > 0) {
                // Data row — alternate shading
                const isNum = typeof row[c] === 'number';
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: r % 2 === 0 ? 'F1F5F9' : 'FFFFFF' } },
                    font:      { sz: 10, name: 'Calibri' },
                    alignment: isNum ? numAlign : leftAlign,
                    border:    cellBorder,
                };
            }
        });
    });

    // ── Export ───────────────────────────────────────────────────────────────
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Dashboard Analysis');
    XLSX.writeFile(wb, `davao-employment-analysis-${dateStr}.xlsx`, { bookType: 'xlsx', cellStyles: true });

    console.log('Export complete: davao-employment-analysis-' + dateStr + '.xlsx');
}


// ─────────────────────────────────────────────────────────────────────────────
        // Helper function to format salary range with peso sign and thousand separators
        function formatSalaryRange(salaryRange) {
            if (!salaryRange || salaryRange === 'Not specified') {
                return salaryRange;
            }
            
            // Convert to string if it's a number
            let salaryStr = String(salaryRange);
            
            // If it contains a range (e.g., "30000 - 59999" or "30000-59999")
            if (salaryStr.includes('-')) {
                // Split by dash, allowing spaces around it
                let parts = salaryStr.split(/\s*-\s*/);
                
                if (parts.length === 2) {
                    // Format each part
                    let min = parts[0].trim().replace(/[₱,]/g, ''); // Remove existing ₱ and commas
                    let max = parts[1].trim().replace(/[₱,]/g, '');
                    
                    // Check if they're valid numbers
                    if (!isNaN(min) && !isNaN(max)) {
                        min = Number(min).toLocaleString();
                        max = Number(max).toLocaleString();
                        return '₱' + min + ' - ₱' + max;
                    }
                }
            }
            
            // If it's a single number or already formatted
            let cleaned = salaryStr.replace(/[₱,]/g, ''); // Remove existing ₱ and commas
            
            // Check if it's a valid number
            if (!isNaN(cleaned) && cleaned.trim() !== '') {
                let formatted = Number(cleaned).toLocaleString();
                return '₱' + formatted;
            }
            
            // If already has peso sign or is text (like "Below ₱30,000"), return as is
            if (salaryStr.includes('₱')) {
                return salaryStr;
            }
            
            // Default: just add peso sign
            return '₱' + salaryStr;
        }
        
        // Process matrix results to add peso sign to salary ranges
        let matrixResultsRaw = window._jobMarketData.matrixResults;
        window.matrixResultsData = matrixResultsRaw.map(result => ({
            ...result,
            salary_range: formatSalaryRange(result.salary_range)
        }));
        // ↓ INSERTED: keep a pristine copy so the Clear filter can restore original data
        window.matrixResultsDataOriginal = window.matrixResultsData.slice();
        // ↑ END INSERTED

function exportLMIMatrixToCSV() {
    const timestamp = new Date().toLocaleString();
    const dateStr   = new Date().toISOString().split('T')[0];

    // ── Helpers ───────────────────────────────────────────────────────────────
    const mkCell = (v, s) => ({ t: 's', v: String(v ?? ''), s });
    const border      = { style: 'thin', color: { rgb: 'C5D0DE' } };
    const bAll        = { top: border, bottom: border, left: border, right: border };
    const FONT        = (opts = {}) => ({ sz: opts.sz || 10, name: 'Calibri', bold: !!opts.bold, color: { rgb: opts.color || '1A1A1A' }, italic: !!opts.italic });
    const FILL        = rgb  => ({ patternType: 'solid', fgColor: { rgb } });
    const AL          = (h, v, wrap) => ({ horizontal: h, vertical: v, wrapText: !!wrap });
    const impactFill  = { 'High': 'FEE2E2', 'Medium': 'FFF9C4', 'Low': 'DCFCE7' };

    // ── Get data to export (all filtered data if a filter is active, else current page) ──
    const impactOrder = { 'High': 1, 'Medium': 2, 'Low': 3 };
    const sorted = (window.matrixResultsData || []).slice().sort((a, b) =>
        (impactOrder[a.impact] || 2) - (impactOrder[b.impact] || 2)
    );
    const itemsPerPage = 10;
    const totalPages   = Math.ceil(sorted.length / itemsPerPage);

    // If a period filter is active (badge is visible), export ALL filtered results
    const filterBadge   = document.getElementById('matrixFilterBadge');
    const filterIsActive = filterBadge && !filterBadge.classList.contains('hidden') && filterBadge.textContent.trim() !== '';

    let exportData, currentPage, exportLabel;
    if (filterIsActive) {
        // Export every record matching the active filter
        exportData  = sorted;
        currentPage = null; // N/A — exporting all
        exportLabel = `All ${sorted.length} filtered roles`;
    } else {
        // No filter active — export only the current visible page (original behaviour)
        const paginationSpans = document.querySelectorAll('.pagination-controls span.font-bold');
        const startItem = parseInt(paginationSpans[0]?.textContent) || 1;
        currentPage     = Math.ceil(startItem / itemsPerPage);
        exportData      = sorted.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
        exportLabel     = `Page ${currentPage} of ${totalPages}`;
    }

    const pageData = exportData; // alias kept so the rest of the function is unchanged

    // ── Parse each row into skill arrays + plain text ─────────────────────────
    const parseSkills = arr =>
        (arr || []).map(s => typeof s === 'object' ? (s.name || '') : String(s || '')).filter(Boolean);

    // FIXED: added salary_range to row mapping 
    const rows = pageData.map(result => ({
        role:   result.role          || '',
        sector: result.sector        || '',
        salary: result.salary_range  || '',
        tech:   parseSkills(result.hard_skills),   // array of strings
        soft:   parseSkills(result.soft_skills),   // array of strings
        impact: result.impact        || 'Medium',
    }));

    // ── Column order: Job Title | Sector | Salary Range | Tech Skills | Soft Skills | Gap Impact ──
    // (Gap Impact moved to last column per design requirement)
    const NC         = 6;
    const colHeaders = [
        'Job Title / Role',
        'Sector',
        'Missing Technical Skills',
        'Missing Soft Skills',
        'Salary Range',
        'Gap Impact',
    ];

    // ── Auto-fit column widths — based on the longest actual value in each column ──
    // Skill columns: measure the longest individual skill string (each will be on its own line)
    const safeMax = (...vals) => {
        const nums = vals.filter(v => typeof v === 'number' && isFinite(v));
        return nums.length ? Math.max(...nums) : 0;
    };
    const colW = [
        Math.min(safeMax(colHeaders[0].length, ...rows.map(r => r.role.length))                          + 4, 50),
        Math.min(safeMax(colHeaders[1].length, ...rows.map(r => r.sector.length))                        + 4, 45),
        Math.min(safeMax(colHeaders[2].length, ...rows.flatMap(r => r.tech.map(s => s.length)))          + 4, 55),
        Math.min(safeMax(colHeaders[3].length, ...rows.flatMap(r => r.soft.map(s => s.length)))          + 4, 55),
        Math.min(safeMax(colHeaders[4].length, ...rows.map(r => r.salary.length))                        + 4, 30),
        Math.min(safeMax(colHeaders[5].length, ...rows.map(r => r.impact.length))                        + 4, 18),
    ];

    // ── Build worksheet manually — cell by cell ───────────────────────────────
    // SheetJS CE: aoa_to_sheet silently strips \n — must assign t:'s' cells directly.
    const ws = {};
    const C  = (r, c) => XLSX.utils.encode_cell({ r, c });

    // Row 0 — Title
    ws[C(0,0)] = mkCell('LMI Granularity Matrix - Competency Gap Analysis', {
        fill: FILL('064E3B'), font: FONT({ bold: true, color: 'FFFFFF', sz: 13 }),
        alignment: AL('center','center')
    });
    for (let c = 1; c < NC; c++) ws[C(0,c)] = mkCell('', { fill: FILL('064E3B'), alignment: AL('center','center') });

    // Row 1 — Generated on
    ws[C(1,0)] = mkCell(`Generated on: ${timestamp}`, {
        font: FONT({ italic: true, color: '64748B' }), alignment: AL('left','center')
    });
    for (let c = 1; c < NC; c++) ws[C(1,c)] = mkCell('', { font: FONT({ color: '64748B' }) });

    // Row 2 — Page / filter info
    const infoLine = filterIsActive
        ? `Filtered export: ${exportLabel}  (${pageData.length} of ${sorted.length} roles in dataset)`
        : `Showing page ${currentPage} of ${totalPages}  (${pageData.length} of ${sorted.length} total roles)`;
    ws[C(2,0)] = mkCell(infoLine, {
        font: FONT({ italic: true, color: '64748B' }), alignment: AL('left','center')
    });
    for (let c = 1; c < NC; c++) ws[C(2,c)] = mkCell('', { font: FONT({ color: '64748B' }) });

    // Row 3 — Spacer (empty)
    for (let c = 0; c < NC; c++) ws[C(3,c)] = mkCell('', {});

    // Row 4 — Column headers
    colHeaders.forEach((h, c) => {
        ws[C(4,c)] = mkCell(h, {
            fill: FILL('065F46'), font: FONT({ bold: true, color: 'FFFFFF' }),
            alignment: AL('center','center', true), border: bAll
        });
    });

    // Rows 5+ — Data rows
    const rowHeights = [
        { hpt: 30 }, // title
        { hpt: 18 }, // generated on
        { hpt: 18 }, // page info
        { hpt:  5 }, // spacer
        { hpt: 22 }, // col headers
    ];

    rows.forEach((row, di) => {
        const r    = 5 + di;
        const bg   = di % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
        const iBg  = impactFill[row.impact] || 'FFF9C4';

        // Each skill on its own line.
        // IMPORTANT: Excel requires \r\n (not just \n) to honour line breaks on first open.
        // Using only \n causes Excel to collapse all skills onto one line until the column
        // is resized/clicked — \r\n forces the cell to render correctly immediately.
        const techText = row.tech.length ? row.tech.join('\r\n') : '';
        const softText = row.soft.length ? row.soft.join('\r\n') : '';

        // Row height driven by the column with the most line-items.
        // hpt = 15pt per line (matches Calibri 10 single-spaced) + 8pt top/bottom padding.
        // `customHeight: true` tells Excel this is a manually set height — do NOT auto-fit.
        // Without this flag Excel ignores hpt and collapses the row on open.
        const lines  = Math.max(row.tech.length, row.soft.length, 1);
        const rowHpt = lines * 15 + 8;

        // Col 0 — Job Title / Role
        ws[C(r,0)] = mkCell(row.role, {
            fill: FILL(bg), font: FONT({ bold: true }),
            alignment: AL('left','top', true), border: bAll
        });
        // Col 1 — Sector
        ws[C(r,1)] = mkCell(row.sector, {
            fill: FILL(bg), font: FONT(),
            alignment: AL('left','top', true), border: bAll
        });
        // Col 2 — Missing Technical Skills (each skill on its own line via \r\n)
        ws[C(r,2)] = {
            t: 's',
            v: techText,
            s: { fill: FILL(bg), font: FONT(), alignment: AL('left','top', true), border: bAll }
        };
        // Col 3 — Missing Soft Skills (each skill on its own line via \r\n)
        ws[C(r,3)] = {
            t: 's',
            v: softText,
            s: { fill: FILL(bg), font: FONT(), alignment: AL('left','top', true), border: bAll }
        };
        // Col 4 — Salary Range
        ws[C(r,4)] = mkCell(row.salary, {
            fill: FILL(bg), font: FONT(),
            alignment: AL('left','center'), border: bAll
        });
        // Col 5 — Gap Impact (last column)
        ws[C(r,5)] = mkCell(row.impact, {
            fill: FILL(iBg), font: FONT({ bold: true }),
            alignment: AL('center','center'), border: bAll
        });

        // customHeight:true locks the hpt so Excel won't collapse it on open
        rowHeights.push({ hpt: rowHpt, customHeight: true });
    });

    // ── Sheet metadata ────────────────────────────────────────────────────────
    ws['!ref'] = XLSX.utils.encode_range({ s: { r: 0, c: 0 }, e: { r: 4 + rows.length, c: NC - 1 } });
    ws['!merges'] = [
        { s: { r: 0, c: 0 }, e: { r: 0, c: NC - 1 } }, // title
        { s: { r: 1, c: 0 }, e: { r: 1, c: NC - 1 } }, // generated on
        { s: { r: 2, c: 0 }, e: { r: 2, c: NC - 1 } }, // page info
        { s: { r: 3, c: 0 }, e: { r: 3, c: NC - 1 } }, // spacer
    ];
    ws['!cols'] = colW.map(wch => ({ wch }));
    ws['!rows'] = rowHeights;

    // ── Export ────────────────────────────────────────────────────────────────
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Competency Gap Analysis');
    const filenamePart = filterIsActive ? 'all-filtered' : `page${currentPage}`;
    XLSX.writeFile(wb, `lmi-competency-gap-analysis-${filenamePart}-${dateStr}.xlsx`, { bookType: 'xlsx', cellStyles: true });
}

// Helper function to escape CSV values
function escapeCSVValue(value) {
    if (value === null || value === undefined) {
        return '';
    }
    
    const stringValue = String(value);
    
    // If value contains comma, quote, or newline, wrap in quotes and escape quotes
    if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
        return '"' + stringValue.replace(/"/g, '""') + '"';
    }
    
    return stringValue;
}

// Initialize the export button when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportLMIMatrixBtn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            exportLMIMatrixToCSV();
        });
    }
});

// ─────────────────────────────────────────────────────────────────────────────
       document.addEventListener('DOMContentLoaded', function () {

    const steps    = document.querySelectorAll('.lmi-step');   // the 4 <div> wrappers
    const circles  = document.querySelectorAll('.step-circle');
    const lines    = document.querySelectorAll('.step-line');
    let current    = 0;

    // ─── INIT: hide all except first ────────────────────────
    window.showStep = function showStep(n) {
    steps.forEach((s, i) => s.style.display = (i === n) ? 'block' : 'none');
    current = n;
    updateIndicator();
    updateButtons();
    
    // ► HIDE INTRO SECTION AFTER STEP 1 ◄
    const introSection = document.getElementById('intro-section');
    if (introSection) {
        introSection.style.display = (n === 0) ? 'block' : 'none';
    }
    
    // scroll modal back to top
    const scrollable = document.querySelector('#lmi-form-content .overflow-y-auto');
    if (scrollable) scrollable.scrollTo({ top: 0, behavior: 'smooth' });
}

    // ─── INDICATOR ──────────────────────────────────────────
    function updateIndicator() {
        circles.forEach((c, i) => {
            c.classList.remove('bg-white','text-teal-700','bg-teal-500','text-white');
            if (i < current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
            } else if (i === current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = (i + 1).toString();
            } else {
                c.classList.add('bg-teal-500','text-white');
                c.innerHTML = (i + 1).toString();
            }
        });
        lines.forEach((l, i) => {
            l.classList.toggle('bg-white', i < current);
            l.classList.toggle('bg-teal-500', i >= current);
        });
    }

    // ─── BUTTONS ────────────────────────────────────────────
    function updateButtons() {
        steps.forEach((step, i) => {
            const prev   = step.querySelector('.btn-prev');
            const next   = step.querySelector('.btn-next');
            const submit = step.querySelector('.btn-submit-lmi');

            if (prev)   prev.style.display   = (i === 0) ? 'none' : 'inline-flex';
            if (next)   next.style.display   = (i === steps.length - 1) ? 'none' : 'inline-flex';
            if (submit) submit.style.display = (i === steps.length - 1) ? 'inline-flex' : 'none';
        });
    }

    // ─── VALIDATION ─────────────────────────────────────────
    window.validateStep = function validateStep(idx) {
        const step  = steps[idx];
        let   valid = true;

        // -- text / email / tel --
        step.querySelectorAll('input[type="text"][required], input[type="email"][required], input[type="tel"][required]').forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // -- Contact number length check (step 0 only) --
        if (idx === 0) {
            const contactType   = document.getElementById('contact_type_input');
            const mobileInp     = document.getElementById('mobile-input');
            const telephoneInp  = document.getElementById('telephone-input');
            const contactHint   = document.getElementById('contact-hint');

            if (contactType && contactType.value === 'mobile' && mobileInp && !mobileInp.disabled) {
                const digits = mobileInp.value.replace(/\D/g, '');
                const required = selectedCountry.maxDigits;
                if (digits.length !== required) {
                    mobileInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = `<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Mobile number must be exactly ${required} digits for ${selectedCountry.name}</span>`;
                    }
                    valid = false;
                } else {
                    mobileInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${required}-digit mobile number (${selectedCountry.name})`;
                    }
                }
            } else if (contactType && contactType.value === 'telephone' && telephoneInp && !telephoneInp.disabled) {
                const digits = telephoneInp.value.replace(/\D/g, '');
                if (digits.length !== 10) {
                    telephoneInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Telephone number must be exactly 10 digits</span>';
                    }
                    valid = false;
                } else {
                    telephoneInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 10-digit telephone number';
                    }
                }
            }
        }

        // -- Email format check (step 0 only) --
        if (idx === 0) {
            const emailInput = step.querySelector('input[type="email"]');
            const emailError = document.getElementById('emailError');
            if (emailInput && emailInput.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('border-red-500');
                    if (emailError) emailError.classList.remove('hidden');
                    valid = false;
                } else {
                    emailInput.classList.remove('border-red-500');
                    if (emailError) emailError.classList.add('hidden');
                }
            } else if (emailInput && !emailInput.value.trim()) {
                if (emailError) emailError.classList.add('hidden');
            }
        }

        // -- hidden inputs (dropdowns: industrySelector, companySize, job_classification, vacancy_duration, salary_range) --
        step.querySelectorAll('input[type="hidden"][required]').forEach(input => {
            const wrapper = input.closest('.relative');
            const btn     = wrapper ? wrapper.querySelector('button[type="button"]') : null;
            if (!input.value) {
                valid = false;
                if (btn) btn.classList.add('border-red-500');
            } else {
                if (btn) btn.classList.remove('border-red-500');
            }
        });

        // -- Step 2 (idx 1): validate salary range and "Below ₱30,000" for every job entry --
        if (idx === 1) {
            step.querySelectorAll('.job-entry').forEach(jobEntry => {
                const salaryRangeInput = jobEntry.querySelector('.salary-range-input');
                const salaryRangeBtn = jobEntry.querySelector('.salary-range-btn');
                const below30kInput = jobEntry.querySelector('.below-30k-salary-input');
                const below30kContainer = jobEntry.querySelector('.below-30k-input-container');
                
                // Check if salary range is selected
                if (salaryRangeInput && !salaryRangeInput.value) {
                    valid = false;
                    if (salaryRangeBtn) salaryRangeBtn.classList.add('border-red-500');
                } else {
                    if (salaryRangeBtn) salaryRangeBtn.classList.remove('border-red-500');
                    
                    // If "Below ₱30,000" is selected, validate the input field
                    if (salaryRangeInput && salaryRangeInput.value === '__below_30k__') {
                        if (below30kInput && !below30kInput.value.trim()) {
                            valid = false;
                            below30kInput.classList.add('border-red-500');
                        } else if (below30kInput) {
                            below30kInput.classList.remove('border-red-500');
                            // Validate that the amount is less than 30,000
                            const amount = parseInt(below30kInput.value.replace(/,/g, ''));
                            if (isNaN(amount) || amount >= 30000) {
                                valid = false;
                                below30kInput.classList.add('border-red-500');
                                alert('Salary amount must be less than ₱30,000');
                            } else {
                                // Replace sentinel with the actual numeric value
                                salaryRangeInput.value = String(amount);
                            }
                        }
                    } else if (salaryRangeInput && salaryRangeInput.value === 'Below ₱30,000') {
                        if (below30kInput && !below30kInput.value.trim()) {
                            valid = false;
                            below30kInput.classList.add('border-red-500');
                        } else if (below30kInput) {
                            below30kInput.classList.remove('border-red-500');
                            
                            // Validate that the amount is less than 30,000
                            const amount = parseInt(below30kInput.value.replace(/,/g, ''));
                            if (isNaN(amount) || amount >= 30000) {
                                valid = false;
                                below30kInput.classList.add('border-red-500');
                                alert('Salary amount must be less than ₱30,000');
                            }
                        }
                    }
                }

                // FIX 3: At least one skill must be checked, and if checked must have >=1 tag
                // NOTE: hidden input stores tags as comma-separated string e.g. "Python, SQL"
                // so we check .trim().length > 0, NOT JSON.parse
                const techCheckbox = jobEntry.querySelector('.technical-checkbox');
                const techSkillsLabel = jobEntry.querySelector('.technical-skills-label');
                const techSkillInput = jobEntry.querySelector('.technical-skill-input');

                const softCheckbox = jobEntry.querySelector('.soft-checkbox');
                const softSkillsLabel = jobEntry.querySelector('.soft-skills-label');
                const softSkillInput = jobEntry.querySelector('.soft-skill-input');

                const eitherChecked = (techCheckbox && techCheckbox.checked) || (softCheckbox && softCheckbox.checked);

                if (!eitherChecked) {
                    // Condition 1: neither checked - highlight both, block next
                    valid = false;
                    if (techSkillsLabel) techSkillsLabel.classList.add('border-red-500');
                    if (softSkillsLabel) softSkillsLabel.classList.add('border-red-500');
                } else {
                    // Condition 2 & 3: at least one is checked
                    if (techCheckbox && techCheckbox.checked) {
                        const techHidden = jobEntry.querySelector('.technical-skills-input');
                        const hasTechTags = techHidden && techHidden.value.trim().length > 0;
                        if (!hasTechTags) {
                            valid = false;
                            if (techSkillsLabel) techSkillsLabel.classList.add('border-red-500');
                            if (techSkillInput) techSkillInput.classList.add('border-red-500');
                        } else {
                            if (techSkillsLabel) techSkillsLabel.classList.remove('border-red-500');
                            if (techSkillInput) techSkillInput.classList.remove('border-red-500');
                        }
                    } else {
                        // Unchecked - always clear any lingering red border
                        if (techSkillsLabel) techSkillsLabel.classList.remove('border-red-500');
                        if (techSkillInput) techSkillInput.classList.remove('border-red-500');
                    }

                    if (softCheckbox && softCheckbox.checked) {
                        const softHidden = jobEntry.querySelector('.soft-skills-input');
                        const hasSoftTags = softHidden && softHidden.value.trim().length > 0;
                        if (!hasSoftTags) {
                            valid = false;
                            if (softSkillsLabel) softSkillsLabel.classList.add('border-red-500');
                            if (softSkillInput) softSkillInput.classList.add('border-red-500');
                        } else {
                            if (softSkillsLabel) softSkillsLabel.classList.remove('border-red-500');
                            if (softSkillInput) softSkillInput.classList.remove('border-red-500');
                        }
                    } else {
                        // Unchecked - always clear any lingering red border
                        if (softSkillsLabel) softSkillsLabel.classList.remove('border-red-500');
                        if (softSkillInput) softSkillInput.classList.remove('border-red-500');
                    }
                }
            });
        }

        // -- radio groups --
        const radioNames = new Set();
        step.querySelectorAll('input[type="radio"][required]').forEach(r => radioNames.add(r.name));
        radioNames.forEach(name => {
            const checked = step.querySelector(`input[type="radio"][name="${name}"]:checked`);
            const radios  = step.querySelectorAll(`input[type="radio"][name="${name}"]`);
            if (!checked) {
                valid = false;
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.add('border-red-500'); });
            } else {
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.remove('border-red-500'); });
            }
        });

        // -- Step 3 (idx 2): at least one rejection_reasons checkbox --
        if (idx === 2) {
            const checked = step.querySelectorAll('input[name="rejection_reasons[]"]:checked');
            if (checked.length === 0) {
                valid = false;
                step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                    const p = cb.closest('label') || cb.closest('.other-rejection-option');
                    if (p) p.classList.add('border-red-500');
                });
            } else {
                step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                    const p = cb.closest('label') || cb.closest('.other-rejection-option');
                    if (p) p.classList.remove('border-red-500');
                });
            }

            // Always check: if "Other" rejection is checked, text must be filled
            const otherRejectionCb = step.querySelector('.other-rejection-checkbox');
            const otherRejectionText = step.querySelector('textarea[name="rejection_reasons_other"]');
            if (otherRejectionCb && otherRejectionCb.checked) {
                if (otherRejectionText && !otherRejectionText.value.trim()) {
                    valid = false;
                    otherRejectionText.classList.add('border-red-500');
                    otherRejectionText.placeholder = 'This field is required';
                } else if (otherRejectionText) {
                    otherRejectionText.classList.remove('border-red-500');
                }
            } else if (otherRejectionText) {
                otherRejectionText.classList.remove('border-red-500');
            }

            // Always check: if "Other" coordination is selected, text must be filled
            const otherCoordCb = step.querySelector('.other-coordination-radio');
            const otherCoordText = step.querySelector('textarea[name="coordination_frequency_other"]');
            if (otherCoordCb && otherCoordCb.checked) {
                if (otherCoordText && !otherCoordText.value.trim()) {
                    valid = false;
                    otherCoordText.classList.add('border-red-500');
                    otherCoordText.placeholder = 'This field is required';
                } else if (otherCoordText) {
                    otherCoordText.classList.remove('border-red-500');
                }
            } else if (otherCoordText) {
                otherCoordText.classList.remove('border-red-500');
            }
        }

        // -- Step 4 (idx 3): consent + at least one lmi_features --
        if (idx === 3) {
            const consent = step.querySelector('input[name="consent"]');
            if (consent && !consent.checked) {
                valid = false;
                const lbl = consent.closest('label');
                if (lbl) lbl.classList.add('border-red-500');
            } else if (consent) {
                const lbl = consent.closest('label');
                if (lbl) lbl.classList.remove('border-red-500');
            }

            const lmiChecked = step.querySelectorAll('input[name="lmi_features[]"]:checked');
            const lmiFeaturesGroup = step.querySelector('#lmi-features-group');
            if (lmiChecked.length < 2 ) {
                valid = false;
                if (lmiFeaturesGroup) lmiFeaturesGroup.classList.add('border-2', 'border-red-400', 'rounded-lg', 'p-2');
            } else {
                if (lmiFeaturesGroup) lmiFeaturesGroup.classList.remove('border-2', 'border-red-400', 'rounded-lg', 'p-2');
            }

            // If "Other" LMI feature is checked, its text must be filled
            const lmiOtherCb = step.querySelector('.lmi-other-checkbox');
            const otherText = step.querySelector('textarea[name="lmi_features_other"]');
            if (lmiOtherCb && lmiOtherCb.checked) {
                if (otherText && !otherText.value.trim()) {
                    valid = false;
                    otherText.classList.add('border-red-500');
                    otherText.placeholder = 'This field is required';
                } else if (otherText) {
                    otherText.classList.remove('border-red-500');
                }
            } else if (otherText) {
                otherText.classList.remove('border-red-500');
            }
        }

        // scroll to first red field
        if (!valid) {
            const bad = step.querySelector('.border-red-500');
            if (bad) bad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid;
    }

    // ─── BIND NEXT / PREV clicks ────────────────────────────
    steps.forEach((step, i) => {
        const next = step.querySelector('.btn-next');
        const prev = step.querySelector('.btn-prev');

        if (next) {
            next.addEventListener('click', function () {
                if (validateStep(i)) showStep(i + 1);
            });
        }
        if (prev) {
            prev.addEventListener('click', function () {
                if (i > 0) showStep(i - 1);
            });
        }
    });
        
    // ─── INIT ───────────────────────────────────────────────
    showStep(0);
});

// ─────────────────────────────────────────────────────────────────────────────
// ─── Contact Number Toggle ────────────────────────────────────────────────────
function switchContactType(type) {
    const mobileWrapper    = document.getElementById("mobile-input-wrapper");
    const telephoneWrapper = document.getElementById("telephone-input-wrapper");
    const mobileInput      = document.getElementById("mobile-input");
    const telephoneInput   = document.getElementById("telephone-input");
    const hint             = document.getElementById("contact-hint");
    const contactTypeInput = document.getElementById("contact_type_input");
    const toggleMobile     = document.getElementById("toggle-mobile");
    const toggleTelephone  = document.getElementById("toggle-telephone");

    [toggleMobile, toggleTelephone].forEach(btn => {
        btn.classList.remove("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        btn.classList.add("text-gray-500");
    });

    if (type === "mobile") {
        mobileWrapper.classList.remove("hidden");
        telephoneWrapper.classList.add("hidden");
        mobileInput.disabled = false;
        mobileInput.required = true;
        telephoneInput.disabled = true;
        telephoneInput.required = false;
        telephoneInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> Enter your mobile number with country code";
        contactTypeInput.value = "mobile";
        toggleMobile.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleMobile.classList.remove("text-gray-500");
        // Sync carrier
        const carrier = document.getElementById('contact_number_carrier');
        if (carrier) carrier.value = selectedCountry.dial + mobileInput.value;
    } else {
        telephoneWrapper.classList.remove("hidden");
        mobileWrapper.classList.add("hidden");
        telephoneInput.disabled = false;
        telephoneInput.required = true;
        mobileInput.disabled = true;
        mobileInput.required = false;
        mobileInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> Auto-formats to 082-123-4567";
        contactTypeInput.value = "telephone";
        toggleTelephone.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleTelephone.classList.remove("text-gray-500");
        // Sync carrier
        const carrier = document.getElementById('contact_number_carrier');
        if (carrier) carrier.value = telephoneInput.value;
        telephoneInput.focus();
    }
}

// ─── Country Code Selector ────────────────────────────────────────────────────
const COUNTRIES = [
    { flag: '🇵🇭', name: 'Philippines',   dial: '+63',  maxDigits: 10 },
    { flag: '🇺🇸', name: 'United States', dial: '+1',   maxDigits: 10 },
    { flag: '🇬🇧', name: 'United Kingdom',dial: '+44',  maxDigits: 10 },
    { flag: '🇦🇺', name: 'Australia',     dial: '+61',  maxDigits: 9  },
    { flag: '🇨🇦', name: 'Canada',        dial: '+1',   maxDigits: 10 },
    { flag: '🇯🇵', name: 'Japan',         dial: '+81',  maxDigits: 10 },
    { flag: '🇰🇷', name: 'South Korea',   dial: '+82',  maxDigits: 10 },
    { flag: '🇸🇬', name: 'Singapore',     dial: '+65',  maxDigits: 8  },
    { flag: '🇲🇾', name: 'Malaysia',      dial: '+60',  maxDigits: 9  },
    { flag: '🇮🇩', name: 'Indonesia',     dial: '+62',  maxDigits: 11 },
    { flag: '🇹🇭', name: 'Thailand',      dial: '+66',  maxDigits: 9  },
    { flag: '🇻🇳', name: 'Vietnam',       dial: '+84',  maxDigits: 9  },
    { flag: '🇮🇳', name: 'India',         dial: '+91',  maxDigits: 10 },
    { flag: '🇨🇳', name: 'China',         dial: '+86',  maxDigits: 11 },
    { flag: '🇭🇰', name: 'Hong Kong',     dial: '+852', maxDigits: 8  },
    { flag: '🇹🇼', name: 'Taiwan',        dial: '+886', maxDigits: 9  },
    { flag: '🇸🇦', name: 'Saudi Arabia',  dial: '+966', maxDigits: 9  },
    { flag: '🇦🇪', name: 'UAE',           dial: '+971', maxDigits: 9  },
    { flag: '🇶🇦', name: 'Qatar',         dial: '+974', maxDigits: 8  },
    { flag: '🇩🇪', name: 'Germany',       dial: '+49',  maxDigits: 11 },
    { flag: '🇫🇷', name: 'France',        dial: '+33',  maxDigits: 9  },
    { flag: '🇮🇹', name: 'Italy',         dial: '+39',  maxDigits: 10 },
    { flag: '🇪🇸', name: 'Spain',         dial: '+34',  maxDigits: 9  },
    { flag: '🇳🇱', name: 'Netherlands',   dial: '+31',  maxDigits: 9  },
    { flag: '🇳🇿', name: 'New Zealand',   dial: '+64',  maxDigits: 9  },
    { flag: '🇧🇷', name: 'Brazil',        dial: '+55',  maxDigits: 11 },
    { flag: '🇲🇽', name: 'Mexico',        dial: '+52',  maxDigits: 10 },
    { flag: '🇿🇦', name: 'South Africa',  dial: '+27',  maxDigits: 9  },
    { flag: '🇳🇬', name: 'Nigeria',       dial: '+234', maxDigits: 10 },
    { flag: '🇰🇪', name: 'Kenya',         dial: '+254', maxDigits: 9  },
];

let selectedCountry = COUNTRIES[0]; // Default: Philippines

function renderCountryList(filter = '') {
    const list = document.getElementById('country-list');
    if (!list) return;

    const filtered = COUNTRIES.filter(c =>
        c.name.toLowerCase().includes(filter.toLowerCase()) ||
        c.dial.includes(filter)
    );

    list.innerHTML = filtered.length
        ? filtered.map(c => `
            <div class="country-option flex items-center gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm transition border-b border-gray-50 last:border-b-0"
                 data-dial="${c.dial}" data-flag="${c.flag}" data-name="${c.name}" data-max-digits="${c.maxDigits}">
                <span class="text-lg">${c.flag}</span>
                <span class="flex-1 text-gray-700">${c.name}</span>
                <span class="text-gray-400 font-mono text-xs">${c.dial}</span>
            </div>`).join('')
        : '<div class="px-4 py-3 text-sm text-gray-400 text-center">No results found</div>';

    list.querySelectorAll('.country-option').forEach(opt => {
        opt.addEventListener('click', () => {
            selectedCountry = {
                flag: opt.dataset.flag,
                name: opt.dataset.name,
                dial: opt.dataset.dial,
                maxDigits: parseInt(opt.dataset.maxDigits),
            };
            document.getElementById('country-flag').textContent  = selectedCountry.flag;
            document.getElementById('country-dial-code').textContent = selectedCountry.dial;
            document.getElementById('country-dropdown').classList.add('hidden');

            // Update maxlength and hint based on selected country
            const mobileInput = document.getElementById('mobile-input');
            if (mobileInput) {
                mobileInput.maxLength = selectedCountry.maxDigits;
                mobileInput.value = mobileInput.value.slice(0, selectedCountry.maxDigits);
            }
            const hint = document.getElementById('contact-hint');
            if (hint) {
                hint.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${selectedCountry.maxDigits}-digit mobile number (${selectedCountry.name})`;
            }

            // Keep carrier in sync
            const carrier = document.getElementById('contact_number_carrier');
            if (carrier) carrier.value = selectedCountry.dial + (mobileInput?.value || '');
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderCountryList();

    const ccBtn     = document.getElementById('country-code-btn');
    const ccDropdown= document.getElementById('country-dropdown');
    const ccSearch  = document.getElementById('country-search');

    // Set initial maxlength for default country (Philippines = 10)
    const mobileInputInit = document.getElementById('mobile-input');
    if (mobileInputInit) mobileInputInit.maxLength = selectedCountry.maxDigits;

    if (ccBtn) {
        ccBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            ccDropdown.classList.toggle('hidden');
            if (!ccDropdown.classList.contains('hidden')) {
                ccSearch.value = '';
                renderCountryList();
                setTimeout(() => ccSearch.focus(), 50);
            }
        });
    }

    if (ccSearch) {
        ccSearch.addEventListener('input', () => renderCountryList(ccSearch.value));
        // Prevent Enter from submitting form while searching
        ccSearch.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });
    }

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!ccBtn?.contains(e.target) && !ccDropdown?.contains(e.target)) {
            ccDropdown?.classList.add('hidden');
        }
    });

    // Keep carrier in sync as user types the number
    const mobileInput = document.getElementById('mobile-input');
    if (mobileInput) {
        mobileInput.addEventListener('input', () => {
            const carrier = document.getElementById('contact_number_carrier');
            if (carrier) carrier.value = selectedCountry.dial + mobileInput.value;
        });
    }
});

// ─── Telephone Auto-Formatter + Area Code Suggestions ────────────────────────
document.addEventListener("DOMContentLoaded", function () {
    const telInput   = document.getElementById("telephone-input");
    const suggestBox = document.getElementById("area-code-suggestions");
    const suggestList= document.getElementById("area-code-list");
    if (!telInput) return;

    // ── Complete PH Area Code Directory ──────────────────────────────────────
    // Format: { code: "0XX", label: "Province / City" }
    // Source: Wikipedia "Telephone numbers in the Philippines" + NTC 2025
    const PH_AREA_CODES = [
        // Metro Manila & surroundings (area code 2 — 8-digit local)
        { code: "02", label: "Metro Manila, Rizal, Bacoor, San Pedro" },

        // Luzon
        { code: "032", label: "Cebu" },
        { code: "033", label: "Guimaras, Iloilo (part)" },
        { code: "034", label: "Iloilo, Negros Occidental" },
        { code: "035", label: "Negros Oriental, Siquijor" },
        { code: "036", label: "Aklan, Antique, Capiz" },
        { code: "038", label: "Bohol" },
        { code: "042", label: "Aurora, Marinduque, Quezon" },
        { code: "043", label: "Batangas, Occidental Mindoro, Oriental Mindoro" },
        { code: "044", label: "Bulacan, Nueva Ecija" },
        { code: "045", label: "Pampanga, Tarlac" },
        { code: "046", label: "Cavite (except Bacoor)" },
        { code: "047", label: "Bataan, Zambales" },
        { code: "048", label: "Palawan" },
        { code: "049", label: "Laguna (except San Pedro)" },
        { code: "052", label: "Albay, Catanduanes" },
        { code: "053", label: "Biliran, Leyte, Southern Leyte" },
        { code: "054", label: "Camarines Norte, Camarines Sur, Romblon" },
        { code: "055", label: "Eastern Samar, Northern Samar, Samar" },
        { code: "056", label: "Masbate, Sorsogon" },
        { code: "062", label: "Basilan, Zamboanga del Sur, Zamboanga Sibugay" },
        { code: "063", label: "Lanao del Norte" },
        { code: "064", label: "Lanao del Sur, Maguindanao, North Cotabato, Sultan Kudarat" },
        { code: "065", label: "Zamboanga del Norte" },
        { code: "068", label: "Tawi-Tawi" },
        { code: "072", label: "La Union" },
        { code: "074", label: "Abra, Benguet, Ifugao, Kalinga, Mountain Province" },
        { code: "075", label: "Pangasinan" },
        { code: "077", label: "Ilocos Norte, Ilocos Sur" },
        { code: "078", label: "Apayao, Batanes, Cagayan, Isabela, Nueva Vizcaya, Quirino" },

        // Mindanao
        { code: "082", label: "Davao del Sur, Davao Occidental" },
        { code: "083", label: "Sarangani, South Cotabato" },
        { code: "084", label: "Compostela Valley, Davao del Norte" },
        { code: "085", label: "Agusan del Norte, Agusan del Sur, Sulu" },
        { code: "086", label: "Dinagat Islands, Surigao del Norte, Surigao del Sur" },
        { code: "087", label: "Davao de Oro, Davao Oriental" },
        { code: "088", label: "Bukidnon, Camiguin, Misamis Occidental, Misamis Oriental" },
    ];

    // ── Format telephone digits → readable string ─────────────────────────────
    // Area code "2"  (Metro Manila): 02-XXXX-XXXX  (1+8 digits)
    // All others:                    0XX-XXX-XXXX   (2+7 digits)
    function formatTelephone(digits) {
        if (!digits) return "";
        if (!digits.startsWith("0")) digits = "0" + digits;

        const withoutTrunk = digits.slice(1);

        if (withoutTrunk.startsWith("2")) {
            const local = withoutTrunk.slice(1);
            if (local.length === 0) return "02";
            if (local.length <= 4)  return "02-" + local;
            return "02-" + local.slice(0, 4) + "-" + local.slice(4);
        }

        const area  = withoutTrunk.slice(0, 2);
        const local = withoutTrunk.slice(2);
        if (local.length === 0) return "0" + area;
        if (local.length <= 3)  return "0" + area + "-" + local;
        return "0" + area + "-" + local.slice(0, 3) + "-" + local.slice(3);
    }

    // ── Show/hide suggestion dropdown ─────────────────────────────────────────
    let activeIndex = -1; // for keyboard navigation

    function showSuggestions(typedDigits) {
        if (!typedDigits || typedDigits.length < 2) {
            hideSuggestions();
            return;
        }

        // Only show suggestions while user is still typing the area code
        // (i.e. total digits typed is 3 or less — "0", "08", "082")
        // Once they go past the area code into local number, hide suggestions
        if (typedDigits.length > 3) {
            hideSuggestions();
            return;
        }

        const matches = PH_AREA_CODES.filter(ac =>
            ac.code.startsWith(typedDigits)
        );

        if (matches.length === 0) {
            hideSuggestions();
            return;
        }

        suggestList.innerHTML = "";
        activeIndex = -1;

        matches.forEach((ac, i) => {
            const item = document.createElement("div");
            item.className = "suggestion-item flex items-center gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b border-gray-50 last:border-b-0 transition-colors";
            item.dataset.index = i;

            // Highlight the matching part of the area code
            const typed      = typedDigits;
            const codeHtml   = `<span class="font-bold text-teal-600">${ac.code.slice(0, typed.length)}</span><span class="font-bold text-gray-800">${ac.code.slice(typed.length)}</span>`;

            item.innerHTML = `
                <span class="shrink-0 text-xs font-mono bg-teal-50 text-teal-700 border border-teal-200 rounded px-2 py-0.5">${codeHtml}</span>
                <span class="text-sm text-gray-600 truncate">${ac.label}</span>
            `;

            item.addEventListener("mousedown", function (e) {
                e.preventDefault(); // prevent input blur before click fires
                selectAreaCode(ac);
            });

            suggestList.appendChild(item);
        });

        suggestBox.classList.remove("hidden");
    }

    function hideSuggestions() {
        suggestBox.classList.add("hidden");
        activeIndex = -1;
    }

    function selectAreaCode(ac) {
        // Fill the input with the area code + dash, ready for local number
        // e.g. selecting "082" → input becomes "082-"
        telInput.value = ac.code + "-";
        hideSuggestions();
        telInput.focus();
    }

    // ── Keyboard navigation through suggestions ───────────────────────────────
    function navigateSuggestions(direction) {
        const items = suggestList.querySelectorAll(".suggestion-item");
        if (items.length === 0) return;

        // Remove highlight from current
        items.forEach(i => i.classList.remove("bg-teal-50"));

        activeIndex += direction;
        if (activeIndex < 0)             activeIndex = items.length - 1;
        if (activeIndex >= items.length) activeIndex = 0;

        items[activeIndex].classList.add("bg-teal-50");
        items[activeIndex].scrollIntoView({ block: "nearest" });
    }

    // ── Event Listeners ───────────────────────────────────────────────────────
    telInput.addEventListener("input", function (e) {
        let digits = e.target.value.replace(/\D/g, "");
        if (digits.length > 10) digits = digits.slice(0, 10);

        // Show suggestions only when typing area code portion
        showSuggestions(digits.length <= 3 ? digits : null);

        // Format the number
        e.target.value = formatTelephone(digits);
    });

    telInput.addEventListener("keydown", function (e) {
        // Navigation keys for suggestion dropdown
        if (!suggestBox.classList.contains("hidden")) {
            if (e.key === "ArrowDown") { e.preventDefault(); navigateSuggestions(1);  return; }
            if (e.key === "ArrowUp")   { e.preventDefault(); navigateSuggestions(-1); return; }
            if (e.key === "Enter") {
                e.preventDefault();
                const items = suggestList.querySelectorAll(".suggestion-item");
                if (activeIndex >= 0 && items[activeIndex]) {
                    const code  = items[activeIndex].querySelector("span").textContent.trim();
                    const match = PH_AREA_CODES.find(ac => ac.code === items[activeIndex].querySelector("span").textContent.replace(/\s/g,'').replace(/[^0-9]/g,'') );
                    // Simpler: just click the active item
                    items[activeIndex].dispatchEvent(new MouseEvent("mousedown"));
                }
                return;
            }
            if (e.key === "Escape") { hideSuggestions(); return; }
        }

        // Block non-numeric keys
        const allowedKeys = ["Backspace","Delete","ArrowLeft","ArrowRight",
                             "ArrowUp","ArrowDown","Tab","Home","End"];
        const isDigit = e.key >= "0" && e.key <= "9";
        const isCtrl  = e.ctrlKey || e.metaKey;
        if (!isDigit && !allowedKeys.includes(e.key) && !isCtrl) {
            e.preventDefault();
        }
    });

    telInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData("text");
        let digits   = pasted.replace(/\D/g, "").slice(0, 10);
        e.target.value = formatTelephone(digits);
        hideSuggestions();
    });

    // Hide suggestions when clicking outside
    document.addEventListener("click", function (e) {
        if (!telInput.contains(e.target) && !suggestBox.contains(e.target)) {
            hideSuggestions();
        }
    });

    telInput.addEventListener("blur", function () {
        // Small delay so mousedown on suggestion fires first
        setTimeout(hideSuggestions, 150);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
(function () {
    const MONTH_SHORT = ['','Jan','Feb','Mar','Apr','May','Jun',
                         'Jul','Aug','Sep','Oct','Nov','Dec'];

    const htfArchiveOptions = window._jobMarketData.archiveOptions;

    // Pending = inside open panel, not yet applied
    let htfPendingYears  = [];
    let htfPendingMonths = []; // empty = all months
    let htfPendingMode  = 'range'; // 'range' | 'exact'

    // Applied = last committed values
    let htfAppliedYears  = [];
    let htfAppliedMonths = []; // empty = all months
    let htfAppliedMode   = 'range';

    /* ════════════════════════════
       BOOT
    ════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        buildHtfYearChips();

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('htfFilterWrapper');
            if (wrapper && !wrapper.contains(e.target)) htfPanelClose();
        });
    });

    /* ════════════════════════════
       BUILD YEAR CHIPS
    ════════════════════════════ */
    function buildHtfYearChips() {
        const container = document.getElementById('htfpYearChips');
        const yearHint  = document.getElementById('htfpYearHint');
        if (!container) return;
        container.innerHTML = '';

        // Update year hint based on mode
        if (yearHint) {
            yearHint.textContent = htfPendingMode === 'range'
                ? 'select From & To'
                : 'pick any years';
        }

        const years = [...new Set(htfArchiveOptions.map(o => String(o.year)))]
            .sort((a, b) => Number(b) - Number(a));

        if (!years.length) {
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">No archived data</span>';
            return;
        }

        years.forEach(yr => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'htfp-chip' + (htfPendingYears.includes(yr) ? ' htfp-selected' : '');
            btn.textContent = yr;
            btn.dataset.val = yr;
            btn.onclick     = () => htfToggleYear(btn, yr);
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════
       BUILD MONTH CHIPS
    ════════════════════════════ */
    function buildHtfMonthChips() {
        const container = document.getElementById('htfpMonthChips');
        const hint      = document.getElementById('htfpMonthHint');
        if (!container) return;
        container.innerHTML = '';

        if (!htfPendingYears.length) {
            if (hint) hint.textContent = 'select a year first';
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">Select a year to continue</span>';
            return;
        }

        // Update hint based on mode
        if (hint) {
            hint.textContent = htfPendingMode === 'range'
                ? 'select From & To month (optional)'
                : 'pick any specific months';
        }

        // In Range mode expand to all years between the two selected
        let yearsForMonths;
        if (htfPendingMode === 'range' && htfPendingYears.length === 2) {
            const minY = Math.min(...htfPendingYears.map(Number));
            const maxY = Math.max(...htfPendingYears.map(Number));
            yearsForMonths = [];
            for (let y = minY; y <= maxY; y++) yearsForMonths.push(String(y));
        } else {
            yearsForMonths = htfPendingYears.slice();
        }

        const availableMonths = [...new Set(
            htfArchiveOptions
                .filter(o => yearsForMonths.includes(String(o.year)))
                .map(o => o.month)
        )].sort((a, b) => a - b);

        if (!availableMonths.length) {
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">No months available</span>';
            return;
        }

        availableMonths.forEach(m => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'htfp-chip' + (htfPendingMonths.includes(String(m)) ? ' htfp-selected' : '');
            btn.textContent = MONTH_SHORT[m];
            btn.dataset.val = String(m);
            btn.onclick     = () => htfToggleMonth(btn, String(m));
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════
       TOGGLE HELPERS
    ════════════════════════════ */
    function htfToggleYear(btn, yr) {
        if (htfPendingYears.includes(yr)) {
            htfPendingYears = htfPendingYears.filter(y => y !== yr);
            btn.classList.remove('htfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (htfPendingMode === 'range' && htfPendingYears.length >= 2) {
                const evicted = htfPendingYears.shift();
                document.querySelector(`#htfpYearChips [data-val="${evicted}"]`)
                    ?.classList.remove('htfp-selected');
            }
            htfPendingYears.push(yr);
            btn.classList.add('htfp-selected');
        }
        htfPendingMonths = [];
        buildHtfMonthChips();
    }

    function htfToggleMonth(btn, m) {
        if (htfPendingMonths.includes(m)) {
            htfPendingMonths = htfPendingMonths.filter(x => x !== m);
            btn.classList.remove('htfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (htfPendingMode === 'range' && htfPendingMonths.length >= 2) {
                const evicted = htfPendingMonths.shift();
                document.querySelector(`#htfpMonthChips [data-val="${evicted}"]`)
                    ?.classList.remove('htfp-selected');
            }
            htfPendingMonths.push(m);
            btn.classList.add('htfp-selected');
        }
    }

    /* ════════════════════════════
       MODE TOGGLE (Range vs Exact)
    ════════════════════════════ */
    window.htfSetMode = function (mode) {
        htfPendingMode = mode;
        document.getElementById('htfModeRange')?.classList.toggle('mfp-mode-active', mode === 'range');
        document.getElementById('htfModeExact')?.classList.toggle('mfp-mode-active', mode === 'exact');
        const hint = document.getElementById('htfModeHint');
        if (hint) {
            hint.innerHTML = mode === 'range'
                ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
        }
        htfPendingYears  = [];
        htfPendingMonths = [];
        buildHtfYearChips();
        buildHtfMonthChips();
    };

    /* ════════════════════════════
       PANEL OPEN / CLOSE
    ════════════════════════════ */
    window.htfPanelToggle = function () {
        const panel = document.getElementById('htfFilterPanel');
        if (panel.classList.contains('htf-panel-open')) {
            htfPanelClose();
        } else {
            // Sync pending to applied state when reopening
            htfPendingYears  = htfAppliedYears.slice();
            htfPendingMonths = htfAppliedMonths.slice();
            htfPendingMode   = htfAppliedMode;
            document.getElementById('htfModeRange')?.classList.toggle('mfp-mode-active', htfPendingMode === 'range');
            document.getElementById('htfModeExact')?.classList.toggle('mfp-mode-active', htfPendingMode === 'exact');
            const htfModeHintEl = document.getElementById('htfModeHint');
            if (htfModeHintEl) {
                htfModeHintEl.innerHTML = htfPendingMode === 'range'
                    ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                    : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
            }
            buildHtfYearChips();
            buildHtfMonthChips();
            panel.classList.add('htf-panel-open');
            document.getElementById('htfFilterTrigger').classList.add('htf-open');
        }
    };

    function htfPanelClose() {
        document.getElementById('htfFilterPanel')?.classList.remove('htf-panel-open');
        document.getElementById('htfFilterTrigger')?.classList.remove('htf-open');
    }

    /* ════════════════════════════
       APPLY
    ════════════════════════════ */
    window.htfPanelApply = function () {
        if (!htfPendingYears.length) return;
        htfAppliedYears  = htfPendingYears.slice();
        htfAppliedMonths = htfPendingMonths.slice();
        htfAppliedMode   = htfPendingMode;
        htfPanelClose();
        htfUpdateTriggerLabel();
        updateHardToFill(htfAppliedYears, htfAppliedMonths, htfAppliedMode);
    };

    /* ════════════════════════════
       RESET
    ════════════════════════════ */
    window.htfPanelReset = function () {
        htfPendingYears  = [];
        htfPendingMonths = [];
        htfAppliedYears  = [];
        htfAppliedMonths = [];
        htfPendingMode   = 'range';
        htfAppliedMode   = 'range';
        htfPanelClose();
        htfUpdateTriggerLabel();
        updateHardToFill([], [], 'range');
    };

    /* ════════════════════════════
       TRIGGER LABEL
    ════════════════════════════ */
    function htfUpdateTriggerLabel() {
        const trigger = document.getElementById('htfFilterTrigger');
        const text    = document.getElementById('htfTriggerText');
        if (!text) return;
        if (htfAppliedYears.length) {
            let yLabel;
            if (htfAppliedMode === 'range' && htfAppliedYears.length === 2) {
                const mn = Math.min(...htfAppliedYears.map(Number));
                const mx = Math.max(...htfAppliedYears.map(Number));
                yLabel = mn === mx ? String(mn) : `${mn} – ${mx}`;
            } else {
                yLabel = htfAppliedYears.join(' & ');
            }
            const mLabel = htfAppliedMonths.length === 0
                ? 'All Months'
                : (htfAppliedMode === 'range' && htfAppliedMonths.length === 2)
                    ? (() => {
                        const mn = Math.min(...htfAppliedMonths.map(Number));
                        const mx = Math.max(...htfAppliedMonths.map(Number));
                        return mn === mx ? MONTH_SHORT[mn] : `${MONTH_SHORT[mn]} – ${MONTH_SHORT[mx]}`;
                    })()
                    : htfAppliedMonths.map(m => MONTH_SHORT[Number(m)]).join(' & ');
            text.textContent = `${yLabel} — ${mLabel}`;
            trigger.classList.add('htf-active');
        } else {
            text.textContent = 'Filter by Period';
            trigger.classList.remove('htf-active');
        }
    }

    /* ════════════════════════════
       CORE FETCH (unchanged logic)
    ════════════════════════════ */
    async function updateHardToFill(years, months, mode) {
        const spinner   = document.getElementById('htfSpinner');
        const badge     = document.getElementById('htfArchiveBadge');
        const label     = document.getElementById('htfArchiveLabel');
        const banner    = document.getElementById('htfBanner');
        const rolesList = document.getElementById('htfRolesList');

        spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams();
            if (years && years.length) {
                // Range mode: expand [2025, 2028] → 2025,2026,2027,2028
                let yearsToSend;
                if (mode === 'range' && years.length === 2) {
                    const minY = Math.min(...years.map(Number));
                    const maxY = Math.max(...years.map(Number));
                    yearsToSend = [];
                    for (let y = minY; y <= maxY; y++) yearsToSend.push(String(y));
                } else {
                    yearsToSend = years.slice();
                }
                yearsToSend.forEach(y => params.append('archive_years[]', y));

                // Range mode: expand [3, 10] → 3,4,5,6,7,8,9,10
                // Exact mode: send the specific months as-is
                if (months && months.length) {
                    let monthsToSend;
                    if (mode === 'range' && months.length === 2) {
                        const minM = Math.min(...months.map(Number));
                        const maxM = Math.max(...months.map(Number));
                        monthsToSend = [];
                        for (let m = minM; m <= maxM; m++) monthsToSend.push(String(m));
                    } else {
                        monthsToSend = months.slice();
                    }
                    monthsToSend.forEach(m => params.append('archive_months[]', m));
                }
            }

            const res  = await fetch(`/api/job-market/hard-to-fill-data?${params.toString()}`);
            const json = await res.json();

            // Update banner
            // NOTE: previously this only rebuilt 2 lines (title + date range), permanently
            // dropping the 3rd "Some data is archived..." warning line that the server-rendered
            // version includes. That line only applies to the default "Last 90 Days" state —
            // it doesn't show while actively viewing an archived period — so it's added here
            // conditionally on `!json.is_archive`, matching the original Blade markup.
            if (banner) {
                banner.className = `bg-blue-50 border-l-4 ${json.is_archive ? 'border-amber-400' : 'border-blue-500'} p-3 rounded-md`;
                banner.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-4 w-4 ${json.is_archive ? 'text-amber-500' : 'text-blue-500'} mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold ${json.is_archive ? 'text-amber-900' : 'text-blue-900'}">${json.is_archive ? 'Archived Quarter' : 'Last 90 Days'}</p>
                            <p class="text-xs ${json.is_archive ? 'text-amber-700' : 'text-blue-700'}">${json.quarter_text}</p>
                            ${!json.is_archive ? '<p class="text-xs font-semibold text-amber-700">Some data is archived and won\'t show by default. Use the year or month filter above to view it.</p>' : ''}
                        </div>
                    </div>`;
            }

            // Update roles list
            if (rolesList) {
                if (json.roles.length === 0) {
                    rolesList.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">No roles found for this period.</p>';
                } else {
                    // NOTE: was previously wrapped in `space-y-3` (old single-column stacked list),
                    // which no longer matches the current server-rendered layout. The default
                    // (unfiltered) view uses a responsive 3-column grid — this now matches it,
                    // so Apply/Reset produce the same layout as the initial page load.
                    rolesList.innerHTML = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-start">' +
                        json.roles.map(role => `
                            <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                 onclick="toggleRoleDetails(${role.submission_id}, ${role.index})">
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-sm text-slate-800">${role.job_title}</p>
                                            <p class="text-xs text-gray-400 mt-1">Vacancy Duration: ${role.vacancy_duration}</p>
                                        </div>
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="role-details hidden" id="role-details-${role.submission_id}-${role.index}">
                                    <div class="border-t border-slate-200 bg-slate-50 p-4">
                                        <div class="space-y-3 text-sm">
                                            <div>
                                                <span class="font-medium text-slate-600">Classification:</span>
                                                <p class="text-slate-800">${role.classification}</p>
                                            </div>
                                            ${role.difficulty_reasons.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Difficulty Reasons:</span>
                                                <ul class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                    ${role.difficulty_reasons.map(r => `<li>${r}</li>`).join('')}
                                                </ul>
                                            </div>` : ''}
                                            ${role.tech_skills.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Technical Skills Missing:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    ${role.tech_skills.map(s => `<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">${s}</span>`).join('')}
                                                </div>
                                            </div>` : ''}
                                            ${role.soft_skills.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Soft Skills Missing:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    ${role.soft_skills.map(s => `<span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">${s}</span>`).join('')}
                                                </div>
                                            </div>` : ''}
                                            <div class="pt-2 border-t">
                                                <p class="text-xs text-slate-500"><strong>Sector:</strong> ${role.sector}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                        ).join('') + '</div>';
                }
            }

            // Badge
            const isArchive = json.is_archive;
            badge.style.display = isArchive ? 'flex' : 'none';
            if (label) label.textContent = isArchive ? `Viewing archived: ${json.archive_label}` : '';

        } catch (e) {
            console.error('Hard-to-fill update failed:', e);
        }

        spinner.style.display = 'none';
    }
})();

// ─────────────────────────────────────────────────────────────────────────────
(function () {
    const MONTH_SHORT = ['','Jan','Feb','Mar','Apr','May','Jun',
                         'Jul','Aug','Sep','Oct','Nov','Dec'];
    const MONTH_FULL  = ['','January','February','March','April','May','June',
                         'July','August','September','October','November','December'];

    // Server-rendered fallback; overwritten by fresh API fetch on boot
    let matrixDateOptions = window._jobMarketData.matrixDateOptions;

    // Pending selections (inside open panel, not yet applied)
    let pendingYears  = [];
    let pendingMonths = [];
    let pendingMode   = 'range'; // 'range' | 'exact'

    // Last applied selections (used for trigger label after Apply)
    // Default to the current selected year, since that's what the server-rendered
    // matrix_results are actually scoped to on first load (see JobMarketDemandsController).
    let appliedYears  = window._jobMarketData.matrixSelectedYear ? [String(window._jobMarketData.matrixSelectedYear)] : [];
    let appliedMonths = [];
    let appliedMode   = 'range';

    /* ════════════════════════════════════════
       BOOT
    ════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', async function () {

        // Fetch fresh date options
        try {
            const res  = await fetch('/api/job-market/matrix-date-options');
            const json = await res.json();
            if (json.options && json.options.length) matrixDateOptions = json.options;
        } catch (e) {
            console.warn('Matrix date options fetch failed, using server fallback.');
        }

        buildYearChips();
        updateTriggerLabel(); // reflect the default year scope in the button immediately

        // Close panel when clicking outside wrapper
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('matrixFilterWrapper');
            if (wrapper && !wrapper.contains(e.target)) mfpClose();
        });
    });

    /* ════════════════════════════════════════
       BUILD YEAR CHIPS
    ════════════════════════════════════════ */
    function buildYearChips() {
        const container = document.getElementById('mfpYearChips');
        const yearHint  = document.getElementById('mfpYearHint');
        if (!container) return;
        container.innerHTML = '';

        // Update year hint based on mode
        if (yearHint) {
            yearHint.textContent = pendingMode === 'range'
                ? 'select From & To'
                : 'pick any years';
        }

        const years = [...new Set(matrixDateOptions.map(o => String(o.year)))]
            .sort((a, b) => Number(b) - Number(a));

        if (!years.length) {
            container.innerHTML = '<span class="mfp-chip mfp-disabled mfp-placeholder">No data available</span>';
            return;
        }

        years.forEach(yr => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'mfp-chip' + (pendingYears.includes(yr) ? ' mfp-selected' : '');
            btn.textContent = yr;
            btn.dataset.val = yr;
            btn.onclick     = () => toggleYear(btn, yr);
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════════════════
       BUILD MONTH CHIPS
    ════════════════════════════════════════ */
    function buildMonthChips() {
        const container = document.getElementById('mfpMonthChips');
        const hint      = document.getElementById('mfpMonthHint');
        if (!container) return;
        container.innerHTML = '';

        if (!pendingYears.length) {
            if (hint) hint.textContent = 'select a year first';
            container.innerHTML = '<span class="mfp-chip mfp-placeholder">Select a year to see months</span>';
            return;
        }

        // In Range mode, expand pendingYears to cover all years in between
        // e.g. [2025, 2028] → [2025, 2026, 2027, 2028] so months from middle years show up
        let yearsForMonths;
        if (pendingMode === 'range' && pendingYears.length === 2) {
            const minY = Math.min(...pendingYears.map(Number));
            const maxY = Math.max(...pendingYears.map(Number));
            yearsForMonths = [];
            for (let y = minY; y <= maxY; y++) yearsForMonths.push(String(y));
        } else {
            yearsForMonths = pendingYears.slice();
        }

        // Months available for the full year range
        const available = new Set(
            matrixDateOptions
                .filter(o => yearsForMonths.includes(String(o.year)))
                .map(o => o.month)
        );

        if (hint) hint.textContent = pendingMode === 'range'
            ? 'select From & To month (optional)'
            : 'pick any specific months';

        // Always show all 12 slots; disable those with no data
        for (let m = 1; m <= 12; m++) {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.dataset.val = String(m);

            if (available.has(m)) {
                btn.className   = 'mfp-chip' + (pendingMonths.includes(String(m)) ? ' mfp-selected' : '');
                btn.textContent = MONTH_SHORT[m];
                btn.onclick     = () => toggleMonth(btn, String(m));
            } else {
                btn.className   = 'mfp-chip mfp-disabled';
                btn.textContent = MONTH_SHORT[m];
            }
            container.appendChild(btn);
        }
    }

    /* ════════════════════════════════════════
       TOGGLE HELPERS
    ════════════════════════════════════════ */
    function toggleYear(btn, yr) {
        if (pendingYears.includes(yr)) {
            pendingYears = pendingYears.filter(y => y !== yr);
            btn.classList.remove('mfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (pendingMode === 'range' && pendingYears.length >= 2) {
                const evicted = pendingYears.shift();
                document.querySelector(`#mfpYearChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingYears.push(yr);
            btn.classList.add('mfp-selected');
        }
        // Reset months when year selection changes
        pendingMonths = [];
        buildMonthChips();
    }

    function toggleMonth(btn, m) {
        if (pendingMonths.includes(m)) {
            pendingMonths = pendingMonths.filter(x => x !== m);
            btn.classList.remove('mfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (pendingMode === 'range' && pendingMonths.length >= 2) {
                const evicted = pendingMonths.shift();
                document.querySelector(`#mfpMonthChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingMonths.push(m);
            btn.classList.add('mfp-selected');
        }
    }

    /* ════════════════════════════════════════
       MODE TOGGLE (Range vs Exact)
    ════════════════════════════════════════ */
    window.mfpSetMode = function (mode) {
        pendingMode = mode;
        document.getElementById('mfpModeRange')?.classList.toggle('mfp-mode-active', mode === 'range');
        document.getElementById('mfpModeExact')?.classList.toggle('mfp-mode-active', mode === 'exact');
        const hint = document.getElementById('mfpModeHint');
        if (hint) {
            hint.innerHTML = mode === 'range'
                ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
        }
        // Clear year/month selections when switching so the user starts fresh
        pendingYears  = [];
        pendingMonths = [];
        buildYearChips();
        buildMonthChips();
    };
    window.mfpToggle = function () {
        const panel = document.getElementById('matrixFilterPanel');
        if (panel.classList.contains('mfp-open')) {
            mfpClose();
        } else {
            // Sync pending selections from last applied state when reopening
            pendingYears  = appliedYears.slice();
            pendingMonths = appliedMonths.slice();
            pendingMode   = appliedMode;
            document.getElementById('mfpModeRange')?.classList.toggle('mfp-mode-active', pendingMode === 'range');
            document.getElementById('mfpModeExact')?.classList.toggle('mfp-mode-active', pendingMode === 'exact');
            const modeHint = document.getElementById('mfpModeHint');
            if (modeHint) {
                modeHint.innerHTML = pendingMode === 'range'
                    ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                    : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
            }
            buildYearChips();
            buildMonthChips();
            panel.classList.add('mfp-open');
            document.getElementById('matrixFilterTrigger').classList.add('mft-open');
        }
    };

    function mfpClose() {
        document.getElementById('matrixFilterPanel')?.classList.remove('mfp-open');
        document.getElementById('matrixFilterTrigger')?.classList.remove('mft-open');
    }

    /* ════════════════════════════════════════
       APPLY
    ════════════════════════════════════════ */
    window.mfpApply = async function () {
        if (!pendingYears.length) {
            mfpReset();
            return;
        }
        appliedYears  = pendingYears.slice();
        appliedMonths = pendingMonths.slice();
        appliedMode   = pendingMode;
        mfpClose();
        updateTriggerLabel();
        await matrixFetch(appliedYears, appliedMonths);
    };

    /* ════════════════════════════════════════
       RESET
    ════════════════════════════════════════ */
    window.mfpReset = function () {
        pendingYears  = [];
        pendingMonths = [];
        appliedYears  = [];
        appliedMonths = [];
        pendingMode   = 'range';
        appliedMode   = 'range';
        mfpClose();
        updateTriggerLabel();
        window.matrixResultsData = window.matrixResultsDataOriginal.slice();
        matrixApplyToAlpine(false, false, '', window.matrixResultsDataOriginal.slice());
    };

    /* ════════════════════════════════════════
       TRIGGER LABEL
    ════════════════════════════════════════ */
    function updateTriggerLabel() {
        const trigger = document.getElementById('matrixFilterTrigger');
        const text    = document.getElementById('mfpTriggerText');
        if (!text) return;

        if (!appliedYears.length) {
            text.textContent = 'Filter by Period';
            trigger.classList.remove('mft-active');
        } else {
            const yLabel = (appliedMode === 'range' && appliedYears.length === 2)
                ? (() => { const mn = Math.min(...appliedYears.map(Number)), mx = Math.max(...appliedYears.map(Number)); return mn === mx ? String(mn) : `${mn} – ${mx}`; })()
                : appliedYears.join(' & ');
            const mLabel = appliedMonths.length === 0
                ? 'All Months'
                : (appliedMode === 'range' && appliedMonths.length === 2)
                    ? (() => {
                        const mn = Math.min(...appliedMonths.map(Number)), mx = Math.max(...appliedMonths.map(Number));
                        return mn === mx ? MONTH_SHORT[mn] : `${MONTH_SHORT[mn]} – ${MONTH_SHORT[mx]}`;
                    })()
                    : appliedMonths.map(m => MONTH_SHORT[Number(m)]).join(' & ');
            text.textContent = `${yLabel} — ${mLabel}`;
            trigger.classList.add('mft-active');
        }
    }

    /* ════════════════════════════════════════
       CORE FETCH
    ════════════════════════════════════════ */
    async function matrixFetch(years, months) {
        const spinner = document.getElementById('matrixSpinner');
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams();

            // Range mode: expand e.g. [2026, 2028] → [2026, 2027, 2028]
            // Exact mode: send only the selected years as-is
            let yearsToFetch;
            if (appliedMode === 'range' && years.length === 2) {
                const minY = Math.min(...years.map(Number));
                const maxY = Math.max(...years.map(Number));
                yearsToFetch = [];
                for (let y = minY; y <= maxY; y++) yearsToFetch.push(String(y));
            } else {
                yearsToFetch = years.slice();
            }
            yearsToFetch.forEach(y => params.append('years[]', y));

            // Range mode: expand months e.g. [3, 10] → [3,4,5,6,7,8,9,10]
            // Exact mode: send the selected months as-is
            let monthsToFetch;
            if (appliedMode === 'range' && months.length === 2) {
                const minM = Math.min(...months.map(Number));
                const maxM = Math.max(...months.map(Number));
                monthsToFetch = [];
                for (let m = minM; m <= maxM; m++) monthsToFetch.push(String(m));
            } else {
                monthsToFetch = months.slice();
            }
            monthsToFetch.forEach(m => params.append('months[]', m));

            const res  = await fetch(`/api/job-market/matrix-data?${params}`);
            const json = await res.json();

            const formatted = (json.results || []).map(r => ({
                ...r,
                salary_range: typeof formatSalaryRange === 'function'
                    ? formatSalaryRange(r.salary_range)
                    : r.salary_range,
            }));

            window.matrixResultsData = formatted;

            const yLabel = (appliedMode === 'range' && years.length === 2)
                ? (() => { const mn = Math.min(...years.map(Number)), mx = Math.max(...years.map(Number)); return mn === mx ? String(mn) : `${mn} – ${mx}`; })()
                : years.join(' & ');
            // Range mode badge: "Jan – Dec", Exact mode: "Jan & Dec"
            const mLabel = months.length === 0
                ? 'All Months'
                : (appliedMode === 'range' && months.length === 2)
                    ? (() => {
                        const mn = Math.min(...months.map(Number)), mx = Math.max(...months.map(Number));
                        return mn === mx ? MONTH_FULL[mn] : `${MONTH_FULL[mn]} – ${MONTH_FULL[mx]}`;
                    })()
                    : months.map(m => MONTH_FULL[Number(m)]).join(' & ');

            matrixApplyToAlpine(true, true, `${yLabel} — ${mLabel}`, formatted);

        } catch (e) {
            console.error('Matrix filter fetch failed:', e);
        }

        if (spinner) spinner.style.display = 'none';
    }

    /* ════════════════════════════════════════
       PUSH INTO ALPINE
    ════════════════════════════════════════ */
    function matrixApplyToAlpine(filterActive, showAll, badgeText, data) {
        const scrollArea = document.getElementById('matrixScrollArea');
        if (scrollArea) scrollArea.style.maxHeight = showAll ? '85vh' : '600px';

        const badge = document.getElementById('matrixFilterBadge');
        if (badge) {
            badge.textContent = badgeText ? `Filtered: ${badgeText}` : '';
            badge.classList.toggle('hidden', !badgeText);
        }

        const alpineRoot = document.querySelector('#matrixScrollArea')?.closest('[x-data]');
        if (alpineRoot) {
            alpineRoot.dispatchEvent(new CustomEvent('matrix-filter-update', {
                bubbles: false,
                detail: { tableData: data, filterActive, showAll }
            }));
        }

        if (filterActive && scrollArea) {
            setTimeout(() => scrollArea.scrollTo({ top: 0, behavior: 'smooth' }), 120);
        }
    }
})();

// ─────────────────────────────────────────────────────────────────────────────
// Critical Skill Gaps Per Sector — "Filter by Period" (mirrors the matrix filter above)
(function () {
    const MONTH_SHORT = ['','Jan','Feb','Mar','Apr','May','Jun',
                         'Jul','Aug','Sep','Oct','Nov','Dec'];
    const MONTH_FULL  = ['','January','February','March','April','May','June',
                         'July','August','September','October','November','December'];

    // Reuses the same date options as the matrix filter — same underlying data source
    // (LmiSubmission), so a separate endpoint isn't needed just for this.
    let sgpDateOptions = window._jobMarketData.matrixDateOptions;

    let pendingYears  = [];
    let pendingMonths = [];
    let pendingMode   = 'range';

    // Defaults to the latest year with real submission data — same as the matrix table.
    let appliedYears  = window._jobMarketData.matrixSelectedYear ? [String(window._jobMarketData.matrixSelectedYear)] : [];
    let appliedMonths = [];
    let appliedMode   = 'range';

    /* ════════════════════════════════════════
       BOOT
    ════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', async function () {
        try {
            const res  = await fetch('/api/job-market/matrix-date-options');
            const json = await res.json();
            if (json.options && json.options.length) sgpDateOptions = json.options;
        } catch (e) {
            console.warn('Sector skill gaps date options fetch failed, using server fallback.');
        }

        buildYearChips();
        updateTriggerLabel();

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('sectorFilterWrapper');
            if (wrapper && !wrapper.contains(e.target)) sgpClose();
        });
    });

    /* ════════════════════════════════════════
       BUILD YEAR CHIPS
    ════════════════════════════════════════ */
    function buildYearChips() {
        const container = document.getElementById('sgpYearChips');
        const yearHint  = document.getElementById('sgpYearHint');
        if (!container) return;
        container.innerHTML = '';

        if (yearHint) {
            yearHint.textContent = pendingMode === 'range'
                ? 'select From & To'
                : 'pick any years';
        }

        const years = [...new Set(sgpDateOptions.map(o => String(o.year)))]
            .sort((a, b) => Number(b) - Number(a));

        if (!years.length) {
            container.innerHTML = '<span class="mfp-chip mfp-disabled mfp-placeholder">No data available</span>';
            return;
        }

        years.forEach(yr => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'mfp-chip' + (pendingYears.includes(yr) ? ' mfp-selected' : '');
            btn.textContent = yr;
            btn.dataset.val = yr;
            btn.onclick     = () => toggleYear(btn, yr);
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════════════════
       BUILD MONTH CHIPS
    ════════════════════════════════════════ */
    function buildMonthChips() {
        const container = document.getElementById('sgpMonthChips');
        const hint      = document.getElementById('sgpMonthHint');
        if (!container) return;
        container.innerHTML = '';

        if (!pendingYears.length) {
            if (hint) hint.textContent = 'select a year first';
            container.innerHTML = '<span class="mfp-chip mfp-placeholder">Select a year to see months</span>';
            return;
        }

        let yearsForMonths;
        if (pendingMode === 'range' && pendingYears.length === 2) {
            const minY = Math.min(...pendingYears.map(Number));
            const maxY = Math.max(...pendingYears.map(Number));
            yearsForMonths = [];
            for (let y = minY; y <= maxY; y++) yearsForMonths.push(String(y));
        } else {
            yearsForMonths = pendingYears.slice();
        }

        const available = new Set(
            sgpDateOptions
                .filter(o => yearsForMonths.includes(String(o.year)))
                .map(o => o.month)
        );

        if (hint) hint.textContent = pendingMode === 'range'
            ? 'select From & To month (optional)'
            : 'pick any specific months';

        for (let m = 1; m <= 12; m++) {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.dataset.val = String(m);

            if (available.has(m)) {
                btn.className   = 'mfp-chip' + (pendingMonths.includes(String(m)) ? ' mfp-selected' : '');
                btn.textContent = MONTH_SHORT[m];
                btn.onclick     = () => toggleMonth(btn, String(m));
            } else {
                btn.className   = 'mfp-chip mfp-disabled';
                btn.textContent = MONTH_SHORT[m];
            }
            container.appendChild(btn);
        }
    }

    /* ════════════════════════════════════════
       TOGGLE HELPERS
    ════════════════════════════════════════ */
    function toggleYear(btn, yr) {
        if (pendingYears.includes(yr)) {
            pendingYears = pendingYears.filter(y => y !== yr);
            btn.classList.remove('mfp-selected');
        } else {
            if (pendingMode === 'range' && pendingYears.length >= 2) {
                const evicted = pendingYears.shift();
                document.querySelector(`#sgpYearChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingYears.push(yr);
            btn.classList.add('mfp-selected');
        }
        pendingMonths = [];
        buildMonthChips();
    }

    function toggleMonth(btn, m) {
        if (pendingMonths.includes(m)) {
            pendingMonths = pendingMonths.filter(x => x !== m);
            btn.classList.remove('mfp-selected');
        } else {
            if (pendingMode === 'range' && pendingMonths.length >= 2) {
                const evicted = pendingMonths.shift();
                document.querySelector(`#sgpMonthChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingMonths.push(m);
            btn.classList.add('mfp-selected');
        }
    }

    /* ════════════════════════════════════════
       MODE TOGGLE (Range vs Exact)
    ════════════════════════════════════════ */
    window.sgpSetMode = function (mode) {
        pendingMode = mode;
        document.getElementById('sgpModeRange')?.classList.toggle('mfp-mode-active', mode === 'range');
        document.getElementById('sgpModeExact')?.classList.toggle('mfp-mode-active', mode === 'exact');
        const hint = document.getElementById('sgpModeHint');
        if (hint) {
            hint.innerHTML = mode === 'range'
                ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
        }
        pendingYears  = [];
        pendingMonths = [];
        buildYearChips();
        buildMonthChips();
    };

    window.sgpToggle = function () {
        const panel = document.getElementById('sectorFilterPanel');
        if (panel.classList.contains('mfp-open')) {
            sgpClose();
        } else {
            pendingYears  = appliedYears.slice();
            pendingMonths = appliedMonths.slice();
            pendingMode   = appliedMode;
            document.getElementById('sgpModeRange')?.classList.toggle('mfp-mode-active', pendingMode === 'range');
            document.getElementById('sgpModeExact')?.classList.toggle('mfp-mode-active', pendingMode === 'exact');
            const modeHint = document.getElementById('sgpModeHint');
            if (modeHint) {
                modeHint.innerHTML = pendingMode === 'range'
                    ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                    : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
            }
            buildYearChips();
            buildMonthChips();
            panel.classList.add('mfp-open');
            document.getElementById('sectorFilterTrigger').classList.add('mft-open');
        }
    };

    function sgpClose() {
        document.getElementById('sectorFilterPanel')?.classList.remove('mfp-open');
        document.getElementById('sectorFilterTrigger')?.classList.remove('mft-open');
    }

    /* ════════════════════════════════════════
       APPLY / RESET
    ════════════════════════════════════════ */
    window.sgpApply = async function () {
        if (!pendingYears.length) {
            sgpReset();
            return;
        }
        appliedYears  = pendingYears.slice();
        appliedMonths = pendingMonths.slice();
        appliedMode   = pendingMode;
        sgpClose();
        updateTriggerLabel();
        await sgpFetch(appliedYears, appliedMonths);
    };

    window.sgpReset = function () {
        pendingYears  = [];
        pendingMonths = [];
        appliedYears  = [];
        appliedMonths = [];
        pendingMode   = 'range';
        appliedMode   = 'range';
        sgpClose();
        updateTriggerLabel();
        sgpFetch([], []); // "All" — full archive, all years
    };

    /* ════════════════════════════════════════
       TRIGGER LABEL
    ════════════════════════════════════════ */
    function updateTriggerLabel() {
        const trigger = document.getElementById('sectorFilterTrigger');
        const text    = document.getElementById('sgpTriggerText');
        if (!text) return;

        if (!appliedYears.length) {
            text.textContent = 'Filter by Period';
            trigger.classList.remove('mft-active');
        } else {
            const yLabel = (appliedMode === 'range' && appliedYears.length === 2)
                ? (() => { const mn = Math.min(...appliedYears.map(Number)), mx = Math.max(...appliedYears.map(Number)); return mn === mx ? String(mn) : `${mn} – ${mx}`; })()
                : appliedYears.join(' & ');
            const mLabel = appliedMonths.length === 0
                ? 'All Months'
                : (appliedMode === 'range' && appliedMonths.length === 2)
                    ? (() => {
                        const mn = Math.min(...appliedMonths.map(Number)), mx = Math.max(...appliedMonths.map(Number));
                        return mn === mx ? MONTH_SHORT[mn] : `${MONTH_SHORT[mn]} – ${MONTH_SHORT[mx]}`;
                    })()
                    : appliedMonths.map(m => MONTH_SHORT[Number(m)]).join(' & ');
            text.textContent = `${yLabel} — ${mLabel}`;
            trigger.classList.add('mft-active');
        }
    }

    /* ════════════════════════════════════════
       CORE FETCH
    ════════════════════════════════════════ */
    async function sgpFetch(years, months) {
        const spinner = document.getElementById('sectorSpinner');
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams();

            let yearsToFetch;
            if (appliedMode === 'range' && years.length === 2) {
                const minY = Math.min(...years.map(Number));
                const maxY = Math.max(...years.map(Number));
                yearsToFetch = [];
                for (let y = minY; y <= maxY; y++) yearsToFetch.push(String(y));
            } else {
                yearsToFetch = years.slice();
            }
            yearsToFetch.forEach(y => params.append('years[]', y));

            let monthsToFetch;
            if (appliedMode === 'range' && months.length === 2) {
                const minM = Math.min(...months.map(Number));
                const maxM = Math.max(...months.map(Number));
                monthsToFetch = [];
                for (let m = minM; m <= maxM; m++) monthsToFetch.push(String(m));
            } else {
                monthsToFetch = months.slice();
            }
            monthsToFetch.forEach(m => params.append('months[]', m));

            const res  = await fetch(`/api/job-market/sector-skills-data?${params}`);
            const json = await res.json();

            renderSkillTags('tech-skills-container', json.tech_skills || [], 'tech-skill', 'bg-blue-100', 'bg-blue-200');
            renderSkillTags('soft-skills-container', json.soft_skills || [], 'soft-skill', 'bg-red-100', 'bg-red-200');

            // Re-apply whichever sector tab is currently active so the new tags respect it
            const activeTab = document.querySelector('.sector-tab.bg-gray-900')?.dataset.sector || 'All';
            if (typeof window.filterSkills === 'function') window.filterSkills(activeTab);

        } catch (e) {
            console.error('Sector skill gaps filter fetch failed:', e);
        }

        if (spinner) spinner.style.display = 'none';
    }

    /* ════════════════════════════════════════
       RENDER SKILL TAGS
       Rebuilds the tag containers from JSON. Uses textContent (not innerHTML with
       string interpolation) for skill/sector names since they're user-submitted data.
    ════════════════════════════════════════ */
    function renderSkillTags(containerId, skills, tagClass, bgClass, countBgClass) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';

        skills.forEach(skill => {
            const tag = document.createElement('div');
            tag.className = `skill-tag ${tagClass} ${bgClass} text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5`;
            tag.setAttribute('data-sector', skill.sector);

            const row = document.createElement('div');
            row.className = 'flex items-center gap-1';
            row.append(document.createTextNode(skill.name));

            if (skill.count && skill.count > 1) {
                const badge = document.createElement('span');
                badge.className = `px-1.5 py-0.5 ${countBgClass} rounded-full text-[9px] font-bold`;
                badge.textContent = `${skill.count}×`;
                row.appendChild(badge);
            }

            const sectorLabel = document.createElement('span');
            sectorLabel.className = 'text-[11px] opacity-60 font-normal';
            sectorLabel.textContent = `(${skill.sector})`;

            tag.appendChild(row);
            tag.appendChild(sectorLabel);
            container.appendChild(tag);
        });

        setTimeout(() => {
            if (typeof window._techScrollUpdate === 'function') window._techScrollUpdate();
            if (typeof window._softScrollUpdate === 'function') window._softScrollUpdate();
        }, 50);
    }
})();



// ─── Global Exports ───────────────────────────────────────────────────────────
// Functions called directly from HTML onclick attributes or dynamically generated
// HTML must be on window so Vite's module scope doesn't hide them.
window.toggleRoleDetails = toggleRoleDetails;
window.filterSkills      = filterSkills;
window.switchContactType = switchContactType;
window.expandChart       = expandChart;
window.closeChart        = closeChart;
window.updateChart       = updateChart;