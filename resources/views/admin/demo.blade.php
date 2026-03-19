@extends('layouts.admin')
@section('title', 'AI Demo — A/B Scenarios')

@section('body-content')
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
    @if($aiOnline)
      <span style="background:#e6faf5;color:#00b894;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-circle" style="font-size:8px"></i> AI Engine Online</span>
    @else
      <span style="background:#ffeaea;color:#e84040;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-circle" style="font-size:8px"></i> AI Engine Offline</span>
    @endif
    <a href="{{ route('admin.security') }}" data-nav="/admin/security"
       style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:9px;font-size:13px;font-weight:700;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;text-decoration:none;border:none;box-shadow:0 2px 8px rgba(239,68,68,.35);transition:all .2s"
       onmouseenter="this.style.boxShadow='0 4px 16px rgba(239,68,68,.55)';this.style.transform='translateY(-1px)'"
       onmouseleave="this.style.boxShadow='0 2px 8px rgba(239,68,68,.35)';this.style.transform=''">
      <i class="fas fa-shield-alt"></i> Security Log
    </a>
  </div>
</div>

@if(!$aiOnline)
<div class="alert alert-warning mb-4" style="border-radius:10px;font-size:13px">
  <i class="fas fa-exclamation-triangle me-2"></i>
  AI Engine offline. Start it with: <code>cd python_ai &amp;&amp; python app.py</code>
  — Static demo shown below.
</div>
@endif

{{-- ─── Architecture Overview ─────────────────────────────────────────────── --}}
<div class="card mb-4" style="border-radius:14px;overflow:hidden;border:1px solid #e8edf2">
  <div style="background:linear-gradient(135deg,#1a1f2e,#2d3561);padding:18px 24px;color:#fff">
    <h4 style="margin:0;font-size:15px;font-weight:700"><i class="fas fa-sitemap me-2"></i>Adaptive 3FA System Architecture</h4>
    <p style="margin:4px 0 0;font-size:12px;opacity:.8">Design and Implementation of an AI-Driven Anomaly Detection Layer to Reinforce 3FA in E-Commerce</p>
  </div>
  <div style="padding:20px 24px;background:#f8fafc">
    <div style="display:flex;align-items:center;gap:0;flex-wrap:wrap;justify-content:center">
      @foreach([
        ['icon'=>'fa-user','label'=>'Users','sub'=>'Enter Username / Password','color'=>'#0984e3'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-envelope','label'=>'Factor 2','sub'=>'OTP qua Email','color'=>'#6c5ce7'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-brain','label'=>'AI Engine','sub'=>'Isolation Forest\nRisk Score','color'=>'#e84040'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-check-double','label'=>'Factor 3','sub'=>'Security Question / Biometric\n(if HIGH/CRITICAL)','color'=>'#f39c12'],
        ['icon'=>'fa-arrow-right','label'=>'','sub'=>'','color'=>'#b2bec3','arrow'=>true],
        ['icon'=>'fa-home','label'=>'Sign In','sub'=>'Success','color'=>'#00b894'],
      ] as $step)
        @if(isset($step['arrow']))
          <i class="fas fa-long-arrow-alt-right" style="color:#b2bec3;font-size:20px;margin:0 8px"></i>
        @else
          <div style="text-align:center;width:90px">
            <div style="width:48px;height:48px;border-radius:50%;background:{{ $step['color'] }}22;border:2px solid {{ $step['color'] }};display:flex;align-items:center;justify-content:center;margin:0 auto 6px">
              <i class="fas {{ $step['icon'] }}" style="color:{{ $step['color'] }};font-size:16px"></i>
            </div>
            <div style="font-size:12px;font-weight:700;color:#1a1f2e">{{ $step['label'] }}</div>
            <div style="font-size:10.5px;color:#64748b;line-height:1.35;white-space:pre-line">{{ $step['sub'] }}</div>
          </div>
        @endif
      @endforeach
    </div>
  </div>
</div>

{{-- ─── Scenario A/B Cards ──────────────────────────────────────────────────── --}}
@if($demoData)
@php
  $scenA = $demoData['scenarios']['normal'] ?? null;
  $scenB = $demoData['scenarios']['anomaly'] ?? null;
@endphp
<div class="demo-grid">
  @foreach([['normal',$scenA],['anomaly',$scenB]] as [$key,$sc])
  @if($sc)
  @php
    $rNum = $sc['risk_numeric'] ?? 0;
    $rl   = $sc['risk_level']   ?? 'low';
    $rlColors = ['low'=>'#00b894','medium'=>'#f39c12','high'=>'#e84040','critical'=>'#8e0000'];
    $rlColor  = $rlColors[$rl] ?? '#64748b';
    $is3fa = $sc['requires_3fa'] ?? false;
  @endphp
  <div class="demo-card {{ $key }}">
    <div class="demo-card-head">
      <div style="flex:1">
        <h3>{{ $sc['label'] }}</h3>
        <p>{{ $sc['description'] }}</p>
      </div>
      <div class="badge-risk">{{ strtoupper($rl) }}</div>
    </div>
    <div class="demo-card-body">

      {{-- Risk meter --}}
      <div class="risk-meter">
        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
          <span style="font-weight:700;color:#1a1f2e">Risk Score</span>
          <span style="font-weight:800;font-size:17px;color:{{ $rlColor }}">{{ $rNum }}<span style="font-size:11px;font-weight:400">/100</span></span>
        </div>
        <div class="risk-bar">
          <div class="risk-bar-fill" style="width:{{ $rNum }}%;background:{{ $key==='normal' ? 'linear-gradient(90deg,#00b894,#81ecec)' : 'linear-gradient(90deg,#f39c12,#e84040)' }}"></div>
        </div>
      </div>

      {{-- Decision --}}
      <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <span style="background:{{ $is3fa?'#ffeaea':'#e6faf5' }};color:{{ $is3fa?'#e84040':'#00b894' }};padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:800">
          <i class="fas {{ $is3fa?'fa-exclamation-triangle':'fa-check-circle' }}"></i>
          {{ $is3fa ? '3FA REQUIRED' : '✓ Allow sign-in' }}
        </span>
        @if($sc['is_anomaly'])
        <span style="background:#fff9ec;color:#f39c12;padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:700"><i class="fas fa-robot"></i> Anomaly Detected</span>
        @endif
      </div>

      {{-- Feature values --}}
      <div style="margin-bottom:14px">
        <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">Input Signals</div>
        @php
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
        @endphp
        @foreach($sc['session'] as $feat => $val)
        @if($feat !== 'user_id')
        @php $risky = $isRiskyVal($feat, $val); @endphp
        <div class="feat-row">
          <span class="feat-label">{{ $featureLabels[$feat] ?? $feat }}</span>
          <div style="display:flex;align-items:center;gap:8px">
            <span class="feat-val" style="{{ $risky ? 'color:#e84040' : '' }}">
              {{ is_bool($val) ? ($val?'Yes':'No') : $val }}
              @if($risky)<i class="fas fa-exclamation-circle" style="color:#e84040;font-size:10px"></i>@endif
            </span>
            @if(isset($sc['feature_contributions'][$feat]))
            <div class="feat-contrib">
              <div class="feat-contrib-fill" style="width:{{ min(100,round($sc['feature_contributions'][$feat]*100)) }}%;background:{{ $risky?'#e84040':'#00b894' }}"></div>
            </div>
            @endif
          </div>
        </div>
        @endif
        @endforeach
      </div>

      {{-- Explanations --}}
      @if(!empty($sc['explanation']))
      <div>
        <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">
          {{ $key==='normal' ? 'Analysis' : 'Anomaly reason' }}
        </div>
        <ul class="explain-list {{ $key==='normal' ? 'ok' : '' }}" style="list-style:none;margin:0;padding:0">
          @foreach($sc['explanation'] as $exp)
          <li>{{ $exp }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f4f7fa;font-size:11.5px;color:#94a3b8">
        Latency: <strong>{{ $sc['latency_ms'] ?? 0 }} ms</strong> &nbsp;|&nbsp; IF score: <strong>{{ $sc['risk_score'] ?? '—' }}</strong>
      </div>
    </div>
  </div>
  @endif
  @endforeach
</div>
@else
{{-- Offline placeholder --}}
<div class="demo-grid">
  @foreach([
    ['class'=>'normal','title'=>'Scenario A — Legitimate User','level'=>'LOW','num'=>12,'color'=>'#00b894','bg'=>'linear-gradient(90deg,#00b894,#81ecec)','features'=>['Login time: 10:00 AM','Familiar IP (Vietnam)','Known device','No failed logins','Natural typing (145ms)','Cart value: 800,000 ₫'],'result'=>'Allow sign-in — no 3FA needed'],
    ['class'=>'anomaly','title'=>'Scenario B — Attacker','level'=>'CRITICAL','num'=>92,'color'=>'#e84040','bg'=>'linear-gradient(90deg,#f39c12,#e84040)','features'=>['Login time: 3:00 AM ⚠','Unknown foreign IP ⚠','Brand new device ⚠','4 failed login attempts ⚠','Ultra-fast 12ms typing (Bot?) ⚠','Cart spike: 15,000,000 ₫ ⚠'],'result'=>'3FA REQUIRED — Biometric/security question needed'],
  ] as $mock)
  <div class="demo-card {{ $mock['class'] }}">
    <div class="demo-card-head" style="{{ $mock['class']==='normal' ? 'background:linear-gradient(135deg,#00b894,#00a381)' : 'background:linear-gradient(135deg,#e84040,#c0392b)' }}">
      <div style="flex:1"><h3>{{ $mock['title'] }}</h3><p style="opacity:.8;font-size:12px">Static demo — start AI for live results</p></div>
      <div class="badge-risk">{{ $mock['level'] }}</div>
    </div>
    <div class="demo-card-body">
      <div class="risk-meter">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px">
          <span style="font-weight:700">Risk Score</span>
          <span style="font-weight:800;font-size:17px;color:{{ $mock['color'] }}">{{ $mock['num'] }}<span style="font-size:11px;font-weight:400">/100</span></span>
        </div>
        <div class="risk-bar"><div class="risk-bar-fill" style="width:{{ $mock['num'] }}%;background:{{ $mock['bg'] }}"></div></div>
      </div>
      <div style="margin-bottom:14px">
        <span style="background:{{ $mock['class']==='anomaly'?'#ffeaea':'#e6faf5' }};color:{{ $mock['color'] }};padding:5px 14px;border-radius:20px;font-size:12.5px;font-weight:800">{{ $mock['result'] }}</span>
      </div>
      <ul style="list-style:none;padding:0;margin:0">
        @foreach($mock['features'] as $f)
        <li style="padding:5px 0;border-bottom:1px solid #f4f7fa;font-size:13px;color:#475569">{{ $f }}</li>
        @endforeach
      </ul>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- ─── Custom Scorer ────────────────────────────────────────────────────────── --}}
<div class="scorer-form">
  <h4 style="margin:0 0 18px;font-size:15px;font-weight:700;color:#1a1f2e"><i class="fas fa-sliders-h me-2" style="color:#0984e3"></i>Custom test — Run AI now</h4>
  <form id="scorerForm">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        @foreach([
          ['hour_of_day','Login hour (0-23)','range',0,23,10,1],
          ['failed_attempts','Failed login count','range',0,20,0,1],
          ['keystroke_speed_ms','Keystroke speed (ms, low=fast=bot)','range',5,500,150,5],
          ['keystroke_irregularity','Keystroke irregularity (std-dev ms)','range',0,200,30,1],
          ['click_count_per_min','Click rate/min (high=bot)','range',0,500,30,5],
        ] as [$name,$label,$type,$min,$max,$def,$step])
        <div class="slider-row">
          <label for="s_{{ $name }}">{{ $label }}</label>
          <input type="{{ $type }}" id="s_{{ $name }}" name="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $def }}" step="{{ $step }}" oninput="document.getElementById('v_{{ $name }}').textContent=this.value">
          <span class="val" id="v_{{ $name }}">{{ $def }}</span>
        </div>
        @endforeach
      </div>
      <div class="col-md-6">
        @foreach([
          ['transaction_amount','Cart value (1000 VND)','range',0,20000,800,100],
        ] as [$name,$label,$type,$min,$max,$def,$step])
        <div class="slider-row">
          <label for="s_{{ $name }}">{{ $label }}</label>
          <input type="{{ $type }}" id="s_{{ $name }}" name="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $def }}" step="{{ $step }}" oninput="document.getElementById('v_{{ $name }}').textContent=(this.value*1000).toLocaleString('vi-VN')+'₫'">
          <span class="val" id="v_{{ $name }}">800,000₫</span>
        </div>
        @endforeach
        <div style="display:flex;gap:16px;margin-top:8px">
          @foreach([['is_new_ip','New/unknown IP'],['is_new_device','New device']] as [$name,$label])
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;color:#475569">
            <input type="checkbox" id="s_{{ $name }}" name="{{ $name }}" value="1" style="width:16px;height:16px"> {{ $label }}
          </label>
          @endforeach
        </div>
        <button type="button" id="scoreBtn" onclick="runScore()" style="margin-top:16px;padding:11px 28px;background:linear-gradient(135deg,#0984e3,#0773c5);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;width:100%">
          <i class="fas fa-brain me-2"></i>Run AI Risk Analysis
        </button>
      </div>
    </div>
  </form>

  <div id="scoreResult" class="result-box"></div>
</div>

{{-- ─── Recent login attempts ─────────────────────────────────────────────── --}}
<div class="card" style="border-radius:14px;overflow:hidden;border:1px solid #e8edf2">
  <div style="padding:14px 20px;border-bottom:1px solid #e8edf2;font-weight:700;font-size:13.5px;display:flex;align-items:center;gap:8px">
    <i class="fas fa-history" style="color:#0984e3;font-size:13px"></i> Last 10 login attempts
    <a href="{{ route('admin.security') }}" style="margin-left:auto;font-size:12px;font-weight:600;color:#0984e3">View all →</a>
  </div>
  <div class="table-responsive">
    <table class="live-table" style="width:100%;border-collapse:collapse">
      <thead><tr>
        <th>Users</th><th>IP</th><th>Risk Level</th><th>3FA</th><th>Result</th><th>Time</th>
      </tr></thead>
      <tbody>
        @forelse($recentAttempts as $att)
        @php
          $rlColors = ['low'=>['#e6faf5','#00b894'],'medium'=>['#fff9ec','#f39c12'],'high'=>['#ffeaea','#e84040'],'critical'=>['#ffeaea','#8e0000']];
          $rc = $rlColors[$att->risk_level ?? 'low'] ?? ['#f4f7fa','#64748b'];
        @endphp
        <tr>
          <td><strong>{{ $att->user->name ?? $att->email }}</strong><div style="font-size:11px;color:#94a3b8">{{ $att->email }}</div></td>
          <td style="font-family:monospace;font-size:12px">{{ $att->ip_address ?? '—' }}</td>
          <td>
            @if($att->risk_level)
            <span style="background:{{ $rc[0] }};color:{{ $rc[1] }};padding:3px 10px;border-radius:10px;font-size:11.5px;font-weight:700">{{ strtoupper($att->risk_level) }}</span>
            @else<span style="color:#b2bec3">—</span>@endif
          </td>
          <td style="text-align:center">{{ $att->required_3fa ? '<span style="color:#e84040;font-weight:700">✓ Activated</span>' : '—' }}</td>
          <td>
            @if($att->success)
              <span style="color:#00b894;font-weight:700">✓ Success</span>
            @else
              <span style="color:#e84040;font-weight:700">✗ Failed</span>
            @endif
          </td>
          <td style="font-size:11px;color:#94a3b8">{{ $att->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px">No login attempts recorded</td></tr>
        @endforelse
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
    const resp = await fetch('{{ route("admin.demo.score") }}', {
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
@endsection
