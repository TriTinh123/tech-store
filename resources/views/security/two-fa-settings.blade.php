@extends('layouts.app')

@section('title', '2FA Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Two-Factor Authentication (2FA)</h1>
            <p class="text-muted">Add an extra layer of security to your account</p>
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
                        <i class="fas fa-shield-alt me-2"></i>
                        Current Status
                    </h5>
                    <hr>
                    <div class="mb-3">
                        @if($two_fa_enabled)
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <strong>2FA is enabled</strong> - Your account has 2-factor authentication enabled
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>2FA is disabled</strong> - Your account is not protected by 2-factor authentication
                            </div>
                        @endif
                    </div>

                    @if(!$two_fa_enabled)
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#setupModal">
                        <i class="fas fa-plus"></i> Enable 2FA
                    </button>
                    @else
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disableModal">
                        <i class="fas fa-times"></i> Disable 2FA
                    </button>
                    @endif
                </div>
            </div>

            <!-- 2FA Methods -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Available 2FA Methods</h5>
                    <hr>
                    <div class="row g-3">
                        <!-- Authenticator App -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-mobile-alt fa-2x text-info me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6>Authenticator App</h6>
                                        <p class="text-muted small mb-0">
                                            Use an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator 
                                            to generate one-time codes
                                        </p>
                                    </div>
                                    <span class="badge bg-success">Recommended</span>
                                </div>
                            </div>
                        </div>

                        <!-- SMS -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-sms fa-2x text-success me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6>SMS Text Message</h6>
                                        <p class="text-muted small mb-0">
                                            Receive one-time codes via SMS to your registered phone number
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Codes -->
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-key fa-2x text-warning me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6>Backup Codes</h6>
                                        <p class="text-muted small mb-0">
                                            Generate backup codes to use if you lose access to your authenticator
                                        </p>
                                    </div>
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
                                    What is 2FA?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Two-factor authentication (2FA) adds an extra layer of security by requiring a second form 
                                    of verification in addition to your password. This makes it much harder for unauthorized users 
                                    to access your account.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq2">
                                    Which method is the most secure?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Authenticator apps like Google Authenticator are generally considered the most secure because 
                                    they generate codes that are hard to intercept. SMS is also secure but slightly less so than apps.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq3">
                                    What are backup codes?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Backup codes are one-time use emergency codes. If you lose access to your authenticator or 
                                    phone, you can use these codes to regain access to your account. Keep them safe!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5><i class="fas fa-lightbulb"></i> Security Tips</h5>
                    <ul class="small">
                        <li>Enable 2FA on all your important accounts</li>
                        <li>Use an authenticator app for best security</li>
                        <li>Save your backup codes in a safe place</li>
                        <li>Never share your 2FA codes with anyone</li>
                        <li>Update your phone number if it changes</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 border-success border-2 mt-3">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                    <p class="mb-0 small text-muted">
                        2FA significantly improves your account security. We recommend enabling it today!
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
                    <h5 class="modal-title">Enable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>To enable 2FA, you will need to:</p>
                    <ol>
                        <li>Download an authenticator app (Google Authenticator, Authy, etc.)</li>
                        <li>Scan the QR code to add your account</li>
                        <li>Enter the 6-digit code from your app</li>
                        <li>Save your backup codes in a secure location</li>
                    </ol>
                    <p class="text-muted mb-0">
                        <strong>Note:</strong> You will need access to your authenticator app to log in after enabling 2FA.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('security.two-fa.enable') }}" class="d-inline">
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
                    <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong> Disabling 2FA will make your account less secure.
                    </div>
                    <p>Are you sure you want to disable 2FA? Your account will only be protected by your password.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('security.two-fa.disable') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Disable 2FA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
