
<?php $__env->startSection('title', 'AI Demo — A/B Scenarios'); ?>

<?php $__env->startSection('body-content'); ?>
<style>
.demo-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
.demo-card{border-radius:14px;border:2px solid;padding:0;overflow:hidden;}
.demo-card-head{padding:18px 22px;display:flex;align-items:center;gap:12px;}
.demo-card-body{padding:18px 22px;background:#fff;}
.demo-card.normal{border-color:#00b894;}
.demo-card.anomaly{border-color:#e84040;}
.demo-card.normal .demo-card-head{background:linear-gradient(135deg,#00b894,#00a381);}
.demo-card.anomaly .demo-card-head{background:linear-gradient(135deg,#e84040,#c0392b);}
.demo-card-head h3{color:#fff;font-size:15px;font-weight:700;margin:0;}
.demo-card-head p{color:rgba(255,255,255,.85);font-size:12px;margin:3px 0 0 0;}
.demo-card-head .badge-risk{padding:5px 14px;border-radius:20px;font-size:13px;font-weight:800;background:rgba(255,255,255,.25);color:#fff;border:1.5px solid rgba(255,255,255,.5);}
.risk-meter{margin:16px 0;}
.risk-bar{height:14px;border-radius:7px;background:#e8edf2;overflow:hidden;margin-top:6px;}
.risk-bar-fill{height:100%;border-radius:7px;transition:width 1s ease;}
.feat-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f4f7fa;font-size:13px;}
.feat-row:last-child{border-bottom:none;}
.feat-label{color:#64748b;}
.feat-val{font-weight:700;color:#1a1f2e;}
.feat-contrib{width:80px;height:8px;border-radius:4px;background:#e8edf2;overflow:hidden;margin-left:8px;}
.feat-contrib-fill{height:100%;border-radius:4px;}
.explain-list li{font-size:12.5px;color:#475569;padding:3px 0;padding-left:18px;position:relative;}
.explain-list li::before{content:"⚠";position:absolute;left:0;font-size:10px;}
.explain-list.ok li::before{content:"✓";color:#00b894;}
.step-badge{display:inline-block;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;}
/* Custom scorer */
.scorer-form{background:#fff;border-radius:14px;border:1px solid #e8edf2;padding:22px;margin-bottom:24px;}
.slider-row{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.slider-row label{width:220px;font-size:13px;color:#64748b;flex-shrink:0;}
.slider-row input[type=range]{flex:1;}
.slider-row .val{width:60px;text-align:right;font-weight:700;font-size:13px;color:#1a1f2e;}
.result-box{border-radius:12px;padding:18px;margin-top:18px;display:none;}
.live-table th{font-size:12px;font-weight:600;color:#64748b;background:#f8fafc;padding:8px 12px;}
.live-table td{font-size:12px;padding:8px 12px;vertical-align:middle;}
@media(max-width:800px){.demo-grid{grid-template-columns:1fr;}}
</style>

<div class="pg-hdr" style="margin-bottom:20px">
  <div>
    <h2>AI Demo — A/B Scenarios</h2>
    <p>Compare legitimate users vs attackers via Isolation Forest</p>
  </div>
  <div style="display:flex;gap:8px;align-items:center">
    <?php if($aiOnline): ?>
      <span style="background:#e6faf5;color:#00b894;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-circle" style="font-size:8px"></i> AI Engine Online</span>
    <?php else: ?>
      <span style="background:#ffeaea;color:#e84040;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-circle" style="font-size:8px"></i> AI Engine Offline</span>
    <?php endif; ?>
    <a href="<?php echo e(route('admin.security')); ?>" data-nav="/admin/security"
       style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:9px;font-size:13px;font-weight:700;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;text-decoration:none;border:none;box-shadow:0 2px 8px rgba(239,68,68,.35);transition:all .2s"
       onmouseenter="this.style.boxShadow='0 4px 16px rgba(239,68,68,.55)';this.style.transform='translateY(-1px)'"
       onmouseleave="this.style.boxShadow='0 2px 8px rgba(239,68,68,.35)';this.style.transform=''">
      <i class="fas fa-shield-alt"></i> Security Log
    </a>
  </div>
</div>

<?php if(!$aiOnline): ?>
<div class="alert alert-warning mb-4" style="border-radius:10px;font-size:13px">
  <i class="fas fa-exclamation-triangle me-2"></i>
  AI Engine offline. Start it with: <code>cd python_ai &amp;&amp; python app.py</code>
  — Static demo shown below.
</div>
<?php endif; ?>


<div class="card mb-4" style="border-radius:14px;overflow:hidden;border:1px solid #e8edf2">
  <div style="background:linear-gradient(135deg,#1a1f2e,#2d3561);padding:18px 24px;color:#fff">
    <h4 style="margin:0;font-size:15px;font-weight:700"><i class="fas fa-sitemap me-2"></i>Adaptive 3FA System Architecture</h4>
    <p style="margin:4px 0 0;font-size:12px;opacity:.8">Design and Implementation of an AI-Driven Anomaly Detection Layer to Reinforce 3FA in E-Commerce</p>
  </div>
  <div style="padding:20px 24px;background:#f8fafc">
    <div style="display:flex;align-items:center;gap:0;flex-wrap:wrap;justify-content:center">
      <?php $__currentLoopData = [
        ['icon'=>'fa-user','label'=>'Users','sub'=>'Enter Username / Password','color'=>'#0984e3'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-envelope','label'=>'Factor 2','sub'=>'OTP qua Email','color'=>'#6c5ce7'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-brain','label'=>'AI Engine','sub'=>'Isolation Forest\nRisk Score','color'=>'#e84040'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-check-double','label'=>'Factor 3','sub'=>'Security Question / Biometric\n(if HIGH/CRITICAL)','color'=>'#f39c12'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-home','label'=>'Sign In','sub'=>'Success','color'=>'#00b894'],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($step['arrow'])): ?>
          <i class="fas fa-long-arrow-alt-right" style="color:#b2bec3;font-size:20px;margin:0 8px"></i>
        <?php else: ?>
          <div style="text-align:center;width:90px">
            <div style="width:48px;height:48px;border-radius:50%;background:<?php echo e($step['color']); ?>22;border:2px solid <?php echo e($step['color']); ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 6px">
              <i class="fas <?php echo e($step['icon']); ?>" style="color:<?php echo e($step['color']); ?>;font-size:16px"></i>
            </div>
            <div style="font-size:12px;font-weight:700;color:#1a1f2e"><?php echo e($step['label']); ?></div>
            <div style="font-size:10.5px;color:#64748b;line-height:1.35;white-space:pre-line"><?php echo e($step['sub']); ?></div>
          </div>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>


<?php if($demoData): ?>
<?php
  $scenA = $demoData['scenarios']['normal'] ?? null;
  $scenB = $demoData['scenarios']['anomaly'] ?? null;
?>
<div class="demo-grid">
  <?php $__currentLoopData = [['normal',$scenA],['anomaly',$scenB]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key,$sc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if($sc): ?>
  <?php
    $rNum = $sc['risk_numeric'] ?? 0;
    $rl   = $sc['risk_level']   ?? 'low';
    $rlColors = ['low'=>'#00b894','medium'=>'#f39c12','high'=>'#e84040','critical'=>'#8e0000'];
    $rlColor  = $rlColors[$rl] ?? '#64748b';
    $is3fa = $sc['requires_3fa'] ?? false;
  ?>
  <div class="demo-card <?php echo e($key); ?>">
    <div class="demo-card-head">
      <div style="flex:1">
        <h3><?php echo e($sc['label']); ?></h3>
        <p><?php echo e($sc['description']); ?></p>
      </div>
      <div class="badge-risk"><?php echo e(strtoupper($rl)); ?></div>
    </div>
    <div class="demo-card-body">

      
      <div class="risk-meter">
        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
          <span style="font-weight:700;color:#1a1f2e">Risk Score</span>
          <span style="font-weight:800;font-size:17px;color:<?php echo e($rlColor); ?>"><?php echo e($rNum); ?><span style="font-size:11px;font-weight:400">/100</span></span>
        </div>
        <div class="risk-bar">
          <div class="risk-bar-fill" style="width:<?php echo e($rNum); ?>%;background:<?php echo e($key==='normal' ? 'linear-gradient(90deg,#00b894,#81ecec)' : 'linear-gradient(90deg,#f39c12,#e84040)'); ?>"></div>
        </div>
      </div>

      
      <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <span style="background:<?php echo e($is3fa?'#ffeaea':'#e6faf5'); ?>;color:<?php echo e($is3fa?'#e84040':'#00b894'); ?>;padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:800">
          <i class="fas <?php echo e($is3fa?'fa-exclamation-triangle':'fa-check-circle'); ?>"></i>
          <?php echo e($is3fa ? '3FA REQUIRED' : '✓ Allow sign-in'); ?>

        </span>
        <?php if($sc['is_anomaly']): ?>
        <span style="background:#fff9ec;color:#f39c12;padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:700"><i class="fas fa-robot"></i> Anomaly Detected</span>
        <?php endif; ?>
      </div>

      
      <div style="margin-bottom:14px">
        <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">Input Signals</div>
        <?php
          $featureLabels = [
            'hour_of_day'=>'Login hour','is_weekend'=>'Weekend','is_new_ip'=>'New IP',
            'is_new_device'=>'New device','failed_attempts'=>'Failed attempts',
            'keystroke_speed_ms'=>'Keystroke speed (ms)','keystroke_irregularity'=>'Keystroke irregularity',
            'transaction_amount'=>'Transaction amount (k₫)', 'click_count_per_min'=>'Click rate/min',
          ];
          $isRiskyVal = fn($k,$v) => match($k) {
            'hour_of_day'      => $v <= 4 || $v >= 23,
            'is_new_ip'        => $v == 1,
            'is_new_device'    => $v == 1,
            'failed_attempts'  => $v >= 3,
            'keystroke_speed_ms' => $v < 30 || $v > 800,
            'transaction_amount' => $v > 5000,
            'click_count_per_min' => $v > 150,
            default            => false,
          };
        ?>
        <?php $__currentLoopData = $sc['session']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($feat !== 'user_id'): ?>
        <?php $risky = $isRiskyVal($feat, $val); ?>
        <div class="feat-row">
          <span class="feat-label"><?php echo e($featureLabels[$feat] ?? $feat); ?></span>
          <div style="display:flex;align-items:center;gap:8px">
            <span class="feat-val" style="<?php echo e($risky ? 'color:#e84040' : ''); ?>">
              <?php echo e(is_bool($val) ? ($val?'Yes':'No') : $val); ?>

              <?php if($risky): ?><i class="fas fa-exclamation-circle" style="color:#e84040;font-size:10px"></i><?php endif; ?>
            </span>
            <?php if(isset($sc['feature_contributions'][$feat])): ?>
            <div class="feat-contrib">
              <div class="feat-contrib-fill" style="width:<?php echo e(min(100,round($sc['feature_contributions'][$feat]*100))); ?>%;background:<?php echo e($risky?'#e84040':'#00b894'); ?>"></div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      
      <?php if(!empty($sc['explanation'])): ?>
      <div>
        <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">
          <?php echo e($key==='normal' ? 'Analysis' : 'Anomaly reason'); ?>

        </div>
        <ul class="explain-list <?php echo e($key==='normal' ? 'ok' : ''); ?>" style="list-style:none;margin:0;padding:0">
          <?php $__currentLoopData = $sc['explanation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($exp); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
      <?php endif; ?>

      <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f4f7fa;font-size:11.5px;color:#94a3b8">
        Latency: <strong><?php echo e($sc['latency_ms'] ?? 0); ?> ms</strong> &nbsp;|&nbsp; IF score: <strong><?php echo e($sc['risk_score'] ?? '—'); ?></strong>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>

<div class="demo-grid">
  <?php $__currentLoopData = [
    ['class'=>'normal','title'=>'Scenario A — Legitimate User','level'=>'LOW','num'=>12,'color'=>'#00b894','bg'=>'linear-gradient(90deg,#00b894,#81ecec)','features'=>['Login time: 10:00 AM','Familiar IP (Vietnam)','Known device','No failed logins','Natural typing (145ms)','Cart value: 800,000 ₫'],'result'=>'Allow sign-in — no 3FA needed'],
    ['class'=>'anomaly','title'=>'Scenario B — Attacker','level'=>'CRITICAL','num'=>92,'color'=>'#e84040','bg'=>'linear-gradient(90deg,#f39c12,#e84040)','features'=>['Login time: 3:00 AM ⚠','Unknown foreign IP ⚠','Brand new device ⚠','4 failed login attempts ⚠','Ultra-fast 12ms typing (Bot?) ⚠','Cart spike: 15,000,000 ₫ ⚠'],'result'=>'3FA REQUIRED — Biometric/security question needed'],
  ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="demo-card <?php echo e($mock['class']); ?>">
    <div class="demo-card-head" style="<?php echo e($mock['class']==='normal' ? 'background:linear-gradient(135deg,#00b894,#00a381)' : 'background:linear-gradient(135deg,#e84040,#c0392b)'); ?>">
      <div style="flex:1"><h3><?php echo e($mock['title']); ?></h3><p style="opacity:.8;font-size:12px">Static demo — start AI for live results</p></div>
      <div class="badge-risk"><?php echo e($mock['level']); ?></div>
    </div>
    <div class="demo-card-body">
      <div class="risk-meter">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px">
          <span style="font-weight:700">Risk Score</span>
          <span style="font-weight:800;font-size:17px;color:<?php echo e($mock['color']); ?>"><?php echo e($mock['num']); ?><span style="font-size:11px;font-weight:400">/100</span></span>
        </div>
        <div class="risk-bar"><div class="risk-bar-fill" style="width:<?php echo e($mock['num']); ?>%;background:<?php echo e($mock['bg']); ?>"></div></div>
      </div>
      <div style="margin-bottom:14px">
        <span style="background:<?php echo e($mock['class']==='anomaly'?'#ffeaea':'#e6faf5'); ?>;color:<?php echo e($mock['color']); ?>;padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:800"><?php echo e($mock['result']); ?></span>
      </div>
      <ul style="list-style:none;padding:0;margin:0">
        <?php $__currentLoopData = $mock['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li style="padding:5px 0;border-bottom:1px solid #f4f7fa;font-size:13px;color:#475569"><?php echo e($f); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<div class="scorer-form">
  <h4 style="margin:0 0 18px;font-size:15px;font-weight:700;color:#1a1f2e"><i class="fas fa-sliders-h me-2" style="color:#0984e3"></i>Custom test — Run AI now</h4>
  <form id="scorerForm">
    <?php echo csrf_field(); ?>
    <div class="row g-3">
      <div class="col-md-6">
        <?php $__currentLoopData = [
          ['hour_of_day','Login hour (0-23)','range',0,23,10,1],
          ['failed_attempts','Failed login count','range',0,20,0,1],
          ['keystroke_speed_ms','Keystroke speed (ms, low=fast=bot)','range',5,500,150,5],
          ['keystroke_irregularity','Keystroke irregularity (std-dev ms)','range',0,200,30,1],
          ['click_count_per_min','Click rate/min (high=bot)','range',0,500,30,5],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name,$label,$type,$min,$max,$def,$step]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="slider-row">
          <label for="s_<?php echo e($name); ?>"><?php echo e($label); ?></label>
          <input type="<?php echo e($type); ?>" id="s_<?php echo e($name); ?>" name="<?php echo e($name); ?>" min="<?php echo e($min); ?>" max="<?php echo e($max); ?>" value="<?php echo e($def); ?>" step="<?php echo e($step); ?>" oninput="document.getElementById('v_<?php echo e($name); ?>').textContent=this.value">
          <span class="val" id="v_<?php echo e($name); ?>"><?php echo e($def); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="col-md-6">
        <?php $__currentLoopData = [
          ['transaction_amount','Cart value (1000 VND)','range',0,20000,800,100],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name,$label,$type,$min,$max,$def,$step]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="slider-row">
          <label for="s_<?php echo e($name); ?>"><?php echo e($label); ?></label>
          <input type="<?php echo e($type); ?>" id="s_<?php echo e($name); ?>" name="<?php echo e($name); ?>" min="<?php echo e($min); ?>" max="<?php echo e($max); ?>" value="<?php echo e($def); ?>" step="<?php echo e($step); ?>" oninput="document.getElementById('v_<?php echo e($name); ?>').textContent=(this.value*1000).toLocaleString('vi-VN')+'₫'">
          <span class="val" id="v_<?php echo e($name); ?>">800,000₫</span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div style="display:flex;gap:16px;margin-top:8px">
          <?php $__currentLoopData = [['is_new_ip','New/unknown IP'],['is_new_device','New device']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;color:#475569">
            <input type="checkbox" id="s_<?php echo e($name); ?>" name="<?php echo e($name); ?>" value="1" style="width:16px;height:16px"> <?php echo e($label); ?>

          </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="button" id="scoreBtn" onclick="runScore()" style="margin-top:16px;padding:11px 28px;background:linear-gradient(135deg,#0984e3,#0773c5);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;width:100%">
          <i class="fas fa-brain me-2"></i>Run AI Risk Analysis
        </button>
      </div>
    </div>
  </form>

  <div id="scoreResult" class="result-box"></div>
</div>


<div class="card" style="border-radius:14px;overflow:hidden;border:1px solid #e8edf2">
  <div style="padding:14px 20px;border-bottom:1px solid #e8edf2;font-weight:700;font-size:13.5px;display:flex;align-items:center;gap:8px">
    <i class="fas fa-history" style="color:#0984e3;font-size:13px"></i> Last 10 login attempts
    <a href="<?php echo e(route('admin.security')); ?>" style="margin-left:auto;font-size:12px;font-weight:600;color:#0984e3">View all →</a>
  </div>
  <div class="table-responsive">
    <table class="live-table" style="width:100%;border-collapse:collapse">
      <thead><tr>
        <th>Users</th><th>IP</th><th>Risk Level</th><th>3FA</th><th>Result</th><th>Time</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $recentAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $rlColors = ['low'=>['#e6faf5','#00b894'],'medium'=>['#fff9ec','#f39c12'],'high'=>['#ffeaea','#e84040'],'critical'=>['#ffeaea','#8e0000']];
          $rc = $rlColors[$att->risk_level ?? 'low'] ?? ['#f4f7fa','#64748b'];
        ?>
        <tr>
          <td><strong><?php echo e($att->user->name ?? $att->email); ?></strong><div style="font-size:11px;color:#94a3b8"><?php echo e($att->email); ?></div></td>
          <td style="font-family:monospace;font-size:12px"><?php echo e($att->ip_address ?? '—'); ?></td>
          <td>
            <?php if($att->risk_level): ?>
            <span style="background:<?php echo e($rc[0]); ?>;color:<?php echo e($rc[1]); ?>;padding:3px 10px;border-radius:10px;font-size:11.5px;font-weight:700"><?php echo e(strtoupper($att->risk_level)); ?></span>
            <?php else: ?><span style="color:#b2bec3">—</span><?php endif; ?>
          </td>
          <td style="text-align:center"><?php echo e($att->required_3fa ? '<span style="color:#e84040;font-weight:700">✓ Activated</span>' : '—'); ?></td>
          <td>
            <?php if($att->success): ?>
              <span style="color:#00b894;font-weight:700">✓ Success</span>
            <?php else: ?>
              <span style="color:#e84040;font-weight:700">✗ Failed</span>
            <?php endif; ?>
          </td>
          <td style="font-size:11px;color:#94a3b8"><?php echo e($att->created_at->diffForHumans()); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px">No login attempts recorded</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
async function runScore() {
  const btn = document.getElementById('scoreBtn');
  const resultBox = document.getElementById('scoreResult');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyzing...';

  const form = document.getElementById('scorerForm');
  const data = {
    hour_of_day:            parseInt(document.getElementById('s_hour_of_day').value),
    is_new_ip:              document.getElementById('s_is_new_ip').checked ? 1 : 0,
    is_new_device:          document.getElementById('s_is_new_device').checked ? 1 : 0,
    failed_attempts:        parseInt(document.getElementById('s_failed_attempts').value),
    keystroke_speed_ms:     parseFloat(document.getElementById('s_keystroke_speed_ms').value),
    keystroke_irregularity: parseFloat(document.getElementById('s_keystroke_irregularity').value),
    transaction_amount:     parseFloat(document.getElementById('s_transaction_amount').value),
    click_count_per_min:    parseFloat(document.getElementById('s_click_count_per_min').value),
    _token: document.querySelector('[name=_token]').value,
  };

  try {
    const resp = await fetch('<?php echo e(route("admin.demo.score")); ?>', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':data._token,'Accept':'application/json'},
      body: JSON.stringify(data),
    });
    const r = await resp.json();
    if (r.error) throw new Error(r.error);

    const lvlColor = {low:'#00b894',medium:'#f39c12',high:'#e84040',critical:'#8e0000'}[r.risk_level] || '#64748b';
    const lvlBg    = {low:'#e6faf5',medium:'#fff9ec',high:'#ffeaea',critical:'#fff0f0'}[r.risk_level] || '#f4f7fa';

    resultBox.style.display = 'block';
    resultBox.style.background = lvlBg;
    resultBox.style.border = `2px solid ${lvlColor}`;
    resultBox.innerHTML = `
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
        <div style="font-size:2rem">${r.requires_3fa ? '🚨' : '✅'}</div>
        <div>
          <div style="font-size:18px;font-weight:800;color:${lvlColor}">${r.risk_level.toUpperCase()} — Risk: ${r.risk_numeric}/100</div>
          <div style="font-size:13px;color:#475569">${r.requires_3fa ? '3FA REQUIRED — Anomaly Detected!' : 'Allow normal sign-in'}</div>
        </div>
      </div>
      <div style="height:10px;background:#e8edf2;border-radius:5px;overflow:hidden;margin-bottom:12px">
        <div style="height:100%;border-radius:5px;width:${r.risk_numeric}%;background:${r.requires_3fa ? 'linear-gradient(90deg,#f39c12,#e84040)' : 'linear-gradient(90deg,#00b894,#81ecec)'}"></div>
      </div>
      ${r.explanation && r.explanation.length ? '<ul style="list-style:none;padding:0;margin:0">' + r.explanation.map(e=>`<li style="font-size:12.5px;color:#475569;padding:3px 0">⚠ ${e}</li>`).join('') + '</ul>' : '<p style="font-size:13px;color:#00b894">No anomalies detected.</p>'}
      <div style="margin-top:10px;font-size:11.5px;color:#94a3b8">IF Score: ${r.risk_score?.toFixed(4) ?? '—'} | Latency: ${r.latency_ms ?? '—'}ms</div>
    `;
  } catch(e) {
    resultBox.style.display = 'block';
    resultBox.style.background = '#ffeaea';
    resultBox.style.border = '2px solid #e84040';
    resultBox.innerHTML = `<div style="color:#e84040;font-weight:700"><i class="fas fa-times-circle me-2"></i>Error: ${e.message}</div>`;
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-brain me-2"></i>Run AI Risk Analysis';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/admin/demo.blade.php ENDPATH**/ ?>