@extends('layouts.app')

@section('title', 'Risk Analysis - Detailed')

@section('content')
<div class="risk-analysis py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-microscope"></i> Risk Analysis Details</h1>
                <small class="text-muted">In-depth analysis of all risk factors</small>
            </div>
            <a href="{{ route('admin.risk-assessment.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#users" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-users"></i> User Risk Profiles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#ips" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-network-wired"></i> IP Risk Assessment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#devices" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-mobile-alt"></i> Device Risk Assessment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#time" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-clock"></i> Time-Based Analysis
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- User Risk Profiles -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-users"></i> User Risk Profiles</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th style="width: 120px;">Risk Score</th>
                                    <th style="width: 100px;">Risk Level</th>
                                    <th style="width: 80px;">Alerts</th>
                                    <th style="width: 100px;">Anomalies</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user_risk_profiles as $profile)
                                    <tr class="align-middle">
                                        <td>
                                            <strong>{{ $profile['user']->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $profile['user']->email }}</small>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 24px;">
                                                <div class="progress-bar bg-{{ $profile['risk_score'] >= 80 ? 'danger' : ($profile['risk_score'] >= 60 ? 'warning' : 'success') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $profile['risk_score'] }}%"
                                                     title="{{ $profile['risk_score'] }}%">
                                                    {{ round($profile['risk_score'], 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $profile['risk_score'] >= 80 ? 'danger' : ($profile['risk_score'] >= 60 ? 'warning' : 'success') }}">
                                                {{ $profile['risk_level'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $profile['alerts_count'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $profile['anomalies_count'] }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.risk-assessment.user-detail', $profile['user']->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No user data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- IP Risk Assessment -->
            <div class="tab-pane fade" id="ips" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-network-wired"></i> IP Risk Assessment (Last 7 Days)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>IP Address</th>
                                    <th style="width: 120px;">Access Count</th>
                                    <th style="width: 120px;">Failed Attempts</th>
                                    <th style="width: 120px;">Risk Score</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ip_risk_assessment as $ip)
                                    <tr class="align-middle">
                                        <td>
                                            <strong class="text-monospace">{{ $ip['ip'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $ip['access_count'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $ip['failed_attempts'] }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 24px; width: 100px;">
                                                <div class="progress-bar bg-{{ $ip['risk_score'] >= 70 ? 'danger' : ($ip['risk_score'] >= 40 ? 'warning' : 'success') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min(100, $ip['risk_score']) }}%"
                                                     title="{{ round($ip['risk_score'], 1) }}%">
                                                    {{ round($ip['risk_score'], 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($ip['risk_score'] >= 70)
                                                <button class="btn btn-sm btn-danger" title="Blacklist this IP">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="fas fa-check"></i> OK
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No IP risk data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Device Risk Assessment -->
            <div class="tab-pane fade" id="devices" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-mobile-alt"></i> Device Risk Assessment (Last 7 Days)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Device Fingerprint</th>
                                    <th style="width: 120px;">Anomalies</th>
                                    <th style="width: 120px;">Risk Level</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($device_risk_assessment as $device)
                                    <tr class="align-middle">
                                        <td>
                                            <code style="font-size: 11px;">{{ substr($device['device'], 0, 40) }}...</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $device['anomalies'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $device['risk_level'] === 'high' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($device['risk_level']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning" title="Monitor device">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No device risk data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Time-Based Analysis -->
            <div class="tab-pane fade" id="time" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> 24-Hour Risk Analysis</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="timeBasedRiskChart" height="60"></canvas>
                        
                        <div class="mt-4">
                            <div class="row">
                                @php
                                    $peakHours = collect($time_based_risk)
                                        ->sortByDesc('risk_level')
                                        ->take(3);
                                @endphp
                                
                                @foreach($peakHours as $hour)
                                    <div class="col-md-4">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-0">Peak Risk Time</h6>
                                                <h4 class="text-danger mb-0">{{ $hour['hour'] }}</h4>
                                                <small class="text-muted">{{ $hour['risk_level'] }} alerts</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Time-based Risk Chart
        const timeCtx = document.getElementById('timeBasedRiskChart');
        if (timeCtx) {
            const timeData = {!! json_encode($time_based_risk) !!};
            new Chart(timeCtx, {
                type: 'bar',
                data: {
                    labels: timeData.map(d => d.hour),
                    datasets: [{
                        label: 'Risk Level',
                        data: timeData.map(d => d.risk_level),
                        backgroundColor: timeData.map(d => 
                            d.risk_level > 15 ? '#dc3545' : 
                            (d.risk_level > 10 ? '#fd7e14' : 
                            (d.risk_level > 5 ? '#ffc107' : '#28a745'))
                        )
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
    .risk-analysis {
        background-color: #f5f7fa;
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

    .text-monospace {
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }
</style>
@endsection
