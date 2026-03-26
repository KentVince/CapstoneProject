<div
    x-data="{ isOpen: <?php if ((object) ('isOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'->value()); ?>')<?php echo e('isOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'); ?>')<?php endif; ?>.live, showLightbox: false }"
    x-on:modal-opened.window="isOpen = true"
>
    <?php if (isset($component)) { $__componentOriginal0942a211c37469064369f887ae8d1cef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0942a211c37469064369f887ae8d1cef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.modal.index','data' => ['id' => 'pest-disease-approval-modal','xModel' => 'isOpen','width' => '3xl','@close' => 'isOpen = false; $wire.closeModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'pest-disease-approval-modal','x-model' => 'isOpen','width' => '3xl','@close' => 'isOpen = false; $wire.closeModal()']); ?>
         <?php $__env->slot('heading', null, []); ?> 
            <!--[if BLOCK]><![endif]--><?php if($record): ?>
                Detection: <?php echo e($record->pest); ?>

            <?php else: ?>
                Pest/Disease Approval
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php $__env->endSlot(); ?>

        <!--[if BLOCK]><![endif]--><?php if($record): ?>
            <div class="space-y-4 dark:bg-custom-color-darkmode">
                
                <div class="flex flex-col md:flex-row gap-6 pt-2">
                    
                    <div class="flex-shrink-0">
                        <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
                            <img
                                src="<?php echo e(\Illuminate\Support\Facades\Storage::disk('public')->url($record->image_path)); ?>"
                                alt="Detection Image"
                                class="w-48 h-48 rounded-lg shadow-lg object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                @click="showLightbox = true"
                                title="Click to enlarge"
                            />
                        <?php else: ?>
                            <div class="flex items-center justify-center w-48 h-48 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <span class="text-gray-400 text-sm">No image</span>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div class="mt-3 flex justify-center">
                            <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'disapproved' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                ];
                                $statusColor = $statusColors[$record->validation_status] ?? $statusColors['pending'];
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusColor); ?>">
                                <!--[if BLOCK]><![endif]--><?php if($record->validation_status === 'approved'): ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <?php elseif($record->validation_status === 'disapproved'): ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                <?php else: ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php echo e(ucfirst($record->validation_status)); ?>

                            </span>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
                            <p class="text-xs text-gray-400 text-center mt-1">Click image to enlarge</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div class="flex-1 grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Pest/Disease</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($record->pest); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Type</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(ucfirst($record->type ?? 'N/A')); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Severity</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(ucfirst($record->severity)); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Area</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($record->area); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Description</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo e($record->description ?? 'No description'); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Detected</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo e($record->date_detected ? \Carbon\Carbon::parse($record->date_detected)->format('M d, Y') : 'N/A'); ?></p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Coordinates</h4>
                            <p class="mt-1 text-xs text-gray-900 dark:text-white">
                                <?php echo e(number_format($record->latitude, 4)); ?>, <?php echo e(number_format($record->longitude, 4)); ?>

                            </p>
                        </div>
                    </div>
                </div>

                
                <!--[if BLOCK]><![endif]--><?php if($record->validation_status === 'disapproved' && $record->expert_comments): ?>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 rounded-lg">
                        <h4 class="text-xs font-medium text-red-800 dark:text-red-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                            </svg>
                            Expert Comments
                        </h4>
                        <p class="text-sm text-red-700 dark:text-red-200 mt-2"><?php echo e($record->expert_comments); ?></p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
                    <div
                        x-show="showLightbox"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="showLightbox = false"
                        @keydown.escape.window="showLightbox = false"
                        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
                        style="display: none;"
                    >
                        
                        <button
                            @click="showLightbox = false"
                            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors"
                        >
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        
                        <img
                            src="<?php echo e(\Illuminate\Support\Facades\Storage::disk('public')->url($record->image_path)); ?>"
                            alt="Detection Image - Full Size"
                            class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                            @click.stop
                        />

                        
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-lg text-sm">
                            <?php echo e($record->pest); ?> - <?php echo e(ucfirst($record->type ?? 'N/A')); ?>

                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($record->validation_status === 'pending'): ?>
                <div class="mt-6 space-y-4">
                    <?php echo e($this->form); ?>


                    <div class="flex justify-end gap-3 mt-6">
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'gray','type' => 'button','@click' => 'isOpen = false; $wire.closeModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','type' => 'button','@click' => 'isOpen = false; $wire.closeModal()']); ?>
                            Close
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'success','type' => 'button','wire:click' => 'approve']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success','type' => 'button','wire:click' => 'approve']); ?>
                            Approve
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'danger','type' => 'button','wire:click' => 'disapprove']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'danger','type' => 'button','wire:click' => 'disapprove']); ?>
                            Disapprove
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex justify-end gap-3 mt-6">
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'gray','type' => 'button','@click' => 'isOpen = false; $wire.closeModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','type' => 'button','@click' => 'isOpen = false; $wire.closeModal()']); ?>
                        Close
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php else: ?>
            <p class="text-gray-500">No record found.</p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
<?php /**PATH /var/www/html/CapstoneProject/resources/views/livewire/pest-disease-approval-modal.blade.php ENDPATH**/ ?>