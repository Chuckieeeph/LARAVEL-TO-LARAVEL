<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Students</div><div class="fs-2 fw-bold"><?php echo e($studentCount); ?></div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Assessments</div><div class="fs-2 fw-bold"><?php echo e($assessmentCount); ?></div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Payments</div><div class="fs-2 fw-bold"><?php echo e($paymentCount); ?></div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Fee Schedules</div><div class="fs-2 fw-bold"><?php echo e($feeScheduleCount); ?></div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Enrollment Logs</div><div class="fs-2 fw-bold"><?php echo e($enrollmentLogCount); ?></div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">Recent Assessments</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($assessment->enrollment_reference_number); ?></td>
                        <td><?php echo e(trim($assessment->student?->first_name.' '.$assessment->student?->last_name)); ?></td>
                        <td><?php echo e($assessment->course_name); ?></td>
                        <td><?php echo e(number_format((float) $assessment->total_amount, 2)); ?></td>
                        <td><span class="badge text-bg-success"><?php echo e($assessment->status); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No assessments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Enrollment Logs</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentEnrollmentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($log->enrollment_reference_number); ?></td>
                        <td><?php echo e($log->student_name); ?></td>
                        <td><?php echo e($log->course_name); ?></td>
                        <td><span class="badge text-bg-secondary"><?php echo e($log->processing_status); ?></span></td>
                        <td><?php echo e(optional($log->received_at)->format('M d, Y h:i A')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No enrollment logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Accounting System\resources\views/dashboard.blade.php ENDPATH**/ ?>