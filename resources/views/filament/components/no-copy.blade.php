<style>
    /* Disable text selection and copying on evaluation pages */
    .fi-page {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

<script>
    // Disable right-click context menu
    document.addEventListener('select', function(e) {
        e.preventDefault();
    });

    // Disable copy keyboard shortcut
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
            e.preventDefault();
        }
    });
</script>
