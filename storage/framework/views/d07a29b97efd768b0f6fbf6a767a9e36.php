

<?php $__env->startSection('title', 'Create Category'); ?>

<?php $__env->startSection('body-content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="margin: 0; color: #1a202c; font-weight: 700;">Create Category</h2>
    <a href="<?php echo e(route('admin.categories')); ?>" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Category Name *</label>
                <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>" required placeholder="Enter category name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4" placeholder="Enter category description" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;"><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-save"></i> Create Category
                </button>
                <a href="<?php echo e(route('admin.categories')); ?>" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\admin\categories\create.blade.php ENDPATH**/ ?>