<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Register User</h1>
                <form method="POST" action="<?php echo e(route('register.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="Accounting Administrator">Accounting Administrator</option>
                                <option value="Cashier">Cashier</option>
                                <option value="Accounting Staff">Accounting Staff</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-dark w-100 mt-4">Create Account</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?php echo e(route('login')); ?>">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\student-api - M\laravel-b\resources\views/auth/register.blade.php ENDPATH**/ ?>