

<?php $__env->startSection('content'); ?>
<style>
*{box-sizing:border-box}
body{background:#f8fafc}
.sec-wrap{max-width:680px;margin:48px auto;padding:0 16px}

/* Header */
.sec-header{margin-bottom:32px}
.sec-header a{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:16px}
.sec-header a:hover{color:#334155}
.sec-header h1{font-size:22px;font-weight:700;color:#0f172a;margin:0 0 4px}
.sec-header p{font-size:14px;color:#64748b;margin:0}

/* Section block */
.sec-block{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:20px;overflow:hidden}
.sec-block-title{padding:16px 20px 12px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
.sec-block-title .icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sec-block-title h2{font-size:14px;font-weight:700;color:#0f172a;margin:0}
.sec-block-title p{font-size:12px;color:#94a3b8;margin:0;margin-top:1px}

/* Row inside block */
.sec-row{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid #f8fafc}
.sec-row:last-child{border-bottom:none}
.sec-row-left{display:flex;align-items:center;gap:14px;min-width:0}
.sec-row-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sec-row-info{min-width:0}
.sec-row-info .title{font-size:14px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sec-row-info .desc{font-size:12px;color:#64748b;margin-top:2px;line-height:1.4}
.sec-row-right{display:flex;align-items:center;gap:10px;flex-shrink:0}
.pill{font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap}
.pill-green{color:#15803d;background:#dcfce7}
.pill-yellow{color:#a16207;background:#fef9c3}
.row-action{font-size:13px;font-weight:500;color:#6366f1;background:none;border:none;padding:0;cursor:pointer;text-decoration:none;white-space:nowrap}
.row-action:hover{color:#4f46e5}

/* Expand form */
.expand-form{display:none;padding:16px 20px 20px;border-top:1px solid #f1f5f9;background:#fafafa}
.expand-form label{font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:6px}
.expand-form .form-control,.expand-form .form-select{font-size:13px;border-color:#e2e8f0;border-radius:8px;box-shadow:none}
.expand-form .form-control:focus,.expand-form .form-select:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.expand-form .btn-save{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:600;cursor:pointer}
.expand-form .btn-save:hover{opacity:.9}
.expand-form .btn-cancel{background:#f1f5f9;color:#64748b;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:500;cursor:pointer}

/* Alert */
.flash{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.flash-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
.flash-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
</style>

<div class="sec-wrap">

    
    <div class="sec-header">
        <a href="<?php echo e(route('profile.show')); ?>">
            <i class="fas fa-arrow-left" style="font-size:11px"></i> Account
        </a>
        <h1>Security</h1>
        <p>Manage how you verify your identity when signing in</p>
    </div>

    
    <?php if(session('success')): ?>
        <div class="flash flash-ok"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="flash flash-err"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    
    <div class="sec-block">
        <div class="sec-block-title">
            <div class="icon" style="background:#eff6ff">
                <i class="fas fa-shield-alt" style="color:#3b82f6;font-size:13px"></i>
            </div>
            <div>
                <h2>Two-step verification</h2>
                <p>Additional checks triggered when we detect unusual sign-in activity</p>
            </div>
        </div>

        
        <div class="sec-row">
            <div class="sec-row-left">
                <div class="sec-row-icon" style="background:#eff6ff">
                    <i class="fas fa-key" style="color:#3b82f6;font-size:14px"></i>
                </div>
                <div class="sec-row-info">
                    <div class="title">Security question</div>
                    <div class="desc">
                        <?php if($user->security_question): ?>
                            <?php echo e($user->security_question); ?>

                        <?php else: ?>
                            Not set up — required for identity verification
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="sec-row-right">
                <?php if($user->security_question): ?>
                    <span class="pill pill-green">Active</span>
                <?php else: ?>
                    <span class="pill pill-yellow">Not set</span>
                <?php endif; ?>
                <button class="row-action" onclick="toggle('sqForm')">
                    <?php echo e($user->security_question ? 'Update' : 'Set up'); ?> →
                </button>
            </div>
        </div>
        <div class="expand-form" id="sqForm"
             style="<?php echo e(($errors->has('security_question') || $errors->has('security_answer')) ? 'display:block' : ''); ?>">
            <form action="<?php echo e(route('profile.update-security-question')); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="mb-3">
                    <label>Question</label>
                    <select name="security_question" class="form-select" required>
                        <option value="">— Choose a question —</option>
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
                </div>
                <div class="mb-3">
                    <label>Your answer</label>
                    <input type="text" name="security_answer" class="form-control"
                           placeholder="Case-insensitive" autocomplete="off" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-save">Save</button>
                    <button type="button" class="btn-cancel" onclick="toggle('sqForm')">Cancel</button>
                </div>
            </form>
        </div>

        
        <div class="sec-row">
            <div class="sec-row-left">
                <div class="sec-row-icon" style="background:#fff7ed">
                    <i class="fas fa-envelope" style="color:#f97316;font-size:14px"></i>
                </div>
                <div class="sec-row-info">
                    <div class="title">Email confirmation link</div>
                    <div class="desc">A sign-in confirmation link sent to <?php echo e($user->email); ?></div>
                </div>
            </div>
            <div class="sec-row-right">
                <span class="pill pill-green">Active</span>
            </div>
        </div>

        
        <div class="sec-row">
            <div class="sec-row-left">
                <?php if($user->face_photo): ?>
                    <img src="<?php echo e($user->face_photo); ?>" alt="Face"
                         style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:2px solid #22c55e;flex-shrink:0">
                <?php else: ?>
                    <div class="sec-row-icon" style="background:#f0fdf4">
                        <i class="fas fa-user-circle" style="color:#22c55e;font-size:18px"></i>
                    </div>
                <?php endif; ?>
                <div class="sec-row-info">
                    <div class="title">Face ID</div>
                    <div class="desc">
                        <?php if($user->face_descriptor): ?>
                            Face data enrolled — ready for biometric verification
                        <?php else: ?>
                            Not enrolled — falls back to email confirmation link
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="sec-row-right">
                <?php if($user->face_descriptor): ?>
                    <span class="pill pill-green">Enrolled</span>
                <?php else: ?>
                    <span class="pill pill-yellow">Not set</span>
                <?php endif; ?>
                <a href="<?php echo e(route('auth.face.enroll.form')); ?>" class="row-action">
                    <?php echo e($user->face_descriptor ? 'Update' : 'Set up'); ?> →
                </a>
            </div>
        </div>

    </div>

    
    <div class="sec-block">
        <div class="sec-block-title">
            <div class="icon" style="background:#faf5ff">
                <i class="fas fa-lock" style="color:#8b5cf6;font-size:13px"></i>
            </div>
            <div>
                <h2>Password</h2>
                <p>Regularly update your password to keep your account safe</p>
            </div>
        </div>
        <div class="sec-row">
            <div class="sec-row-left">
                <div class="sec-row-icon" style="background:#faf5ff">
                    <i class="fas fa-key" style="color:#8b5cf6;font-size:14px"></i>
                </div>
                <div class="sec-row-info">
                    <div class="title">Account password</div>
                    <div class="desc">Last changed: <?php echo e($user->updated_at->format('M d, Y')); ?></div>
                </div>
            </div>
            <div class="sec-row-right">
                <a href="<?php echo e(route('profile.change-password')); ?>" class="row-action">Change →</a>
            </div>
        </div>
    </div>

</div>

<script>
function toggle(id) {
    var el = document.getElementById(id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\profile\security.blade.php ENDPATH**/ ?>