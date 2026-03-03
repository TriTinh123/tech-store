@extends('layouts.app')

@section('title', 'Session Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Active Sessions</h1>
            <p class="text-muted">Manage your active login sessions</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Security
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($sessions->count() > 1)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="alert-heading mb-0"><i class="fas fa-info-circle"></i> You have {{ $sessions->count() }} active sessions</h5>
                    <small>You can end inactive sessions to keep your account secure</small>
                </div>
                <form method="POST" action="{{ route('security.sessions.end-all') }}" class="d-inline" 
                      onsubmit="return confirm('End all sessions except current? You will need to log in again.')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        End All Other Sessions
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Device</th>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Login Time</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>
                                <strong>
                                    @if($session->device)
                                        {{ $session->device->device_name }}
                                        @if($session->session_id == session()->getId())
                                        <span class="badge bg-info ms-2">Current</span>
                                        @endif
                                    @else
                                        Unknown Device
                                    @endif
                                </strong>
                            </td>
                            <td><code>{{ $session->ip_address }}</code></td>
                            <td>{{ $session->device->browser ?? 'Unknown' }}</td>
                            <td>{{ $session->device->os ?? 'Unknown' }}</td>
                            <td>{{ $session->login_at->format('M d, Y H:i') }}</td>
                            <td>
                                @php
                                    $duration = $session->login_at->diffInMinutes(now());
                                    $hours = intdiv($duration, 60);
                                    $mins = $duration % 60;
                                @endphp
                                @if($hours > 0)
                                    {{ $hours }}h {{ $mins }}m
                                @else
                                    {{ $mins }}m
                                @endif
                            </td>
                            <td>
                                @if($session->session_id != session()->getId())
                                <form method="POST" action="{{ route('security.sessions.end', $session->id) }}" 
                                      class="d-inline" onsubmit="return confirm('End this session?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-sign-out-alt"></i> End Session
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">Your current session</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No active sessions</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Session Security Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-lightbulb"></i> Session Security Tips</h5>
                <ul class="mb-0">
                    <li>A session is created when you log in to your account</li>
                    <li>If you see unfamiliar devices or IP addresses, end those sessions immediately</li>
                    <li>You can end all sessions except the current one for maximum security</li>
                    <li>Check your login history regularly for unauthorized access</li>
                    <li>Consider enabling 2FA to add an extra layer of security</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
