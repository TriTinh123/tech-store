<?php $__env->startComponent('mail::message'); ?>
# Password Reset — TechStore

Hello <?php echo e($user->name); ?>,

We received a request to reset the password for your account. Click the button below to reset your password:

<?php $__env->startComponent('mail::button', ['url' => $resetLink, 'color' => 'success']); ?>
Reset Password
<?php echo $__env->renderComponent(); ?>

This link will expire in 60 minutes.

If you did not request a password reset, please ignore this email.

Best regards,<br>
<?php echo e(config('app.name')); ?>


<?php $__env->startComponent('mail::footer'); ?>
© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
<?php echo $__env->renderComponent(); ?>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\emails\password-reset.blade.php ENDPATH**/ ?>