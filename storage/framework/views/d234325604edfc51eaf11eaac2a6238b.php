<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            // Open the view modal for this record
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').mountTableAction('view', '<?php echo e($recordId); ?>');

            // Explicitly mark all related notifications as read on the same Livewire request
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').markRelatedNotificationsRead(<?php echo e((int) $recordId); ?>);

            // Clean the URL so it doesn't re-trigger on refresh
            const url = new URL(window.location);
            url.searchParams.delete('viewRecord');
            url.searchParams.delete('scrollTo');
            window.history.replaceState({}, '', url);

            <!--[if BLOCK]><![endif]--><?php if(($scrollTo ?? null) === 'conversation'): ?>
            // Scroll to conversation thread once the modal has rendered
            setTimeout(function () {
                var el = document.getElementById('conversation-thread');
                if (el) {
                    var scrollParent = el.closest('[style*="overflow"]') || el.closest('.fi-modal-content') || el;
                    if (scrollParent && scrollParent !== el) {
                        scrollParent.scrollTop = el.offsetTop - 16;
                    } else {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    el.style.transition = 'box-shadow 0.3s';
                    el.style.boxShadow = '0 0 0 3px #f59e0b, 0 4px 16px rgba(0,0,0,0.13)';
                    setTimeout(function () { el.style.boxShadow = '0 4px 16px rgba(0,0,0,0.13)'; }, 2000);
                }
            }, 800);
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        }, 500);
    });
</script>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/resources/soil-analysis/auto-open-modal.blade.php ENDPATH**/ ?>