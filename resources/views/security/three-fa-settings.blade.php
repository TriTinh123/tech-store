@extends('layouts.app')

@section('title', '3FA Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Three-Factor Authentication (3FA)</h1>
            <p class="text-muted">Maximum security with three authentication methods</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Security
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="fas fa-triangle me-2"></i>
                        Current Status
                    </h5>
                    <hr>
                    <div class="mb-3">
                        @if($three_fa_enabled)
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <strong>3FA is enabled</strong> - Your account has the maximum security with 3-factor authentication
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>3FA is disabled</strong> - Upgrade to 3FA for maximum security
                            </div>
                        @endif
                    </div>

                    @if(!$three_fa_enabled)
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#setupModal">
                        <i class="fas fa-plus"></i> Enable 3FA
                    </button>
                    @else
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disableModal">
                        <i class="fas fa-times"></i> Disable 3FA
                    </button>
                    @endif
                </div>
            </div>

            <!-- 3FA Authentication Factors -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">The Three Authentication Factors</h5>
                    <hr>
                    <div class="row g-3">
                        <!-- Factor 1: Something You Know -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="text-uppercase small mb-2">
                                    <span class="badge bg-primary">Factor 1</span> Something You Know
                                </h6>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-key fa-2x text-info me-3"></i>
                                    <div>
                                        <p class="mb-0 small">
                                            Your password - information only you should know
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Factor 2: Something You Have -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="text-uppercase small mb-2">
                                    <span class="badge bg-success">Factor 2</span> Something You Have
                                </h6>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-mobile-alt fa-2x text-success me-3"></i>
                                    <div>
                                        <p class="mb-0 small">
                                            Authenticator app or SMS - a physical device you possess
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Factor 3: Something You Are -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="text-uppercase small mb-2">
                                    <span class="badge bg-warning">Factor 3</span> Something You Are
                                </h6>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-fingerprint fa-2x text-warning me-3"></i>
                                    <div>
                                        <p class="mb-0 small">
                                            Biometric authentication - fingerprint, facial recognition, or device fingerprint
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Benefits -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Why 3FA?</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong>Maximum Security</strong>
                                    <p class="small text-muted mb-0">Three layers of protection against unauthorized access</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong>Phishing Resistant</strong>
                                    <p class="small text-muted mb-0">Even if password is compromised, account is still secure</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong>Peace of Mind</strong>
                                    <p class="small text-muted mb-0">Know that your account has the strongest protection</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong>Industry Standard</strong>
                                    <p class="small text-muted mb-0">Trusted by major financial and government institutions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Frequently Asked Questions</h5>
                    <hr>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq1">
                                    Is 3FA more secure than 2FA?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, 3FA provides an additional layer of security by requiring a third factor of authentication. 
                                    Even if an attacker obtains your password and bypasses your 2FA, the third factor (biometric or device fingerprint) 
                                    provides additional protection.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq2">
                                    Will I need 3FA every time I log in?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The login flow depends on your settings. On trusted devices, you may need fewer factors. 
                                    On new or suspicious devices, you will need to complete all three factors.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq3">
                                    What biometric options are available?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We support fingerprint and facial recognition on devices that have these capabilities. 
                                    We also use device fingerprinting as a fallback biometric method.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq4">
                                    Can I use 3FA on multiple devices?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, 3FA works across all your devices. Each device will contribute its own biometric data 
                                    to the authentication process.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card border-0 bg-light mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-star text-warning"></i> Premium Security</h5>
                    <p class="small text-muted mb-2">
                        3FA is the highest level of account security we offer. It's perfect for accounts with sensitive data or high-value transactions.
                    </p>
                    <hr>
                    <ul class="small mb-0">
                        <li><strong>3 layers of security</strong></li>
                        <li>Works on all devices</li>
                        <li>Backup authentication methods</li>
                        <li>Emergency access codes</li>
                        <li>Account recovery options</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 border-danger border-2">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt text-danger fa-2x mb-2"></i>
                    <p class="mb-0 small">
                        <strong>Ultimate Protection</strong><br>
                        3FA provides the strongest defense against unauthorized account access
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Setup Modal -->
    <div class="modal fade" id="setupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enable Three-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>To enable 3FA, you will need to:</p>
                    <ol>
                        <li>Have 2FA already enabled</li>
                        <li>Set up biometric authentication on your device</li>
                        <li>Generate and save backup codes</li>
                        <li>Confirm 3FA setup in your security settings</li>
                    </ol>
                    <div class="alert alert-info mb-0">
                        <strong>Note:</strong> 3FA requires 2FA to be enabled first. 
                        <a href="{{ route('security.two-fa') }}">Enable 2FA now</a> if you haven't already.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('security.three-fa.enable') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Proceed to Setup</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Modal -->
    <div class="modal fade" id="disableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disable Three-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong> Disabling 3FA will reduce your account security.
                    </div>
                    <p>You will still have 2FA protection, but the additional third factor will be removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('security.three-fa.disable') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Disable 3FA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
