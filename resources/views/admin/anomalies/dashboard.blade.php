@extends('layouts.app')

@section('title', 'Anomaly Detection Dashboard')

@section('content')
<div class="anomaly-detection-dashboard py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-shield-alt"></i> Anomaly Detection Dashboard</h1>
                <small class="text-muted">Real-time login anomaly monitoring and analysis</small>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.anomalies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> View All Anomalies
                </a>
                <a href="{{ route('admin.anomalies.geo-map') }}" class="btn btn-outline-info">
                    <i class="fas fa-globe"></i> Geographic Map
                </a>
                <a href="{{ route('admin.anomalies.risk-report') }}" class="btn btn-outline-primary">
                    <i class="fas fa-chart-bar"></i> Risk Report
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
                                <h6 class="text-muted mb-2">Total</h6>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <i class="fas fa-chart-line text-success" style="font-size: 24px;"></i>
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
                            <i class="fas fa-clock text-warning" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Locked</h6>
                                <h3 class="mb-0">{{ $stats['blocked_users'] }}</h3>
                            </div>
                            <i class="fas fa-lock text-secondary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Anomaly Trend Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> 30-Day Anomaly Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="anomalyTrendChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Risk Level Breakdown -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Risk Level Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="riskLevelChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anomaly Types & Top Users -->
        <div class="row mb-4">
            <!-- Top Anomaly Types -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Top Anomaly Types</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                @forelse($topAnomalyTypes as $type)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                @switch($type->anomaly_type)
                                                    @case('new_location')
                                                        <i class="fas fa-map-marker-alt"></i> New Location
                                                    @break
                                                    @case('new_device')
                                                        <i class="fas fa-mobile-alt"></i> New Device
                                                    @break
                                                    @case('unusual_time')
                                                        <i class="fas fa-clock"></i> Unusual Time
                                                    @break
                                                    @case('failed_login')
                                                        <i class="fas fa-times-circle"></i> Failed Login
                                                    @break
                                                    @case('multiple_failed')
                                                        <i class="fas fa-exclamation"></i> Multiple Failed
                                                    @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $type->anomaly_type)) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ $type->count }}</strong>
                                            <small class="text-muted">
                                                @php
                                                    $percentage = ($type->count / $stats['total']) * 100;
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
                                        <td class="text-center text-muted">No anomalies recorded</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Targeted Users -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Most Targeted Users</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Anomalies</th>
                                    <th>Risk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topTargetedUsers as $target)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.anomalies.by-user', $target->user_id) }}">
                                                {{ $target->user?->name ?? 'Unknown' }}
                                            </a>
                                            <br><small class="text-muted">{{ $target->user?->email ?? 'N/A' }}</small>
                                        </td>
                                        <td><strong>{{ $target->anomaly_count }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $target->highest_risk === 'critical' ? 'danger' : ($target->highest_risk === 'high' ? 'warning' : 'info') }}">
                                                {{ ucfirst($target->highest_risk) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No targeted users</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geo Anomalies & Recent Events -->
        <div class="row mb-4">
            <!-- Geographic Anomalies -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-globe"></i> Geographic Anomalies</h5>
                        <a href="{{ route('admin.anomalies.geo-map') }}" class="btn btn-sm btn-outline-primary">
                            View Map
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Count</th>
                                    <th>Risk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($geoAnomalies as $geo)
                                    <tr>
                                        <td>
                                            <i class="fas fa-map-pin"></i> {{ $geo->country }}
                                        </td>
                                        <td><strong>{{ $geo->count }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $geo->risk_level === 'critical' ? 'danger' : ($geo->risk_level === 'high' ? 'warning' : 'info') }}">
                                                {{ ucfirst($geo->risk_level) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No geographic data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Failed Login Attempts -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-times-circle"></i> Recent Failed Attempts</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedLoginAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.users.show', $attempt->user_id) }}">
                                                {{ $attempt->user?->email ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>
                                            <small>{{ $attempt->ip_address }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No failed attempts</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Anomalies -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Anomalies</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>IP Address</th>
                            <th>Risk Level</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAnomalies as $anomaly)
                            <tr>
                                <td><small>#{{ $anomaly->id }}</small></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $anomaly->user_id) }}">
                                        {{ $anomaly->user?->email ?? 'Unknown' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                    </span>
                                </td>
                                <td><small>{{ $anomaly->ip_address }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $anomaly->risk_level === 'critical' ? 'danger' : ($anomaly->risk_level === 'high' ? 'warning' : ($anomaly->risk_level === 'medium' ? 'warning' : 'success')) }}">
                                        {{ ucfirst($anomaly->risk_level) }}
                                    </span>
                                </td>
                                <td>{{ $anomaly->country ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $anomaly->status === 'new' ? 'info' : ($anomaly->status === 'resolved' ? 'success' : 'warning') }}">
                                        {{ ucfirst($anomaly->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $anomaly->created_at->diffForHumans() }}</small></td>
                                <td>
                                    <a href="{{ route('admin.anomalies.show', $anomaly->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle"></i> No anomalies detected
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
    // Anomaly Trend Chart
    const trendCtx = document.getElementById('anomalyTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [@foreach($anomalyTrend as $trend) '{{ $trend["date"] }}', @endforeach],
            datasets: [{
                label: 'Anomalies',
                data: [@foreach($anomalyTrend as $trend) {{ $trend["count"] }}, @endforeach],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#007bff',
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

    // Risk Level Chart
    const riskCtx = document.getElementById('riskLevelChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [{{ $riskLevelBreakdown['critical'] }}, {{ $riskLevelBreakdown['high'] }}, {{ $riskLevelBreakdown['medium'] }}, {{ $riskLevelBreakdown['low'] }}],
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
