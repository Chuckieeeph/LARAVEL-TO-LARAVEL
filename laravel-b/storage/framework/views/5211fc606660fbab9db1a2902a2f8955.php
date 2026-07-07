<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Login to Accounting Management</h1>
                <form method="POST" action="<?php echo e(route('login.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-dark w-100">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?php echo e(route('register.form')); ?>">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\student-api - M\laravel-b\resources\views/auth/login.blade.php ENDPATH**/ ?>