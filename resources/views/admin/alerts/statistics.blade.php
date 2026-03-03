@extends('layouts.app')

@section('title', 'Alert Statistics & Reports')

@section('content')
<div class="alert-statistics py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-chart-area"></i> Alert Statistics & Reports</h1>
                <small class="text-muted">Comprehensive analysis of security alerts and response metrics</small>
            </div>
            <div>
                <a href="{{ route('admin.alerts.export') }}" class="btn btn-outline-primary">
                    <i class="fas fa-download"></i> Export Report
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.alerts.statistics') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Severity</label>
                        <select name="severity" class="form-select">
                            <option value="">All Severities</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Total Alerts</h6>
                        <h2 class="mb-0">{{ $totalAlerts }}</h2>
                        <small class="text-muted">
                            <i class="fas fa-arrow-up text-success"></i> 
                            {{ $alertTrend ?? 0 }}% from last period
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Response Rate</h6>
                        <h2 class="text-success mb-0">{{ $responseRate ?? 0 }}%</h2>
                        <small class="text-muted">
                            <strong>{{ $totalResponded }}</strong> of {{ $totalAlerts }} responded
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Avg Response Time</h6>
                        <h2 class="text-info mb-0">{{ $avgResponseTime ?? 'N/A' }}</h2>
                        <small class="text-muted">Time to first response</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Resolution Rate</h6>
                        <h2 class="text-warning mb-0">{{ $resolutionRate ?? 0 }}%</h2>
                        <small class="text-muted">
                            <strong>{{ $totalResolved }}</strong> resolved
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Alert Coverage by Severity -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Alerts by Severity</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="severityChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Alert Trend Over Time -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Alert Trend (30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Response Action Breakdown -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Response Action Distribution</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actionDistribution as $action => $count)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $action)) }}</span>
                                        </td>
                                        <td><strong>{{ $count }}</strong></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ ($count / $totalResponded) * 100 }}%"
                                                     title="{{ round(($count / $totalResponded) * 100) }}%">
                                                    {{ round(($count / $totalResponded) * 100) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Alert Type Distribution -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-sitemap"></i> Alerts by Type</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Alert Type</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeDistribution as $type => $count)
                                    <tr>
                                        <td>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
                                        </td>
                                        <td><strong>{{ $count }}</strong></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ ($count / $totalAlerts) * 100 }}%"
                                                     title="{{ round(($count / $totalAlerts) * 100) }}%">
                                                    {{ round(($count / $totalAlerts) * 100) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Top Affected Users -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Top Affected Users</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th style="width: 100px;">Alerts</th>
                                    <th style="width: 150px;">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUsers as $user)
                                    <tr>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $user->alert_count }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                     style="width: {{ ($user->alert_count / ($topUsers->first()->alert_count ?? 1)) * 100 }}%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Response Time Distribution -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Response Time Analysis</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Time Range</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Under 1 hour</td>
                                    <td><strong>{{ $responseTimeUnder1h ?? 0 }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ (($responseTimeUnder1h ?? 0) / ($totalResponded ?: 1)) * 100 }}%">
                                                {{ round((($responseTimeUnder1h ?? 0) / ($totalResponded ?: 1)) * 100) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1 - 4 hours</td>
                                    <td><strong>{{ $responseTime1to4h ?? 0 }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ (($responseTime1to4h ?? 0) / ($totalResponded ?: 1)) * 100 }}%">
                                                {{ round((($responseTime1to4h ?? 0) / ($totalResponded ?: 1)) * 100) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4 - 24 hours</td>
                                    <td><strong>{{ $responseTime4to24h ?? 0 }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ (($responseTime4to24h ?? 0) / ($totalResponded ?: 1)) * 100 }}%">
                                                {{ round((($responseTime4to24h ?? 0) / ($totalResponded ?: 1)) * 100) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Over 24 hours</td>
                                    <td><strong>{{ $responseTimeOver24h ?? 0 }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-danger" role="progressbar" 
                                                 style="width: {{ (($responseTimeOver24h ?? 0) / ($totalResponded ?: 1)) * 100 }}%">
                                                {{ round((($responseTimeOver24h ?? 0) / ($totalResponded ?: 1)) * 100) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Alert Log -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-list"></i> All Alerts (Filtered)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Alert</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Created</th>
                            <th>Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.alerts.show', $alert->id) }}" class="text-decoration-none">
                                        {{ Str::limit($alert->title, 40) }}
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
                                    <span class="badge bg-{{ $alert->status === 'pending' ? 'warning' : ($alert->status === 'resolved' ? 'success' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $alert->user?->name ?? 'Unknown' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $alert->created_at->format('M d, H:i') }}</small>
                                </td>
                                <td>
                                    @if($alert->latestResponse)
                                        <small class="badge bg-secondary">
                                            {{ ucfirst($alert->latestResponse->action) }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No alerts found for the selected filters
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($alerts->hasPages())
                <div class="card-footer bg-white">
                    {{ $alerts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Severity Chart
        const severityCtx = document.getElementById('severityChart');
        if (severityCtx) {
            new Chart(severityCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Critical', 'High', 'Medium', 'Low'],
                    datasets: [{
                        data: [
                            {{ $severityDistribution['critical'] ?? 0 }},
                            {{ $severityDistribution['high'] ?? 0 }},
                            {{ $severityDistribution['medium'] ?? 0 }},
                            {{ $severityDistribution['low'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#dc3545',
                            '#fd7e14',
                            '#ffc107',
                            '#28a745'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Trend Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels ?? []) !!},
                    datasets: [{
                        label: 'Alerts',
                        data: {!! json_encode($trendData ?? []) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

<style>
    .alert-statistics {
        background-color: #f5f7fa;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
