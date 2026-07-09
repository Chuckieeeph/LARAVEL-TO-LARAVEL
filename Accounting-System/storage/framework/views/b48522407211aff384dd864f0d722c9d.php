<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?php echo e($title); ?></h1>
    <a href="<?php echo e($backUrl); ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?php echo e($action); ?>">
            <?php echo csrf_field(); ?>
            <?php if($method !== 'POST'): ?>
                <?php echo method_field($method); ?>
            <?php endif; ?>

            <div class="row g-3">
                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-<?php echo e(($field['type'] ?? 'text') === 'textarea' ? 12 : 6); ?>">
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <?php if(($field['type'] ?? 'text') === 'textarea'): ?>
                            <textarea name="<?php echo e($field['name']); ?>" class="form-control" rows="4"><?php echo e(old($field['name'], $field['value'] ?? '')); ?></textarea>
                        <?php elseif(($field['type'] ?? 'text') === 'select'): ?>
                            <select name="<?php echo e($field['name']); ?>" class="form-select">
                                <option value="">Select...</option>
                                <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php if(old($field['name'], $field['value'] ?? '') == $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif(($field['type'] ?? 'text') === 'multiselect'): ?>
                            <select name="<?php echo e($field['name']); ?>[]" class="form-select" multiple size="8">
                                <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php if(collect(old($field['name'], $field['value'] ?? []))->contains($value)): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php else: ?>
                            <input
                                type="<?php echo e($field['type'] ?? 'text'); ?>"
                                name="<?php echo e($field['name']); ?>"
                                value="<?php echo e(old($field['name'], $field['value'] ?? '')); ?>"
                                class="form-control"
                                <?php if(! empty($field['step'])): ?> step="<?php echo e($field['step']); ?>" <?php endif; ?>
                                <?php if(! empty($field['readonly'])): ?> readonly <?php endif; ?>
                            >
                        <?php endif; ?>
                        <?php if(! empty($field['help'])): ?>
                            <div class="form-text"><?php echo e($field['help']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <button class="btn btn-primary mt-4"><?php echo e($title); ?></button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Accounting System\resources\views/shared/form.blade.php ENDPATH**/ ?>