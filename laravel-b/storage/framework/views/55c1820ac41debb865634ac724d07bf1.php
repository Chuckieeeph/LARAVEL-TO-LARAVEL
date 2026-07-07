<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? config('app.name')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="<?php echo e(route('dashboard')); ?>"><?php echo e(config('app.name')); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('students.index')); ?>">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('fee-schedules.index')); ?>">Fee Schedules</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('assessments.index')); ?>">Assessments</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('payments.index')); ?>">Payments</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('transactions.index')); ?>">Transactions</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center text-white">
                <span><?php echo e(auth()->user()->name ?? 'Guest'); ?> (<?php echo e(auth()->user()->role ?? 'n/a'); ?>)</span>
                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="mb-0">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\student-api - M\laravel-b\resources\views/layouts/app.blade.php ENDPATH**/ ?>