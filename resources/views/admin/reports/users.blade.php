@extends('layouts.app')

@section('title', 'User Analytics')

@section('content')
<div class="admin-reports-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-users"></i> User Analytics</h1>
                <small class="text-muted">User behavior and engagement metrics</small>
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

        <!-- Key User Metrics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Total Users</small>
                        <h3 class="h2 mt-2 mb-0">{{ number_format($totalUsers) }}</h3>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +5.2% growth</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">New Users</small>
                        <h3 class="h2 mt-2 mb-0">{{ number_format($newUsersCount) }}</h3>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +12.3% vs period</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Active Users</small>
                        <h3 class="h2 mt-2 mb-0">{{ number_format($activeUsers) }}</h3>
                        <small class="text-info"><i class="fas fa-equals"></i> Currently active</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Churn Rate</small>
                        <h3 class="h2 mt-2 mb-0">{{ $churnRate }}%</h3>
                        <small class="text-danger"><i class="fas fa-arrow-down"></i> Monitor closely</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Growth and Retention Charts -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-area"></i> User Growth Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userGrowthChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> User Segmentation</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="segmentationChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Behavior and Retention -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-mouse"></i> User Behavior Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="behavior-metric mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Avg Session Duration</span>
                                <strong>{{ $userBehavior['avgSessionDuration'] }}s</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: 65%;"></div>
                            </div>
                        </div>

                        <div class="behavior-metric mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Bounce Rate</span>
                                <strong>{{ $userBehavior['bounceRate'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $userBehavior['bounceRate'] }}%;"></div>
                            </div>
                        </div>

                        <div class="behavior-metric">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Pages per Session</span>
                                <strong>{{ $userBehavior['pagesPerSession'] }} pages</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 75%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-redo"></i> Retention & LTV</h5>
                    </div>
                    <div class="card-body">
                        <div class="retention-metric mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>User Retention Rate</span>
                                <strong>{{ $userRetention }}%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $userRetention }}%;"></div>
                            </div>
                            <small class="text-muted d-block mt-2">7-day retention from new users</small>
                        </div>

                        <div class="retention-metric">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Avg User Lifetime Value</span>
                                <strong>${{ number_format($userLifetimeValue, 2) }}</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 85%;"></div>
                            </div>
                            <small class="text-muted d-block mt-2">Total value over user lifetime</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Segmentation Breakdown -->
        <div class="row mb-5">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> User Segmentation Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-4">
                                <div class="segment-card">
                                    <i class="fas fa-crown fa-2x text-warning mb-3"></i>
                                    <h4>{{ number_format($userSegmentation['premium']) }}</h4>
                                    <small class="text-muted">Premium Users</small>
                                    <small class="d-block text-success mt-2">
                                        {{ round(($userSegmentation['premium'] / $totalUsers) * 100, 2) }}% of total
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="segment-card">
                                    <i class="fas fa-user fa-2x text-primary mb-3"></i>
                                    <h4>{{ number_format($userSegmentation['standard']) }}</h4>
                                    <small class="text-muted">Standard Users</small>
                                    <small class="d-block text-success mt-2">
                                        {{ round(($userSegmentation['standard'] / $totalUsers) * 100, 2) }}% of total
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="segment-card">
                                    <i class="fas fa-user-shield fa-2x text-danger mb-3"></i>
                                    <h4>{{ number_format($userSegmentation['admin']) }}</h4>
                                    <small class="text-muted">Admin Users</small>
                                    <small class="d-block text-success mt-2">
                                        {{ round(($userSegmentation['admin'] / $totalUsers) * 100, 2) }}% of total
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Countries by Users -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-globe"></i> Top Countries by Users</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country</th>
                                    <th>Users</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCountries as $country)
                                <tr>
                                    <td>{{ $country->country }}</td>
                                    <td>{{ $country->count }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ ($country->count / $totalUsers) * 100 }}%;"></div>
                                            </div>
                                            <span class="ms-2 text-muted small">{{ round(($country->count / $totalUsers) * 100, 1) }}%</span>
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
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-stat mb-4 pb-4 border-bottom">
                            <small class="text-muted text-uppercase">2FA Adoption</small>
                            <h4 class="mt-2 mb-3">{{ $twoFactorAdoption }}%</h4>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $twoFactorAdoption }}%;"></div>
                            </div>
                        </div>

                        <div class="summary-stat">
                            <small class="text-muted text-uppercase">Countries Represented</small>
                            <h4 class="mt-2">{{ count($topCountries) }}+</h4>
                            <small class="text-muted">Global reach</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.segment-card {
    padding: 30px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.segment-card:hover {
    transform: translateY(-4px);
}

.behavior-metric,
.retention-metric {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
}

.summary-stat {
    padding: 10px 0;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const ctx1 = document.getElementById('userGrowthChart').getContext('2d');
    const growthData = @json($userGrowthChart);
    const labels1 = Object.keys(growthData);
    const data1 = Object.values(growthData);

    new Chart(ctx1, {
        type: 'area',
        data: {
            labels: labels1,
            datasets: [{
                label: 'New Users',
                data: data1,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
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

    // Segmentation Chart
    const ctx2 = document.getElementById('segmentationChart').getContext('2d');
    const segmentation = @json($userSegmentation);
    
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Premium', 'Standard', 'Admin'],
            datasets: [{
                data: [segmentation.premium, segmentation.standard, segmentation.admin],
                backgroundColor: ['#ffc107', '#0d6efd', '#dc3545'],
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
