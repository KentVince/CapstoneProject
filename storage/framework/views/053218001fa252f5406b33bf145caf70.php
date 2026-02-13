<div>
    <style>
        .login-page-wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0d3320 0%, #1e5631 25%, #2d7a4a 50%, #0d3320 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 50;
        }

        .login-page-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 20% 80%, rgba(34, 197, 94, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(21, 128, 61, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(74, 222, 128, 0.05) 0%, transparent 40%);
            animation: login-float 20s ease-in-out infinite;
            pointer-events: none;
        }

        .login-page-wrapper::after {
            content: '';
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(to top, rgba(34, 197, 94, 0.08), transparent);
            pointer-events: none;
        }

        @keyframes login-float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(5deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        }

        .login-content-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .login-container {
            display: flex;
            max-width: 900px;
            width: 100%;
            min-height: 420px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: login-slideUp 0.6s ease-out;
            background: white;
        }

        @keyframes login-slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-image {
            flex: 1;
            background: linear-gradient(to bottom, #15d35b 2%, #166534 50%, #003432 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
          
            position: relative;
            overflow: hidden;
            min-width: 280px;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.1) 0%, transparent 70%);
        }

        .login-image img {
            max-width: 160px;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .login-image img:hover {
            transform: scale(1.05);
        }

        .login-image h1 {
            font-size: 1.4rem;
            margin-top: 1rem;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }

        .login-image p {
            margin-top: 0.5rem;
            opacity: 0.9;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .login-form-section {
            flex: 1.2;
            padding: 2rem 2.5rem;
            background: linear-gradient(145deg, #fffbf0 0%, #f0fdf4 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 380px;
        }

        .login-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #22c55e, #15803d, #22c55e);
        }

        .login-form-section h2 {
            margin-bottom: 0.25rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: #14532d;
        }

        .login-form-section .subtitle {
            color: #15803d;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .login-error-message {
            background: linear-gradient(135deg, #ffe5e5 0%, #ffd4d4 100%);
            color: #9f1d1d;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid #dc3545;
            font-size: 0.9rem;
        }

        .login-error-message ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .login-register-link {
            text-align: center;
            margin-top: 1rem;
            color: #166534;
            font-size: 0.85rem;
        }

        .login-footer {
            text-align: center;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            padding: 1rem;
            position: relative;
            z-index: 1;
        }

        /* Filament form styles */
        .login-form-section .fi-fo-field-wrp {
            margin-bottom: 0.75rem;
        }

        .login-form-section .fi-fo-field-wrp-label {
            margin-bottom: 0.35rem;
        }

        .login-form-section .fi-fo-field-wrp-label span {
            color: #166534 !important;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .login-form-section .fi-input-wrp {
            border-radius: 10px !important;
            border: 2px solid rgba(21, 128, 61, 0.2) !important;
            background: rgba(255, 255, 255, 0.9) !important;
            box-shadow: none !important;
            overflow: hidden;
            --tw-ring-color: transparent !important;
        }

        .login-form-section .fi-input-wrp:focus-within {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15) !important;
            background: white !important;
        }

        .login-form-section .fi-input {
            background: transparent !important;
            border: none !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.9rem !important;
            color: #14532d !important;
        }

        .login-form-section .fi-input::placeholder {
            color: #067c31 !important;
        }

        .login-form-section .fi-checkbox-input {
            accent-color: #22c55e !important;
        }

        .login-form-section .fi-btn {
            width: 100%;
            padding: 0.7rem 1.5rem !important;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%) !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-radius: 10px !important;
            cursor: pointer;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4) !important;
            justify-content: center !important;
        }

        .login-form-section .fi-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5) !important;
        }

        .login-form-section .fi-link {
            color: #15803d !important;
            font-weight: 600;
        }

        .login-form-section .fi-link:hover {
            color: #22c55e !important;
        }

        .login-form-section .fi-checkbox label span {
            color: #166534 !important;
        }

        @media screen and (max-width: 900px) {
            .login-container {
                flex-direction: column;
                max-width: 420px;
                min-height: auto;
            }

            .login-image {
                min-width: unset;
                padding: 1.5rem;
                min-height: 160px;
            }

            .login-image h1 {
                font-size: 1.2rem;
            }

            .login-form-section {
                min-width: unset;
                padding: 1.5rem;
            }
        }

        @media screen and (max-width: 480px) {
            .login-content-wrapper {
                padding: 1rem;
            }

            .login-form-section {
                padding: 1.25rem;
            }

            .login-form-section h2 {
                font-size: 1.3rem;
            }
        }
    </style>

    <div class="login-page-wrapper">
        <div class="login-content-wrapper">
            <div class="login-container">
                <div class="login-image">
                    <img src="<?php echo e(asset('images/cafarm_wg.png')); ?>" alt="CAFARM Logo">
                <h1 style="color:#ffffff;">CAFARM</h1>
                    <p style="color:#ffffff;">COFFEE ANALYTICS AND FARM MANAGEMENT SYSTEM FOR PEST AND DISEASE CONTROL IN DAVAO DE ORO</p>
                </div>
                <div class="login-form-section">
                    <h2>Welcome Back</h2>
                    <p class="subtitle">Sign in to your account to continue</p>

                    <!--[if BLOCK]><![endif]--><?php if(session('status')): ?>
                        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid #22c55e; font-size: 0.9rem;">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                        <div class="login-error-message">
                            <ul>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </ul>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if (isset($component)) { $__componentOriginald09a0ea6d62fc9155b01d885c3fdffb3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald09a0ea6d62fc9155b01d885c3fdffb3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.form.index','data' => ['id' => 'form','wire:submit' => 'authenticate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-panels::form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'form','wire:submit' => 'authenticate']); ?>
                        <?php echo e($this->form); ?>


                        <?php if (isset($component)) { $__componentOriginal742ef35d02cb00943edd9ad8ebf61966 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal742ef35d02cb00943edd9ad8ebf61966 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.form.actions','data' => ['actions' => $this->getCachedFormActions(),'fullWidth' => $this->hasFullWidthFormActions()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-panels::form.actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getCachedFormActions()),'full-width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->hasFullWidthFormActions())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal742ef35d02cb00943edd9ad8ebf61966)): ?>
<?php $attributes = $__attributesOriginal742ef35d02cb00943edd9ad8ebf61966; ?>
<?php unset($__attributesOriginal742ef35d02cb00943edd9ad8ebf61966); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal742ef35d02cb00943edd9ad8ebf61966)): ?>
<?php $component = $__componentOriginal742ef35d02cb00943edd9ad8ebf61966; ?>
<?php unset($__componentOriginal742ef35d02cb00943edd9ad8ebf61966); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald09a0ea6d62fc9155b01d885c3fdffb3)): ?>
<?php $attributes = $__attributesOriginald09a0ea6d62fc9155b01d885c3fdffb3; ?>
<?php unset($__attributesOriginald09a0ea6d62fc9155b01d885c3fdffb3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald09a0ea6d62fc9155b01d885c3fdffb3)): ?>
<?php $component = $__componentOriginald09a0ea6d62fc9155b01d885c3fdffb3; ?>
<?php unset($__componentOriginald09a0ea6d62fc9155b01d885c3fdffb3); ?>
<?php endif; ?>

                    <!--[if BLOCK]><![endif]--><?php if(filament()->hasRegistration()): ?>
                        <div class="login-register-link">
                            <?php echo e(__('filament-panels::pages/auth/login.actions.register.before')); ?>

                            <?php echo e($this->registerAction); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        <div class="login-footer">
            &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/pages/auth/login.blade.php ENDPATH**/ ?>