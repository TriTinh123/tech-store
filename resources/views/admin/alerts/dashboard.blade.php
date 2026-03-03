@extends('layouts.app')

@section('title', 'Alert Management Dashboard')

@section('content')
<div class="alert-management-dashboard py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-bell"></i> Alert Management Dashboard</h1>
                <small class="text-muted">Monitor and respond to security alerts in real-time</small>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> All Alerts
                </a>
                <a href="{{ route('admin.alerts.response-center') }}" class="btn btn-outline-primary">
                    <i class="fas fa-reply"></i> Response Center
                </a>
                <a href="{{ route('admin.alerts.statistics') }}" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar"></i> Statistics
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Today</h6>
                                <h3 class="mb-0">{{ $stats['today'] }}</h3>
                            </div>
                            <i class="fas fa-calendar-day text-info" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">This Month</h6>
                                <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                            </div>
                            <i class="fas fa-calendar-alt text-primary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm border-left-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Critical</h6>
                                <h3 class="mb-0 text-danger">{{ $stats['critical_today'] }}</h3>
                            </div>
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Pending</h6>
                                <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                            </div>
                            <i class="fas fa-hourglass-half text-warning" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Resolved</h6>
                                <h3 class="mb-0 text-success">{{ $stats['resolved'] }}</h3>
                            </div>
                            <i class="fas fa-check-circle text-success" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Unresolved</h6>
                                <h3 class="mb-0">{{ $stats['unresolved'] }}</h3>
                            </div>
                            <i class="fas fa-inbox text-secondary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Alert Trend Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> 30-Day Alert Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="alertTrendChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Severity Breakdown -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Severity Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="severityChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Types & Top Users -->
        <div class="row mb-4">
            <!-- Alert Types -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Alert Types Distribution</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                @forelse($alertsByType as $type)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst(str_replace('_', ' ', $type->alert_type)) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ $type->count }}</strong>
                                            <small class="text-muted">
                                                @php
                                                    $total = $alertsByType->sum('count');
                                                    $percentage = ($type->count / $total) * 100;
                                                @endphp
                                                ({{ number_format($percentage, 1) }}%)
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No alerts</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Affected Users -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Most Alerted Users</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Alerts</th>
                                    <th>Highest Severity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topAffectedUsers as $user)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.users.show', $user->user_id) }}">
                                                {{ $user->user?->name ?? 'Unknown' }}
                                            </a>
                                            <br><small class="text-muted">{{ $user->user?->email ?? 'N/A' }}</small>
                                        </td>
                                        <td><strong>{{ $user->alert_count }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $user->highest_severity === 'critical' ? 'danger' : ($user->highest_severity === 'high' ? 'warning' : 'info') }}">
                                                {{ ucfirst($user->highest_severity) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No user alerts</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Metrics & Recent Alerts -->
        <div class="row mb-4">
            <!-- Response Metrics -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Response Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="text-muted">Response Rate</h6>
                            <h3 class="mb-2">{{ $responseMetrics['response_rate'] }}%</h3>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $responseMetrics['response_rate'] }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-muted">Avg Response Time</h6>
                            <h4>{{ $responseMetrics['avg_response_time'] }} mins</h4>
                        </div>
                        <div>
                            <h6 class="text-muted">Total Responses</h6>
                            <h4>{{ $responseMetrics['total_responses'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Alerts -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Pending Alerts Requiring Action</h5>
                        <a href="{{ route('admin.alerts.response-center') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Alert</th>
                                    <th>User</th>
                                    <th>Severity</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingAlerts as $alert)
                                    <tr>
                                        <td>
                                            <strong>{{ $alert->title }}</strong>
                                            <br><small class="text-muted">{{ $alert->alert_type }}</small>
                                        </td>
                                        <td>{{ $alert->user?->email ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : ($alert->severity === 'medium' ? 'warning' : 'success')) }}">
                                                {{ ucfirst($alert->severity) }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small></td>
                                        <td>
                                            <a href="{{ route('admin.alerts.show', $alert->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle"></i> No pending alerts
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Alerts</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAlerts as $alert)
                            <tr>
                                <td><small>#{{ $alert->id }}</small></td>
                                <td><strong>{{ $alert->title }}</strong></td>
                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $alert->user_id) }}">
                                        {{ $alert->user?->email ?? 'Unknown' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : ($alert->severity === 'medium' ? 'warning' : 'success')) }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alert->status === 'pending' ? 'warning' : ($alert->status === 'resolved' ? 'success' : 'info') }}">
                                        {{ ucfirst($alert->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small></td>
                                <td>
                                    <a href="{{ route('admin.alerts.show', $alert->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No alerts to display
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Alert Trend Chart
    const trendCtx = document.getElementById('alertTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [@foreach($alertTrend as $trend) '{{ $trend["date"] }}', @endforeach],
            datasets: [{
                label: 'Alerts',
                data: [@foreach($alertTrend as $trend) {{ $trend["count"] }}, @endforeach],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#dc3545',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: true } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (value) => Math.round(value) } }
            }
        }
    });

    // Severity Chart
    const severityCtx = document.getElementById('severityChart').getContext('2d');
    new Chart(severityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [{{ $alertsBySeverity['critical'] }}, {{ $alertsBySeverity['high'] }}, {{ $alertsBySeverity['medium'] }}, {{ $alertsBySeverity['low'] }}],
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745'],
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<style>
    .border-left-danger {
        border-left: 4px solid #dc3545;
    }

    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    table.table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
