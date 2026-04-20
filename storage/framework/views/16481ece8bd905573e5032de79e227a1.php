<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            <?php echo e(__('Update Password')); ?>

        </h2>

        <p class="mt-1 text-sm text-gray-600">
            <?php echo e(__('Ensure your account is using a long, random password to stay secure.')); ?>

        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['label' => 'Current Password','wire:model' => 'current_password','id' => 'update_password_current_password','name' => 'current_password','type' => 'password','autocomplete' => 'current-password','error' => $errors->first('current_password')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Current Password','wire:model' => 'current_password','id' => 'update_password_current_password','name' => 'current_password','type' => 'password','autocomplete' => 'current-password','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('current_password'))]); ?>
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

        <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['label' => 'New Password','wire:model' => 'password','id' => 'update_password_password','name' => 'password','type' => 'password','autocomplete' => 'new-password','error' => $errors->first('password')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'New Password','wire:model' => 'password','id' => 'update_password_password','name' => 'password','type' => 'password','autocomplete' => 'new-password','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('password'))]); ?>
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

        <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['label' => 'Confirm Password','wire:model' => 'password_confirmation','id' => 'update_password_password_confirmation','name' => 'password_confirmation','type' => 'password','autocomplete' => 'new-password','error' => $errors->first('password_confirmation')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Confirm Password','wire:model' => 'password_confirmation','id' => 'update_password_password_confirmation','name' => 'password_confirmation','type' => 'password','autocomplete' => 'new-password','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('password_confirmation'))]); ?>
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

        <div class="flex items-center gap-4">
            <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e(__('Save')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginala665a74688c74e9ee80d4fedd2b98434 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala665a74688c74e9ee80d4fedd2b98434 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-message','data' => ['class' => 'me-3','on' => 'password-updated']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'me-3','on' => 'password-updated']); ?>
                <?php echo e(__('Saved.')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala665a74688c74e9ee80d4fedd2b98434)): ?>
<?php $attributes = $__attributesOriginala665a74688c74e9ee80d4fedd2b98434; ?>
<?php unset($__attributesOriginala665a74688c74e9ee80d4fedd2b98434); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala665a74688c74e9ee80d4fedd2b98434)): ?>
<?php $component = $__componentOriginala665a74688c74e9ee80d4fedd2b98434; ?>
<?php unset($__componentOriginala665a74688c74e9ee80d4fedd2b98434); ?>
<?php endif; ?>
        </div>
    </form>
</section><?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views\livewire/profile/update-password-form.blade.php ENDPATH**/ ?>