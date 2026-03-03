@extends('layouts.app')

@section('title', 'Risk Assessment Dashboard')

@section('content')
<div class="risk-assessment py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-chart-line"></i> Risk Assessment Dashboard</h1>
                <small class="text-muted">Real-time security risk analysis and metrics</small>
            </div>
            <div>
                <span class="badge bg-secondary">Last Updated: {{ $timestamp->format('H:i:s') }}</span>
            </div>
        </div>

        <!-- Overall Risk Score Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Overall System Risk Score</h5>
                                <div class="d-flex align-items-end">
                                    <div class="risk-score-display">
                                        <h1 class="mb-0 text-{{ $overall_risk_score >= 80 ? 'danger' : ($overall_risk_score >= 60 ? 'warning' : 'success') }}">
                                            {{ round($overall_risk_score, 1) }}
                                        </h1>
                                        <small class="text-muted">/100</small>
                                    </div>
                                    <div class="ms-4">
                                        <h6 class="text-muted mb-2">Risk Level:</h6>
                                        <span class="badge bg-{{ $overall_risk_score >= 80 ? 'danger' : ($overall_risk_score >= 60 ? 'warning' : 'success') }}" style="font-size: 13px; padding: 8px 12px;">
                                            {{ $overall_risk_score >= 80 ? 'CRITICAL' : ($overall_risk_score >= 60 ? 'HIGH' : 'MEDIUM') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <canvas id="overallRiskGauge" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Metrics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Active Threats</h6>
                        <h2 class="text-danger mb-2">{{ $threat_analysis['active_threats'] }}</h2>
                        <small class="text-muted">Pending alerts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Critical Alerts</h6>
                        <h2 class="text-danger mb-2">{{ $threat_analysis['critical_threats'] }}</h2>
                        <small class="text-muted">Severity: Critical</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Unresolved Anomalies</h6>
                        <h2 class="text-warning mb-2">{{ $threat_analysis['unresolved_anomalies'] }}</h2>
                        <small class="text-muted">Login anomalies</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-0">Resolution Rate</h6>
                        <h2 class="text-success mb-2">{{ $risk_metrics['resolution_rate'] }}%</h2>
                        <small class="text-muted">Alerts resolved</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <!-- Risk Trend Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-area"></i> Risk Score Trend (30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="riskTrendChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Risk Breakdown Pie Chart -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Risk Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="riskBreakdownChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <!-- Alerts Severity Distribution -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Alert Severity Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="alertsSeverityChart" height="60"></canvas>
                    </div>
                </div>
            </div>

            <!-- Anomalies Trend -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Anomalies (7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="anomaliesTrendChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Top Threats -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-bug"></i> Top Threats</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Threat Type</th>
                                    <th style="width: 80px;">Count</th>
                                    <th style="width: 60px;">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($top_threats as $threat)
                                    <tr>
                                        <td>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $threat['type'])) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $threat['count'] }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                     style="width: {{ $threat['percentage'] }}%"
                                                     title="{{ $threat['percentage'] }}%">
                                                    {{ $threat['percentage'] }}%
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

            <!-- Risk Performance Metrics -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Performance Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>System Compliance</span>
                                <strong>{{ $risk_metrics['system_compliance'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $risk_metrics['system_compliance'] }}%">
                                    {{ $risk_metrics['system_compliance'] }}%
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Resolution Rate</span>
                                <strong>{{ $risk_metrics['resolution_rate'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $risk_metrics['resolution_rate'] }}%">
                                    {{ $risk_metrics['resolution_rate'] }}%
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-6">
                                <small class="text-muted">Avg Response Time</small>
                                <h5 class="mb-0">{{ $risk_metrics['avg_response_time'] }}</h5>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Mean Time To Resolve</small>
                                <h5 class="mb-0">{{ $risk_metrics['mttr'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Users -->
        @if($critical_users->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger bg-opacity-10 border-bottom">
                            <h5 class="mb-0 text-danger"><i class="fas fa-exclamation-circle"></i> Critical Risk Users</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Risk Score</th>
                                        <th>Alerts</th>
                                        <th>Anomalies</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($critical_users as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['user']->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $item['user']->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger" style="font-size: 13px;">{{ round($item['risk_score'], 1) }}/100</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $item['alerts_count'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $item['anomalies_count'] }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.risk-assessment.user-detail', $item['user']->id) }}" 
                                                   class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.risk-assessment.analysis') }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Detailed Analysis
                    </a>
                    <a href="{{ route('admin.alerts.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-bell"></i> Go to Alerts
                    </a>
                    <a href="{{ route('admin.anomalies.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-exclamation-triangle"></i> Go to Anomalies
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Risk Trend Chart
        const trendCtx = document.getElementById('riskTrendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($risk_trend['labels']) !!},
                    datasets: [{
                        label: 'Risk Score',
                        data: {!! json_encode($risk_trend['data']) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#dc3545',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
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
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Risk Breakdown Chart
        const breakdownCtx = document.getElementById('riskBreakdownChart');
        if (breakdownCtx) {
            new Chart(breakdownCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Authentication', 'Anomaly', 'Alerts', 'Session', 'Data Access'],
                    datasets: [{
                        data: [
                            {{ $risk_breakdown['authentication_risk'] }},
                            {{ $risk_breakdown['anomaly_risk'] }},
                            {{ $risk_breakdown['alert_risk'] }},
                            {{ $risk_breakdown['session_risk'] }},
                            {{ $risk_breakdown['data_access_risk'] }}
                        ],
                        backgroundColor: [
                            '#dc3545',
                            '#fd7e14',
                            '#ffc107',
                            '#17a2b8',
                            '#6f42c1'
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

        // Alerts Severity Chart
        const severityCtx = document.getElementById('alertsSeverityChart');
        if (severityCtx) {
            new Chart(severityCtx, {
                type: 'bar',
                data: {
                    labels: ['Critical', 'High', 'Medium', 'Low'],
                    datasets: [{
                        label: 'Alert Count',
                        data: [
                            {{ $alerts_severity_distribution['critical'] }},
                            {{ $alerts_severity_distribution['high'] }},
                            {{ $alerts_severity_distribution['medium'] }},
                            {{ $alerts_severity_distribution['low'] }}
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
                    scaled: true,
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

        // Anomalies Trend Chart
        const anomaliesCtx = document.getElementById('anomaliesTrendChart');
        if (anomaliesCtx) {
            new Chart(anomaliesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($anomalies_trend['labels']) !!},
                    datasets: [{
                        label: 'Anomalies',
                        data: {!! json_encode($anomalies_trend['data']) !!},
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253, 126, 20, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fd7e14'
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
    .risk-assessment {
        background-color: #f5f7fa;
    }

    .risk-score-display h1 {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .table-light {
        background-color: #f8f9fa;
    }
</style>
@endsection
