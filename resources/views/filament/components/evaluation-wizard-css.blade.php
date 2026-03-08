<style>
/* CSS for fixing Filament Select dropdown rendering in RTL */
.fi-select-input {
    padding-inline-start: 1rem !important;
    padding-inline-end: 2.5rem !important; /* Space for the dropdown arrow */
}

div[x-data="selectFormComponent()"] > div > select + div > div[role="listbox"] {
    scrollbar-gutter: auto !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

/* Ensure long text doesn't overflow behind scrollbars or arrows and aligns correctly in RTL */
.fi-select-input-option {
    padding-inline-start: 1rem !important;
    padding-inline-end: 2.25rem !important; /* Space for the checkmark icon */
}

.evaluation-wizard-progress {
    text-align: center;
    font-size: 1.125rem;
    font-weight: bold;
    color: var(--fi-color-primary-600);
    margin-bottom: 2rem;
}
</style>
