<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            @this.mountTableAction('view', '{{ $recordId }}');
            // Clean the URL so it doesn't re-trigger on refresh
            const url = new URL(window.location);
            url.searchParams.delete('viewRecord');
            window.history.replaceState({}, '', url);
        }, 500);
    });
</script>
