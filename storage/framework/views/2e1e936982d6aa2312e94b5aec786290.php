<?php ($farmer = $getState()); ?>

<!--[if BLOCK]><![endif]--><?php if($farmer && $farmer->qr_code): ?>
    <div class="flex items-center justify-center">
        
        <img 
            src="<?php echo e(asset('storage/' . $farmer->qr_code)); ?>" 
            alt="QR Code" 
            class="w-10 h-10 rounded border border-gray-300 object-contain cursor-pointer hover:scale-105 transition-transform"
            x-data
            x-on:click.stop="$dispatch('open-modal', { id: 'qr-preview-<?php echo e($farmer->id); ?>' })"
            title="Click to preview QR"
        >

        
        <?php if (isset($component)) { $__componentOriginal0942a211c37469064369f887ae8d1cef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0942a211c37469064369f887ae8d1cef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.modal.index','data' => ['id' => 'qr-preview-'.e($farmer->id).'','width' => 'lg','alignment' => 'center','class' => 'backdrop-blur-sm bg-black/30']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'qr-preview-'.e($farmer->id).'','width' => 'lg','alignment' => 'center','class' => 'backdrop-blur-sm bg-black/30']); ?>
             <?php $__env->slot('heading', null, []); ?> 
                <div class="flex justify-center items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('heroicon-o-qr-code'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-green-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    <span class="font-semibold text-gray-800">QR Code Preview</span>
                </div>
             <?php $__env->endSlot(); ?>

            <div class="flex flex-col items-center gap-6 p-6">
                <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-200">
                    <img 
                        src="<?php echo e(asset('storage/' . $farmer->qr_code)); ?>" 
                        alt="QR Code" 
                        class="w-72 h-72 object-contain rounded-lg drop-shadow-md"
                    >
                </div>

                <p class="text-sm text-amber-500">
                    Application No: <strong class="text-amber-500"><?php echo e($farmer->app_no); ?></strong>
                </p>

                <a 
                    href="<?php echo e(asset('storage/' . $farmer->qr_code)); ?>" 
                    download="QR-<?php echo e($farmer->app_no); ?>.png"
                    class="inline-flex items-center px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
                >
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('heroicon-o-arrow-down-tray'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    Download QR
                </a>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0942a211c37469064369f887ae8d1cef)): ?>
<?php $attributes = $__attributesOriginal0942a211c37469064369f887ae8d1cef; ?>
<?php unset($__attributesOriginal0942a211c37469064369f887ae8d1cef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0942a211c37469064369f887ae8d1cef)): ?>
<?php $component = $__componentOriginal0942a211c37469064369f887ae8d1cef; ?>
<?php unset($__componentOriginal0942a211c37469064369f887ae8d1cef); ?>
<?php endif; ?>
    </div>
<?php else: ?>
    <span class="text-gray-400 text-xs italic">No QR</span>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH /var/www/html/CapstoneProject/resources/views/components/qr-inline.blade.php ENDPATH**/ ?>