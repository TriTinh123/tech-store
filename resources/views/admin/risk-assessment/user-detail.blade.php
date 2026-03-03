{{-- @var \App\Models\User $user --}}
@extends('layouts.app')
@php
    /** @var \App\Models\User $user */
@endphp

@section('title', 'User Risk Profile - ' . $user->name)

@section('content')
<div class="user-risk-detail py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-user-shield"></i> {{ $user->name }}</h1>
                <small class="text-muted">{{ $user->email }} • Role: <span class="badge bg-secondary">{{ $user->role }}</span></small>
            </div>
            <a href="{{ route('admin.risk-assessment.analysis') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Analysis
            </a>
        </div>

        <div class="row">
            <!-- Risk Score Card -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Risk Score</h6>
                        <div class="risk-score-circle" style="width: 120px; height: 120px; margin: 0 auto;">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="55" fill="none" stroke="#e0e0e0" stroke-width="8"/>
                                <circle cx="60" cy="60" r="55" fill="none" 
                                        stroke="{{ $risk_score >= 80 ? '#dc3545' : ($risk_score >= 60 ? '#fd7e14' : '#28a745') }}" 
                                        stroke-width="8"
                                        stroke-dasharray="{{ ($risk_score / 100) * 345.575 }} 345.575"
                                        stroke-dashoffset="0"
                                        transform="rotate(-90 60 60)"/>
                                <text x="60" y="70" text-anchor="middle" font-size="28" font-weight="bold" 
                                      fill="{{ $risk_score >= 80 ? '#dc3545' : ($risk_score >= 60 ? '#fd7e14' : '#28a745') }}">
                                    {{ round($risk_score) }}
                                </text>
                            </svg>
                        </div>
                        <h6 class="text-muted mt-3 mb-0">Risk Level</h6>
                        <span class="badge bg-{{ $risk_score >= 80 ? 'danger' : ($risk_score >= 60 ? 'warning' : 'success') }}" style="font-size: 13px; padding: 8px 12px;">
                            {{ $risk_score >= 80 ? 'CRITICAL' : ($risk_score >= 60 ? 'HIGH' : ($risk_score >= 40 ? 'MEDIUM' : 'LOW')) }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Statistics</h6>
                        <div class="mb-3">
                            <small class="text-muted">Total Alerts</small>
                            <h4 class="mb-0">{{ $alerts->count() }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Anomalies</small>
                            <h4 class="mb-0">{{ $anomalies->count() }}</h4>
                        </div>
                        <div>
                            <small class="text-muted">Last Login</small>
                            <h6 class="mb-0">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Recommendations -->
                @if($recommendations->count() > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-{{ $recommendations->first()['severity'] === 'critical' ? 'danger' : 'warning' }} bg-opacity-10 border-bottom">
                            <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Security Recommendations</h5>
                        </div>
                        <div class="card-body">
                            @foreach($recommendations as $rec)
                                <div class="alert alert-{{ $rec['severity'] === 'critical' ? 'danger' : ($rec['severity'] === 'high' ? 'warning' : 'info') }} mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $rec['action'] }}</h6>
                                            <small>{{ $rec['description'] }}</small>
                                        </div>
                                        <span class="badge bg-{{ $rec['severity'] === 'critical' ? 'danger' : ($rec['severity'] === 'high' ? 'warning' : 'info') }}">
                                            {{ ucfirst($rec['severity']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#alerts" data-bs-toggle="tab" role="tab">
                            <i class="fas fa-bell"></i> Alerts ({{ $alerts->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#anomalies" data-bs-toggle="tab" role="tab">
                            <i class="fas fa-exclamation-triangle"></i> Anomalies ({{ $anomalies->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#logins" data-bs-toggle="tab" role="tab">
                            <i class="fas fa-sign-in-alt"></i> Login Pattern
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#events" data-bs-toggle="tab" role="tab">
                            <i class="fas fa-list"></i> Risk Events
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Alerts Tab -->
                    <div class="tab-pane fade show active" id="alerts" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Alert Title</th>
                                            <th style="width: 100px;">Type</th>
                                            <th style="width: 80px;">Severity</th>
                                            <th style="width: 80px;">Status</th>
                                            <th style="width: 100px;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($alerts as $alert)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.alerts.show', $alert->id) }}" class="text-decoration-none">
                                                        {{ $alert->title }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($alert->severity) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $alert->status === 'resolved' ? 'success' : 'warning' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $alert->created_at->format('M d, H:i') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    No alerts for this user
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Anomalies Tab -->
                    <div class="tab-pane fade" id="anomalies" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Anomaly Type</th>
                                            <th style="width: 80px;">Risk Level</th>
                                            <th style="width: 100px;">Location</th>
                                            <th style="width: 100px;">IP Address</th>
                                            <th style="width: 80px;">Status</th>
                                            <th style="width: 100px;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($anomalies as $anomaly)
                                            <tr>
                                                <td>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $anomaly->risk_level === 'critical' ? 'danger' : ($anomaly->risk_level === 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($anomaly->risk_level) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $anomaly->location ?? 'Unknown' }}</small>
                                                </td>
                                                <td>
                                                    <code style="font-size: 11px;">{{ $anomaly->ip_address }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $anomaly->is_resolved ? 'success' : 'warning' }}">
                                                        {{ $anomaly->is_resolved ? 'Resolved' : 'Pending' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $anomaly->created_at->format('M d, H:i') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    No anomalies for this user
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Login Pattern Tab -->
                    <div class="tab-pane fade" id="logins" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0">Recent Login Patterns (30 Days)</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date & Time</th>
                                            <th style="width: 120px;">IP Address</th>
                                            <th>User Agent</th>
                                            <th style="width: 80px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($login_patterns as $pattern)
                                            <tr>
                                                <td>
                                                    <small>{{ $pattern->created_at->format('M d, Y H:i:s') }}</small>
                                                </td>
                                                <td>
                                                    <code style="font-size: 11px;">{{ $pattern->ip_address }}</code>
                                                </td>
                                                <td>
                                                    <small class="text-muted" title="{{ $pattern->user_agent }}">
                                                        {{ substr($pattern->user_agent ?? 'Unknown', 0, 50) }}...
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    No login data available
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Events Tab -->
                    <div class="tab-pane fade" id="events" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0">Risk Events (30 Days)</h6>
                            </div>
                            <div class="card-body p-0">
                                @forelse($risk_events as $event)
                                    <div class="border-bottom p-3" style="border-bottom: 1px solid #dee2e6;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-{{ $event->action === 'login_failed' ? 'lock' : 'exclamation-circle' }} text-danger"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $event->action)) }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> {{ $event->ip_address ?? 'Unknown IP' }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">{{ $event->created_at->format('M d, Y H:i') }}</small>
                                                <small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        No risk events recorded
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .user-risk-detail {
        background-color: #f5f7fa;
    }

    .risk-score-circle {
        display: inline-block;
        position: relative;
    }

    .nav-tabs .nav-link {
        color: #495057;
        border: none;
        border-bottom: 2px solid transparent;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #0d6efd;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection
