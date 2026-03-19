

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

                    
                    <h5 class="mb-3" style="color: #667eea;"><i class="fas fa-shield-alt"></i> Security Settings (3FA)</h5>

                    
                    <div class="card mb-3" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <strong><i class="fas fa-question-circle text-primary"></i> Security Question</strong>
                                    <?php if($user->security_question): ?>
                                        <div class="mt-1 text-muted" style="font-size:13px;">
                                            Current: <em><?php echo e($user->security_question); ?></em>
                                        </div>
                                        <span class="badge bg-success mt-1">Set</span>
                                    <?php else: ?>
                                        <div class="mt-1 text-muted" style="font-size:13px;">No security question set yet.</div>
                                        <span class="badge bg-warning text-dark mt-1">Not set</span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                        onclick="var f=document.getElementById('secQForm');f.style.display=f.style.display==='none'?'block':'none'">
                                    <i class="fas fa-edit"></i> Update
                                </button>
                            </div>

                            <div id="secQForm" style="display:<?php echo e(($user->security_question && !$errors->has('security_question') && !$errors->has('security_answer')) ? 'none' : 'block'); ?>">
                                <form action="<?php echo e(route('profile.update-security-question')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Question</label>
                                        <select name="security_question" class="form-select form-select-sm <?php $__errorArgs = ['security_question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">— Select a question —</option>
                                            <?php
                                            $questions = [
                                                'Family' => [
                                                    'What was the name of your first pet?',
                                                    "What was your childhood best friend's name?",
                                                    "What is your mother's middle name?",
                                                    'What is the name of your oldest sibling?',
                                                ],
                                                'Childhood & School' => [
                                                    'What was the name of your elementary school?',
                                                    'What was the name of your favorite teacher?',
                                                    'What was your childhood nickname?',
                                                    'What was your favorite subject in school?',
                                                ],
                                                'Location' => [
                                                    'What city were you born in?',
                                                    'What street did you grow up on?',
                                                    'What city would you most like to live in?',
                                                ],
                                                'Interests' => [
                                                    'What is your favorite movie?',
                                                    'What is your favorite food?',
                                                    'Who is your favorite athlete/sports player?',
                                                    'Who is your favorite singer/band?',
                                                ],
                                            ];
                                            $current = old('security_question', $user->security_question);
                                            ?>
                                            <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $opts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <optgroup label="<?php echo e($group); ?>">
                                                    <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($q); ?>" <?php if($current === $q): echo 'selected'; endif; ?>><?php echo e($q); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['security_question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Answer</label>
                                        <input type="text" name="security_answer" class="form-control form-control-sm <?php $__errorArgs = ['security_answer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="Your answer (case-insensitive)" autocomplete="off" required>
                                        <?php $__errorArgs = ['security_answer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-save"></i> Save Question
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card mb-3" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <?php if($user->face_photo): ?>
                                        <img src="<?php echo e($user->face_photo); ?>" alt="Your face"
                                             style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:3px solid #22c55e;flex-shrink:0">
                                    <?php else: ?>
                                        <div style="width:56px;height:56px;border-radius:50%;background:#f1f5f9;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <i class="fas fa-camera" style="color:#94a3b8;font-size:20px"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><i class="fas fa-camera text-primary"></i> Face Profile (Biometric)</strong>
                                        <?php if($user->face_descriptor): ?>
                                            <div class="mt-1 text-muted" style="font-size:13px;">Face data enrolled and ready for biometric login.</div>
                                            <span class="badge bg-success mt-1">Enrolled</span>
                                        <?php else: ?>
                                            <div class="mt-1 text-muted" style="font-size:13px;">No face data enrolled. Biometric 3FA will fall back to email confirmation.</div>
                                            <span class="badge bg-warning text-dark mt-1">Not enrolled</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('auth.face.enroll.form')); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-<?php echo e($user->face_descriptor ? 'redo' : 'plus'); ?>"></i>
                                    <?php echo e($user->face_descriptor ? 'Re-enroll' : 'Enroll Face'); ?>

                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit"></i> Edit Info
                        </a>
                        <a href="<?php echo e(route('profile.change-password')); ?>" class="btn btn-warning btn-lg">
                            <i class="fas fa-key"></i> Change Password
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/profile/show.blade.php ENDPATH**/ ?>