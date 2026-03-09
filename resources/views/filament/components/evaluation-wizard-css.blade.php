<style>
    /* CSS for fixing Filament Select dropdown rendering in RTL */
    .fi-select-input {
        padding-inline-start: 1rem !important;
        padding-inline-end: 2.5rem !important;
        /* Space for the dropdown arrow */
    }

    div[x-data="selectFormComponent()"]>div>select+div>div[role="listbox"] {
        scrollbar-gutter: auto !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    /* Ensure long text doesn't overflow behind scrollbars or arrows and aligns correctly in RTL */
    .fi-select-input-option {
        padding-inline-start: 1rem !important;
        padding-inline-end: 2.25rem !important;
        /* Space for the checkmark icon */
    }

    .evaluation-wizard-progress {
        text-align: center;
        font-size: 1.125rem;
        font-weight: bold;
        color: var(--fi-color-primary-600);
        margin-bottom: 2rem;
    }

    /* Context badge — teal pill breadcrumb */
    .evaluation-context-badge {
        display: inline-block;
        background: rgba(8, 145, 178, 0.08);
        color: #0e7490;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        border: 1px solid rgba(8, 145, 178, 0.15);
        margin-bottom: 1rem;
    }

    /* Question text — bolder & larger */
    .fi-sc-wizard-step .fi-fo-toggle-buttons label.fi-fo-field-wrp-label {
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        line-height: 1.8 !important;
    }
</style>