

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="d-flex align-items-center gap-3">
                        <?php if($user->face_photo): ?>
                            <img src="<?php echo e($user->face_photo); ?>" alt="<?php echo e($user->name); ?>"
                                 style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.6);flex-shrink:0">
                        <?php else: ?>
                            <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-user" style="font-size:22px;color:rgba(255,255,255,.7)"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="mb-0">
                            <i class="fas fa-user"></i> <?php echo e($user->name); ?>

                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('face_enroll_prompt')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-camera"></i>
                            <strong>Face profile not set up yet.</strong>
                            To enable biometric (face) verification for 3FA,
                            <a href="<?php echo e(route('auth.face.enroll.form')); ?>" class="alert-link">enroll your face here</a>.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> An error occurred!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="profile-info">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Name:</span>
                                    <span class="value"><?php echo e($user->name); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Email:</span>
                                    <span class="value"><?php echo e($user->email); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Phone:</span>
                                    <span class="value"><?php echo e($user->phone ?? 'Not set'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Role:</span>
                                    <span class="badge bg-info"><?php echo e($user->role == 'admin' ? 'Administrator' : 'Users'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Address:</span>
                                    <span class="value"><?php echo e($user->address ?? 'Not set'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Member since:</span>
                                    <span class="value"><?php echo e($user->created_at->format('d/m/Y H:i')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <hr class="my-4">

                    <div class="d-grid gap-2">
                        
                        <a href="<?php echo e(route('profile.security')); ?>"
                           style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;text-decoration:none;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;transition:background .15s"
                           onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                            <div style="display:flex;align-items:center;gap:12px">
                                <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fas fa-shield-alt" style="color:#fff;font-size:13px"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#1e293b">Security</div>
                                    <div style="font-size:12px;color:#64748b;margin-top:1px">3-step verification, Face ID, password</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:#cbd5e1;font-size:12px"></i>
                        </a>
                        <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit"></i> Edit Info
                        </a>
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-info btn-lg">
                            <i class="fas fa-shopping-bag"></i> Order History
                        </a>
                        <a href="<?php echo e(route('wishlist')); ?>" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .label {
        font-weight: 600;
        min-width: 150px;
    }

    .value {
        color: #495057;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #667eea;
    }
</style>

<script>
function toggleForm(id) {
    var f = document.getElementById(id);
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/profile/show.blade.php ENDPATH**/ ?>