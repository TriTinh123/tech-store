@extends('layouts.app')

@section('title', 'Login History')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Login History</h1>
            <p class="text-muted">Monitor all your login activities</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Security
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Device</th>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginHistory as $session)
                        <tr>
                            <td>
                                <strong>{{ $session->login_at->format('M d, Y') }}</strong><br>
                                <small class="text-muted">{{ $session->login_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($session->device)
                                    {{ $session->device->device_name }}
                                @else
                                    <span class="text-muted">Unknown Device</span>
                                @endif
                            </td>
                            <td>
                                <code>{{ $session->ip_address }}</code>
                            </td>
                            <td>{{ $session->device->browser ?? 'Unknown' }}</td>
                            <td>{{ $session->device->os ?? 'Unknown' }}</td>
                            <td>
                                @if($session->logged_out_at)
                                    {{ $session->session_duration }} min
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if($session->logged_out_at)
                                    <span class="badge bg-secondary">Logged Out</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No login history found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($loginHistory->hasPages())
    <div class="mt-4">
        {{ $loginHistory->links() }}
    </div>
    @endif

    <!-- Alert for suspicions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <h5 class="alert-heading"><i class="fas fa-shield-alt"></i> Security Notice</h5>
                <p class="mb-0">See any logins you don't recognize? 
                    <a href="{{ route('security.alerts') }}" class="alert-link">Check your security alerts</a> 
                    and consider 
                    <a href="{{ route('profile.change-password') }}" class="alert-link">changing your password</a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
