<style>
    :root {
        --accent-50: oklch(96.96% 0.013 234.34);
        --accent-100: oklch(93.97% 0.024 231.13);
        --accent-200: oklch(88.36% 0.046 226.75);
        --accent-300: oklch(79.35% 0.082 225.13);
        --accent-400: oklch(69.83% 0.124 225.29);
        --accent-500: oklch(61.27% 0.145 226.23);
        --accent-600: oklch(51.34% 0.124 227.87);
        --accent-700: oklch(43.14% 0.103 229.41);
        --accent-800: oklch(37.52% 0.081 230.13);
        --accent-900: oklch(33.56% 0.063 231.54);
        --accent-950: oklch(24.58% 0.048 232.66);
    }

    .fi-body,
    .fi-main {
        background: #eeeef8 !important;
    }

    /* Buttons */
    /* .fi-btn-primary {
        background-color: var(--primary) !important;
    } */

    .fi-main-sidebar {
        background-color: #fff;
    }

    .fi-main-sidebar .fi-sidebar-nav {
        padding: calc(var(--spacing)*6) 15px;
    }



    .fi-btn.fi-color-primary span,
    .fi-btn.fi-color-primary svg,
    .fi-color-primary.fi-ac-btn-action,
    .fi-btn.fi-color-success span,
    .fi-btn.fi-color-success svg,
    .fi-color-success.fi-ac-btn-action {
        color: white !important;
    }


    .fi-btn.fi-color-accent span,
    .fi-btn.fi-color-accent svg,
    .fi-color-accent.fi-ac-btn-action,
    .fi-btn.fi-color-success span,
    .fi-btn.fi-color-success svg,
    .fi-color-success.fi-ac-btn-action {
        color: white !important;
    }

    .fi-color-youtube.fi-ac-btn-action {
        color: #FF0033;
        border:1px solid
    }



    /* .fi-color.fi-color-primary>.fi-icon {
        color: white !important;
    } */

    .fi-btn-primary:hover {
        background-color: var(--secondary) !important;
    }

    /* Success/Info/Warning badges - map to primary */
    /* .fi-badge-success,
    .fi-badge-info,
    .fi-badge-warning {
        background-color: var(--primary-light) !important;
        color: var(--primary) !important;
    } */

    /* Gray badges - map to secondary */
    .fi-badge-gray {
        background-color: var(--secondary-lighter) !important;
        color: var(--secondary) !important;
    }

    .fi-fo-field:has(.publish-toggle) {
        height: 100%;
        align-items: center;
        margin-top: 13px;
    }

    .lesson-status-toggle {
        display: inline-flex;
        width: fit-content;
    }

    .lesson-status-toggle.is-ready {
        cursor: pointer;
    }

    .lesson-status-toggle.is-disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    .lessons-wrapper,
    .quizzes-wrapper {
        background: #eeeef8;
        padding: 10px;
    }

    .quiz-options-wrapper {
        border: 1px dashed #ccc;
        padding: 10px;
        border-radius: 7px;
    }

    .custom-widget-spacing {
        display: flex !important;
        flex-direction: column !important;
        gap: 1rem !important;
        /* This is the equivalent of space-y-4 */
    }

    .fi-page-content:has(.form-card) {
        background: #fff;
        padding: 20px;
        border-radius: 7px;
    }

    html div:has(>.tutor-auth-layout) {
        background: #eeeefe !important
    }
</style>
