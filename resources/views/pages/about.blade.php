@extends('layouts.app')
@section('title', 'About — TechStore')
@section('page_title', 'About TechStore')

@section('content')
<style>
    .about-wrap { max-width:1100px; margin:0 auto; padding:36px 24px 56px; }
    .about-hero { background:linear-gradient(135deg,#0b1a2e,#1a3a5c); border-radius:16px; padding:52px 48px; margin-bottom:40px; display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; position:relative; overflow:hidden; }
    .about-hero::before { content:''; position:absolute; width:400px; height:400px; background:radial-gradient(circle,rgba(0,184,148,.12) 0%,transparent 70%); top:-150px; right:-80px; pointer-events:none; }
    .ah-tag { display:inline-flex; align-items:center; gap:6px; background:rgba(0,184,148,.15); border:1px solid rgba(0,184,148,.35); color:#00b894; padding:5px 14px; border-radius:50px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; margin-bottom:16px; }
    .ah-title { font-size:36px; font-weight:900; color:white; line-height:1.15; margin-bottom:14px; }
    .ah-hl { background:linear-gradient(90deg,#00b894,#0984e3); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .ah-desc { font-size:15px; color:rgba(255,255,255,.6); line-height:1.8; }
    .ah-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; }
    .ah-stat { background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.1); border-radius:12px; padding:20px; text-align:center; }
    .ah-stat-n { font-size:30px; font-weight:900; color:white; }
    .ah-stat-l { font-size:11px; color:rgba(255,255,255,.45); text-transform:uppercase; margin-top:3px; }
    .about-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:36px; }
    .ac { background:white; border-radius:14px; padding:28px 24px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; transition:all .25s; }
    .ac:hover { box-shadow:0 8px 28px rgba(0,0,0,.1); transform:translateY(-4px); border-color:#00b894; }
    .ac-ico { width:52px; height:52px; border-radius:14px; background:#e6f7f4; display:flex; align-items:center; justify-content:center; margin-bottom:16px; transition:background .25s; }
    .ac:hover .ac-ico { background:#00b894; }
    .ac:hover .ac-ico i { color:white; }
    .ac-ico i { font-size:22px; color:#00b894; transition:color .25s; }
    .ac-title { font-size:15px; font-weight:700; color:#1a1f2e; margin-bottom:8px; }
    .ac-desc { font-size:13px; color:#64748b; line-height:1.7; }
    .ab-mission { background:white; border-radius:14px; padding:36px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; margin-bottom:36px; }
    .ab-mission h2 { font-size:20px; font-weight:800; margin-bottom:6px; display:flex; align-items:center; gap:10px; }
    .ab-mission h2::before { content:''; display:block; width:4px; height:22px; background:linear-gradient(180deg,#00b894,#0984e3); border-radius:2px; flex-shrink:0; }
    .ab-mission p { font-size:14px; color:#64748b; line-height:1.8; margin-bottom:0; margin-top:12px; }
    .commit-list { list-style:none; padding:0; margin:16px 0 0; display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .commit-list li { display:flex; align-items:center; gap:10px; font-size:13px; color:#1a1f2e; font-weight:500; background:#f4f7fa; padding:10px 14px; border-radius:10px; border-left:3px solid #00b894; }
    .commit-list li i { color:#00b894; width:16px; }
    @media(max-width:768px){.about-hero{grid-template-columns:1fr;}.about-cards{grid-template-columns:1fr;}.commit-list{grid-template-columns:1fr;}}
</style>
<div class="about-wrap">

    {{-- Hero --}}
    <div class="about-hero">
        <div style="position:relative">
            <div class="ah-tag"><i class="fas fa-award"></i> 10+ years of experience</div>
            <h1 class="ah-title">We are<br><span class="ah-hl">TechStore</span></h1>
            <p class="ah-desc">A leading retailer of genuine tech accessories. With over 10 years of experience, we proudly serve tens of thousands of customers nationwide.</p>
        </div>
        <div class="ah-stats">
            <div class="ah-stat"><div class="ah-stat-n">500+</div><div class="ah-stat-l">Products</div></div>
            <div class="ah-stat"><div class="ah-stat-n">10K+</div><div class="ah-stat-l">Customer</div></div>
            <div class="ah-stat"><div class="ah-stat-n">4.9★</div><div class="ah-stat-l">Rating</div></div>
            <div class="ah-stat"><div class="ah-stat-n">24/7</div><div class="ah-stat-l">Support</div></div>
        </div>
    </div>

    {{-- Cards --}}
    <div class="about-cards">
        <div class="ac"><div class="ac-ico"><i class="fas fa-medal"></i></div><div class="ac-title">100% Genuine</div><div class="ac-desc">All products come with full certificates of origin, genuine labels, and VAT invoices.</div></div>
        <div class="ac"><div class="ac-ico"><i class="fas fa-shipping-fast"></i></div><div class="ac-title">Express Delivery</div><div class="ac-desc">24-hour delivery in major cities. Nationwide delivery in 1–3 business days.</div></div>
        <div class="ac"><div class="ac-ico"><i class="fas fa-headset"></i></div><div class="ac-title">24/7 Support</div><div class="ac-desc">Our professional support team is available 24/7 via chat and phone.</div></div>
    </div>

    {{-- Mission --}}
    <div class="ab-mission">
        <h2>Our Mission</h2>
        <p>To provide customers with genuine tech products, competitive prices, and outstanding service. We commit to:</p>
        <ul class="commit-list">
            <li><i class="fas fa-check-circle"></i> 100% genuine products</li>
            <li><i class="fas fa-check-circle"></i> Most competitive market prices</li>
            <li><i class="fas fa-check-circle"></i> Full manufacturer warranty</li>
            <li><i class="fas fa-check-circle"></i> Fast nationwide delivery</li>
            <li><i class="fas fa-check-circle"></i> Customer Support 24/7</li>
            <li><i class="fas fa-check-circle"></i> Easy 30-day returns</li>
        </ul>
    </div>

    {{-- Why us --}}
    <div class="ab-mission">
        <h2>Why Choose TechStore?</h2>
        <p>With the goal of delivering the best shopping experience, TechStore always updates the latest products,
        provides free consultation, and ensures every customer is satisfied. We don't
        just sell products — we build long-term relationships based on trust and quality.</p>
    </div>

</div>
@endsection
