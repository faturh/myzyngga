<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div>
    <!-- Session Status -->
    <?php if (isset($component)) { $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-session-status','data' => ['class' => 'mb-4','status' => session('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-4','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $attributes = $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $component = $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>

    <form wire:submit="login">
        <!-- Email Address -->
        <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['label' => 'Email','wire:model' => 'form.email','id' => 'email','type' => 'email','name' => 'email','required' => true,'autofocus' => true,'autocomplete' => 'username','error' => $errors->first('form.email')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Email','wire:model' => 'form.email','id' => 'email','type' => 'email','name' => 'email','required' => true,'autofocus' => true,'autocomplete' => 'username','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('form.email'))]); ?>
             <?php $__env->slot('iconLeft', null, []); ?> 
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $attributes = $__attributesOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__attributesOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $component = $__componentOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__componentOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>

        <!-- Password -->
        <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['label' => 'Password','wire:model' => 'form.password','id' => 'password','class' => 'mt-4','type' => 'password','name' => 'password','required' => true,'autocomplete' => 'current-password','error' => $errors->first('form.password')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Password','wire:model' => 'form.password','id' => 'password','class' => 'mt-4','type' => 'password','name' => 'password','required' => true,'autocomplete' => 'current-password','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('form.password'))]); ?>
             <?php $__env->slot('iconLeft', null, []); ?> 
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $attributes = $__attributesOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__attributesOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $component = $__componentOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__componentOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600"><?php echo e(__('Remember me')); ?></span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Route::has('password.request')): ?>
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="<?php echo e(route('password.request')); ?>" wire:navigate>
                    <?php echo e(__('Forgot your password?')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'submit','variant' => 'primary','size' => 'm','label' => 'Log in','class' => 'ms-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','size' => 'm','label' => 'Log in','class' => 'ms-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $attributes = $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $component = $__componentOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>
        </div>
    </form>
</div><?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views\livewire/pages/auth/login.blade.php ENDPATH**/ ?>