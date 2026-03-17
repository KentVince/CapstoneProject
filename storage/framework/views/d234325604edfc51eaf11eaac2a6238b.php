<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').mountTableAction('view', '<?php echo e($recordId); ?>');
            // Clean the URL so it doesn't re-trigger on refresh
            const url = new URL(window.location);
            url.searchParams.delete('viewRecord');
            window.history.replaceState({}, '', url);
        }, 500);
    });
</script>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/resources/soil-analysis/auto-open-modal.blade.php ENDPATH**/ ?>