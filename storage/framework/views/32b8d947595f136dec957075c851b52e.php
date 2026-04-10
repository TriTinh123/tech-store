

<?php $__env->startSection('title', 'Edit Product'); ?>

<?php $__env->startSection('body-content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="margin: 0; color: #1a202c; font-weight: 700;">Edit Product</h2>
    <a href="<?php echo e(route('admin.products')); ?>" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 1.5rem;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('admin.products.update', $product->id)); ?>" method="PUT" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Product Name *</label>
                    <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $product->name)); ?>" required placeholder="Enter product name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Slug *</label>
                    <input type="text" name="slug" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('slug', $product->slug)); ?>" required placeholder="product-slug" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
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
unset($__errorArgs, $__bag); ?>" rows="4" placeholder="Enter product description" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;"><?php echo e(old('description', $product->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Category</label>
                    <select name="category_id" class="form-control <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                        <option value="">-- Select Category --</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Manufacturer</label>
                    <input type="text" name="manufacturer" class="form-control" value="<?php echo e(old('manufacturer', $product->manufacturer)); ?>" placeholder="Manufacturer name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Price *</label>
                    <input type="number" name="price" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('price', $product->price)); ?>" required min="0" step="0.01" placeholder="0.00" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Original Price</label>
                    <input type="number" name="original_price" class="form-control" value="<?php echo e(old('original_price', $product->original_price)); ?>" min="0" step="0.01" placeholder="0.00" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Stock *</label>
                    <input type="number" name="stock" class="form-control <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('stock', $product->stock)); ?>" required min="0" placeholder="0" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Product Image</label>
                <?php if($product->image): ?>
                    <div style="margin-bottom: 1rem;">
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" style="max-width: 200px; border-radius: 4px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image_file" class="form-control <?php $__errorArgs = ['image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                <?php $__errorArgs = ['image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" class="form-check-input" value="1" <?php echo e(old('is_featured', $product->is_featured) ? 'checked' : ''); ?>>
                    <label class="form-check-label" style="color: #2d3748;">Featured Product</label>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="<?php echo e(route('admin.products')); ?>" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\admin\products\edit.blade.php ENDPATH**/ ?>