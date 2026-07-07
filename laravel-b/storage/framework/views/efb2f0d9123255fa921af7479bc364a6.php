<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?php echo e($title); ?></h1>
    <?php if(! empty($createUrl)): ?>
        <a href="<?php echo e($createUrl); ?>" class="btn btn-primary">Create <?php echo e($title); ?></a>
    <?php endif; ?>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($label); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <?php $__currentLoopData = array_keys($columns); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td><?php echo e($row[$key] ?? '-'); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if(! empty($row['show_url'])): ?>
                                <a href="<?php echo e($row['show_url']); ?>" class="btn btn-sm btn-outline-secondary">View</a>
                            <?php endif; ?>
                            <?php if(! empty($row['edit_url'])): ?>
                                <a href="<?php echo e($row['edit_url']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php endif; ?>
                            <?php if(! empty($row['delete_url'])): ?>
                                <form action="<?php echo e($row['delete_url']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(count($columns) + 1); ?>" class="text-center text-muted py-4">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\student-api - M\laravel-b\resources\views/shared/index.blade.php ENDPATH**/ ?>