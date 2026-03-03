@extends('layouts.app')

@section('title', 'Security Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Security Dashboard</h1>
            <p class="text-muted">Monitor and manage your account security</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('security.devices') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-mobile-alt"></i> Devices
                </a>
                <a href="{{ route('security.logins') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-history"></i> Login History
                </a>
            </div>
        </div>
    </div>

    <!-- Security Score Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <svg width="150" height="150" viewBox="0 0 150 150" style="position: absolute;">
                            <circle cx="75" cy="75" r="65" fill="none" stroke="#e9ecef" stroke-width="8"/>
                            <circle cx="75" cy="75" r="65" fill="none" stroke="#{{ $security_score >= 80 ? '28a745' : ($security_score >= 60 ? 'ffc107' : 'dc3545') }}" 
                                    stroke-width="8" stroke-dasharray="{{ ($security_score / 100) * 408.4 }} 408.4"
                                    stroke-linecap="round" transform="rotate(-90 75 75)"/>
                        </svg>
                        <div class="text-center">
                            <div class="h2 mb-0 fw-bold">{{ $security_score }}</div>
                            <small class="text-muted">Security Score</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $security_score >= 80 ? 'success' : ($security_score >= 60 ? 'warning' : 'danger') }}">
                            {{ $security_score >= 80 ? 'Excellent' : ($security_score >= 60 ? 'Good' : 'At Risk') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Status Cards -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">2FA Status</p>
                            <h4 class="mb-0">
                                @if($two_fa_enabled)
                                    <span class="badge bg-success">Enabled</span>
                                @else
                                    <span class="badge bg-danger">Disabled</span>
                                @endif
                            </h4>
                        </div>
                        <i class="fas fa-shield-alt fa-2x text-{{ $two_fa_enabled ? 'success' : 'danger' }}"></i>
                    </div>
                    <hr>
                    <a href="{{ route('security.two-fa') }}" class="btn btn-sm btn-link p-0">
                        Manage 2FA →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Active Sessions</p>
                            <h4 class="mb-0">{{ $active_sessions->count() }}</h4>
                        </div>
                        <i class="fas fa-laptop fa-2x text-info"></i>
                    </div>
                    <hr>
                    <a href="{{ route('security.sessions') }}" class="btn btn-sm btn-link p-0">
                        View Sessions →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Trusted Devices</p>
                            <h4 class="mb-0">{{ $trusted_devices->count() }}</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <hr>
                    <a href="{{ route('security.devices') }}" class="btn btn-sm btn-link p-0">
                        View Devices →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if(count($recommendations) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb text-warning"></i> Security Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        @foreach($recommendations as $rec)
                        <div class="alert alert-{{ $rec['priority'] == 'high' ? 'danger' : ($rec['priority'] == 'medium' ? 'warning' : 'info') }} 
                             mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="alert-heading mb-1">{{ $rec['title'] }}</h6>
                                <small>{{ $rec['description'] }}</small>
                            </div>
                            <a href="{{ $rec['route'] }}" class="btn btn-sm btn-outline-{{ $rec['priority'] == 'high' ? 'danger' : ($rec['priority'] == 'medium' ? 'warning' : 'info') }}">
                                {{ $rec['action'] }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Alerts -->
    @if($recent_alerts->count() > 0)
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Alerts</h5>
                    <a href="{{ route('security.alerts') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recent_alerts->take(5) as $alert)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-3">
                                <span class="badge bg-{{ $alert->severity == 'critical' ? 'danger' : ($alert->severity == 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($alert->severity) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $alert->alert_type }}</h6>
                                <small class="text-muted">{{ $alert->message }}</small><br>
                                <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Anomalies -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Anomalies</h5>
                    <a href="{{ route('security.logins') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recent_anomalies as $anomaly)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ $anomaly->location }}
                                    </small><br>
                                    <small class="text-muted">{{ $anomaly->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-warning">Risk: {{ $anomaly->risk_level }}%</span>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                            No suspicious activities detected
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Active Sessions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Sessions</h5>
                    @if($active_sessions->count() > 1)
                    <form method="POST" action="{{ route('security.sessions.end-all') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Are you sure? This will end all sessions.')">
                            End All Sessions
                        </button>
                    </form>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Device</th>
                                <th>IP Address</th>
                                <th>Browser</th>
                                <th>Login Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($active_sessions as $session)
                            <tr>
                                <td>
                                    @if($session->device)
                                        {{ $session->device->device_name }}
                                    @else
                                        Unknown Device
                                    @endif
                                </td>
                                <td><code>{{ $session->ip_address }}</code></td>
                                <td>{{ $session->device->browser ?? 'Unknown' }}</td>
                                <td>{{ $session->login_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($session->session_id !== session()->getId())
                                    <form method="POST" action="{{ route('security.sessions.end', $session->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('End this session?')">
                                            End
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge bg-info">Current</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No active sessions
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-4">Security Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('security.devices') }}" class="btn btn-light border text-start d-block w-100">
                                <i class="fas fa-mobile-alt text-info"></i>
                                <div class="mt-2">
                                    <strong>Manage Devices</strong>
                                    <small class="d-block text-muted">View and manage your trusted devices</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('security.two-fa') }}" class="btn btn-light border text-start d-block w-100">
                                <i class="fas fa-shield-alt text-success"></i>
                                <div class="mt-2">
                                    <strong>2FA Settings</strong>
                                    <small class="d-block text-muted">Enable two-factor authentication</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('security.three-fa') }}" class="btn btn-light border text-start d-block w-100">
                                <i class="fas fa-triangle text-warning"></i>
                                <div class="mt-2">
                                    <strong>3FA Settings</strong>
                                    <small class="d-block text-muted">Enable three-factor authentication</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('security.logins') }}" class="btn btn-light border text-start d-block w-100">
                                <i class="fas fa-history text-primary"></i>
                                <div class="mt-2">
                                    <strong>Login History</strong>
                                    <small class="d-block text-muted">View all your login activities</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
