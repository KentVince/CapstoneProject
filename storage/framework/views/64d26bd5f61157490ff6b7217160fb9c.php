

<!--[if BLOCK]><![endif]--><?php if($getRecord() && $getRecord()->qr_code): ?>
    <div class=" flex flex-col items-center space-y-3">
        <img 
            src="<?php echo e(Storage::url($getRecord()->qr_code)); ?>" 
            alt="QR Code" 
            class="rounded-lg shadow-md border p-2 bg-white dark:bg-darkmode w-48 h-48"
        >
        <a 
            href="<?php echo e(Storage::url($getRecord()->qr_code)); ?>" 
            download="<?php echo e($getRecord()->app_no); ?>_QR.png"
            class="px-4 py-2 text-gray-500 bg-green-600 rounded-md hover:bg-green-700"
        >
            ⬇️ Download QR Code
        </a>
    </div>
<?php else: ?>
    <p class="text-gray-500 text-center italic">QR Code will be generated after saving this record.</p>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

 <?php /**PATH /var/www/html/CapstoneProject/resources/views/components/qr-code.blade.php ENDPATH**/ ?>