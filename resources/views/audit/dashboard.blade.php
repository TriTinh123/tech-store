@extends('layouts.app')

@section('title', 'Audit Trail Dashboard')

@section('content')
<div class="audit-trail-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-history"></i> Audit Trail Dashboard</h1>
                <small class="text-muted">Security logs and event tracking</small>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('audit.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> View All Logs
                </a>
                <a href="{{ route('audit.filter') }}" class="btn btn-outline-primary">
                    <i class="fas fa-filter"></i> Advanced Filter
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-scroll fa-2x text-primary mb-3"></i>
                        <h5 class="card-title">{{ number_format($statistics['totalLogs']) }}</h5>
                        <small class="text-muted">Total Logs</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-day fa-2x text-info mb-3"></i>
                        <h5 class="card-title">{{ number_format($statistics['logsToday']) }}</h5>
                        <small class="text-muted">Today's Logs</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x text-success mb-3"></i>
                        <h5 class="card-title">{{ number_format($statistics['logsThisMonth']) }}</h5>
                        <small class="text-muted">This Month</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h5 class="card-title text-warning">{{ number_format($statistics['suspiciousToday']) }}</h5>
                        <small class="text-muted">Suspicious Today</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x text-danger mb-3"></i>
                        <h5 class="card-title text-danger">{{ number_format($statistics['failedAttemptsToday']) }}</h5>
                        <small class="text-muted">Failed Today</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x text-secondary mb-3"></i>
                        <h5 class="card-title">{{ number_format($statistics['uniqueUsersToday']) }}</h5>
                        <small class="text-muted">Unique Users</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Trend and Risk Levels -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Activity Trend (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Risk Level Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="riskChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Actions and Users -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-lightning-bolt"></i> Top Actions</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Count</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalActions = $topActions->sum('count');
                                @endphp
                                @foreach($topActions as $action)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $action->action }}</span>
                                    </td>
                                    <td>{{ number_format($action->count) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ ($action->count / $totalActions) * 100 }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-user-chart"></i> Most Active Users</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Actions</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalUserActions = $topUsers->sum('count');
                                @endphp
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('audit.user-activity', $user->user_id) }}" class="text-decoration-none">
                                            <strong>{{ $user?->user?->name ?? 'Unknown' }}</strong>
                                        </a>
                                        <small class="d-block text-muted">{{ $user?->user?->email }}</small>
                                    </td>
                                    <td>{{ number_format($user->count) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: {{ ($user->count / $totalUserActions) * 100 }}%;"></div>
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

        <!-- Security Events and Failed Attempts -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Security Events</h5>
                        <a href="{{ route('audit.index', ['suspicious' => 'true']) }}" class="btn btn-sm btn-outline-danger">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($securityEvents as $event)
                                <tr>
                                    <td>
                                        <strong>{{ $event->user?->name ?? 'Unknown' }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($event->description, 30) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $event->status === 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No security events</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-ban"></i> Recent Failed Attempts</h5>
                        <a href="{{ route('audit.index', ['status' => 'failed']) }}" class="btn btn-sm btn-outline-danger">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedAttempts as $attempt)
                                <tr>
                                    <td>
                                        <strong>{{ $attempt->user?->name ?? 'Unknown' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $attempt->action }}</span>
                                    </td>
                                    <td>
                                        <code>{{ $attempt->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No failed attempts</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
                        <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-primary">View All Logs</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Suspicious</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log['user'] ?? 'Unknown' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $log['action'] }}</span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($log['description'], 40) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log['status'] === 'success' ? 'success' : ($log['status'] === 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($log['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code>{{ $log['ip_address'] }}</code>
                                    </td>
                                    <td>
                                        @if($log['is_suspicious'])
                                            <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log['time'] }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.audit-trail-container {
    background-color: #f8f9fa;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const activityData = @json($activityTrend);
    const labels = Object.keys(activityData);
    const data = Object.values(activityData);

    const ctx1 = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Log Events',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#667eea',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Risk Chart
    const riskData = @json($riskLevels);
    const ctx2 = document.getElementById('riskChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [riskData.critical, riskData.high, riskData.medium, riskData.low],
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745'],
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
@endsection
