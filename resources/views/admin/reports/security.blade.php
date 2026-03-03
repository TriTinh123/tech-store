@extends('layouts.app')

@section('title', 'Security Metrics')

@section('content')
<div class="admin-reports-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-shield-alt"></i> Security Metrics</h1>
                <small class="text-muted">Threat detection and security analytics</small>
            </div>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Period Selector -->
        <div class="mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Period</label>
                            <select class="form-select" name="period" onchange="this.form.submit()">
                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Key Metrics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm security-metric">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Anomalies Detected</small>
                        <h3 class="h2 mt-2 mb-0 text-danger">{{ $anomaliesDetected }}</h3>
                        <small class="text-danger"><i class="fas fa-alert-triangle"></i> Review required</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm security-metric">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Failed Logins</small>
                        <h3 class="h2 mt-2 mb-0">{{ $failedLogins }}</h3>
                        <small class="text-info"><i class="fas fa-key"></i> Attempted access</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm security-metric">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Flagged Sessions</small>
                        <h3 class="h2 mt-2 mb-0">{{ count($flaggedSessions) }}</h3>
                        <small class="text-warning"><i class="fas fa-exclamation"></i> Suspicious activity</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm security-metric">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">2FA Adoption</small>
                        <h3 class="h2 mt-2 mb-0">{{ $twoFactorAdoption }}%</h3>
                        <small class="text-success"><i class="fas fa-check-circle"></i> Good coverage</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Trend and Threats -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Security Alert Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="securityTrendChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Top Threats</h5>
                    </div>
                    <div class="card-body">
                        <div class="threat-list">
                            @forelse($topThreats as $threat)
                            <div class="threat-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="threat-name">{{ $threat->threat_type ?? 'Unknown' }}</span>
                                    <span class="badge bg-danger">{{ $threat->count }}</span>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ ($threat->count / $threatMax) * 100 }}%;"></div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">No threats detected</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Security Status -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-user-check"></i> User Account Security</h5>
                    </div>
                    <div class="card-body">
                        <div class="security-status-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>2FA Enabled Accounts</span>
                                <strong>{{ $userAccountStatus['twoFactorEnabled'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 78%;"></div>
                            </div>
                        </div>

                        <div class="security-status-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Suspended Accounts</span>
                                <strong class="text-danger">{{ $userAccountStatus['suspendedAccounts'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: 5%;"></div>
                            </div>
                        </div>

                        <div class="security-status-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Secure Passwords (Updated 90+ days ago)</span>
                                <strong>{{ $userAccountStatus['securePasswords'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-network-wired"></i> IP Blacklist</h5>
                    </div>
                    <div class="card-body">
                        <div class="blacklist-stat mb-4 pb-4 border-bottom">
                            <small class="text-muted text-uppercase">Total Blacklisted</small>
                            <h4 class="mt-2">{{ $ipBlacklist['totalBlacklisted'] }}</h4>
                            <small class="text-muted">IPs blocked from access</small>
                        </div>

                        <div class="blacklist-stat">
                            <small class="text-muted text-uppercase">Recently Added (7 days)</small>
                            <h4 class="mt-2 text-warning">{{ $ipBlacklist['recentlyAdded'] }}</h4>
                            <small class="text-muted">New additions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Threat Geography -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-globe"></i> Threat Geography</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country</th>
                                    <th>Suspicious Activities</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($threatGeography as $threat)
                                <tr>
                                    <td>{{ $threat->country }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $threat->count }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-danger" style="width: {{ ($threat->count / $suspiciousMax) * 100 }}%;"></div>
                                            </div>
                                            <span class="ms-2 text-muted small">{{ round(($threat->count / $suspiciousMax) * 100, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-recommendations"></i> Security Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <div class="recommendation-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Password Policy</strong>
                                    <small class="d-block text-muted">Well maintained</small>
                                </div>
                            </div>
                        </div>

                        <div class="recommendation-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle text-warning me-3 mt-1"></i>
                                <div>
                                    <strong>2FA Coverage</strong>
                                    <small class="d-block text-muted">Increase adoption to 90%+</small>
                                </div>
                            </div>
                        </div>

                        <div class="recommendation-item">
                            <div class="d-flex">
                                <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                                <div>
                                    <strong>Session Monitoring</strong>
                                    <small class="d-block text-muted">Review flagged sessions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspicious Activities -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-alert-triangle"></i> Recent Suspicious Activities</h5>
                            <span class="badge bg-danger">{{ $suspiciousActivities->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-list">
                            @forelse($suspiciousActivities->take(10) as $activity)
                            <div class="activity-item px-4 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>{{ $activity->user?->name ?? 'Unknown User' }}</strong>
                                        <small class="d-block text-muted">{{ $activity->description }}</small>
                                        <small class="d-block text-muted mt-1">
                                            <i class="fas fa-map-marker-alt"></i> {{ $activity->ip_address }}
                                            | {{ $activity->created_at->format('M d, Y H:i:s') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-danger ms-3">{{ ucfirst($activity->status) }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-smile-wink fa-2x mb-3"></i>
                                <p>No suspicious activities detected</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.security-metric:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.threat-item {
    padding: 10px;
}

.threat-name {
    font-weight: 500;
}

.security-status-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.blacklist-stat {
    padding: 10px 0;
}

.recommendation-item {
    padding: 10px 0;
}

.activity-item {
    transition: background-color 0.3s ease;
}

.activity-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>

@php
    $threatMax = $topThreats->pluck('count')->max() ?? 1;
    $suspiciousMax = $threatGeography->pluck('count')->max() ?? 1;
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('securityTrendChart').getContext('2d');
    const trendData = @json($securityTrend);
    const labels = trendData.map(t => t.date);
    const data = trendData.map(t => t.alerts);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Security Alerts',
                data: data,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#dc3545',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    });
});
</script>
@endpush
@endsection
