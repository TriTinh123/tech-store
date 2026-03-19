@extends('layouts.app')
@section('title', 'Contact — TechStore')
@section('page_title', 'Contact Us')

@section('content')
<style>
    .ct-wrap { max-width:1100px; margin:0 auto; padding:36px 24px 56px; display:grid; grid-template-columns:380px 1fr; gap:28px; align-items:start; }
    .ct-info { display:flex; flex-direction:column; gap:16px; }
    .ct-ic { background:white; border-radius:14px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; display:flex; align-items:flex-start; gap:16px; transition:all .25s; }
    .ct-ic:hover { border-color:#00b894; transform:translateX(4px); }
    .ct-ico-wrap { width:44px; height:44px; background:#e6f7f4; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ct-ico-wrap i { color:#00b894; font-size:18px; }
    .ct-ic-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; margin-bottom:4px; }
    .ct-ic-val { font-size:13px; color:#1a1f2e; font-weight:600; line-height:1.6; }
    .ct-ic-val a { color:#0984e3; text-decoration:none; }
    .ct-form-card { background:white; border-radius:14px; padding:34px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; }
    .cf-title { font-size:18px; font-weight:800; color:#1a1f2e; margin-bottom:24px; padding-left:12px; border-left:4px solid #00b894; }
    .cf-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .cf-group { margin-bottom:16px; }
    .cf-label { display:block; font-size:12px; font-weight:600; color:#1a1f2e; margin-bottom:6px; }
    .cf-input, .cf-textarea { width:100%; padding:11px 14px; border:1.5px solid #e8edf2; border-radius:10px; font-size:13px; font-family:inherit; color:#1a1f2e; background:#fafcff; transition:all .2s; box-sizing:border-box; }
    .cf-input:focus, .cf-textarea:focus { outline:none; border-color:#00b894; background:white; box-shadow:0 0 0 3px rgba(0,184,148,.1); }
    .cf-textarea { resize:vertical; min-height:120px; }
    .cf-submit { width:100%; padding:13px; background:linear-gradient(90deg,#00b894,#0984e3); color:white; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; letter-spacing:.3px; transition:opacity .2s; }
    .cf-submit:hover { opacity:.88; }
    .cf-success { background:#e6f7f4; border:1.5px solid #00b894; color:#007a5e; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:600; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
    @media(max-width:860px){.ct-wrap{grid-template-columns:1fr;}}
</style>
<div class="ct-wrap">
    <div class="ct-info">
        <div class="ct-ic"><div class="ct-ico-wrap"><i class="fas fa-map-marker-alt"></i></div><div><div class="ct-ic-label">Address</div><div class="ct-ic-val">Alley 1, Pham Ngu Lao Street<br>Thoi Binh Ward, Ninh Kieu Dist.<br>Can Tho City</div></div></div>
        <div class="ct-ic"><div class="ct-ico-wrap"><i class="fas fa-phone-alt"></i></div><div><div class="ct-ic-label">Phone</div><div class="ct-ic-val"><a href="tel:0876211629">0876-211-629</a></div></div></div>
        <div class="ct-ic"><div class="ct-ico-wrap"><i class="fas fa-envelope"></i></div><div><div class="ct-ic-label">Email</div><div class="ct-ic-val"><a href="mailto:support@techstore.com">support@techstore.com</a><br><a href="mailto:sales@techstore.com">sales@techstore.com</a></div></div></div>
        <div class="ct-ic"><div class="ct-ico-wrap"><i class="fas fa-clock"></i></div><div><div class="ct-ic-label">Business Hours</div><div class="ct-ic-val">Mon – Fri: 08:00 – 18:00<br>Sat: 09:00 – 17:00<br>Sun: 10:00 – 16:00</div></div></div>
        <div class="ct-ic"><div class="ct-ico-wrap"><i class="fab fa-facebook-f"></i></div><div><div class="ct-ic-label">Social Media</div><div class="ct-ic-val"><a href="#">facebook.com/techstore</a><br><a href="#">zalo: 0876211629</a></div></div></div>
    </div>
    <div class="ct-form-card">
        @if(session('success'))
            <div class="cf-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="cf-title">Send us a message</div>
        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            <div class="cf-row">
                <div class="cf-group"><label class="cf-label">Your name <span style="color:#e84040">*</span></label><input type="text" name="name" class="cf-input" placeholder="John Doe" required value="{{ old('name') }}"></div>
                <div class="cf-group"><label class="cf-label">Email <span style="color:#e84040">*</span></label><input type="email" name="email" class="cf-input" placeholder="email@example.com" required value="{{ old('email') }}"></div>
            </div>
            <div class="cf-row">
                <div class="cf-group"><label class="cf-label">Phone</label><input type="tel" name="phone" class="cf-input" placeholder="090x-xxx-xxx" value="{{ old('phone') }}"></div>
                <div class="cf-group"><label class="cf-label">Subject <span style="color:#e84040">*</span></label><input type="text" name="subject" class="cf-input" placeholder="Your support request..." required value="{{ old('subject') }}"></div>
            </div>
            <div class="cf-group"><label class="cf-label">Message <span style="color:#e84040">*</span></label><textarea name="message" class="cf-textarea" placeholder="Enter your message..." required>{{ old('message') }}</textarea></div>
            <button type="submit" class="cf-submit"><i class="fas fa-paper-plane" style="margin-right:8px"></i>Send Message</button>
        </form>
    </div>
</div>
@endsection
