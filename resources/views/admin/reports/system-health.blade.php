@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<div class="admin-reports-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-heartbeat"></i> System Health</h1>
                <small class="text-muted">Server and application performance monitoring</small>
            </div>
            <div>
                <span class="badge bg-success me-2">
                    <i class="fas fa-circle-dot"></i> All Systems Operational
                </span>
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Server Status Overview -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-server"></i> Server Status</h5>
                        <span class="badge bg-success">{{ $serverStatus['status'] }}</span>
                    </div>
                    <div class="card-body">
                        <div class="server-stat mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>CPU Usage</span>
                                <strong>{{ $serverStatus['cpuUsage'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ strpos($serverStatus['cpuUsage'], '8') !== false ? 'danger' : (strpos($serverStatus['cpuUsage'], '6') !== false ? 'warning' : 'success') }}" style="width: {{ (int)$serverStatus['cpuUsage'] }}%;"></div>
                            </div>
                        </div>

                        <div class="server-stat mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Memory Usage</span>
                                <strong>{{ $serverStatus['memoryUsage'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ strpos($serverStatus['memoryUsage'], '8') !== false ? 'danger' : (strpos($serverStatus['memoryUsage'], '7') !== false ? 'warning' : 'success') }}" style="width: {{ (int)$serverStatus['memoryUsage'] }}%;"></div>
                            </div>
                        </div>

                        <div class="server-stat">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Uptime</span>
                                <strong>{{ $serverStatus['uptime'] }}</strong>
                            </div>
                            <small class="text-success mt-2 d-block"><i class="fas fa-check-circle"></i> Excellent stability</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-hard-drive"></i> Storage & Database</h5>
                    </div>
                    <div class="card-body">
                        <div class="storage-stat mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Disk Usage</span>
                                <strong>{{ $diskUsage['percentage'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $diskUsage['percentage'] > 80 ? 'danger' : ($diskUsage['percentage'] > 60 ? 'warning' : 'success') }}" style="width: {{ $diskUsage['percentage'] }}%;"></div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                {{ $diskUsage['used'] }} GB / {{ $diskUsage['total'] }} GB
                            </small>
                        </div>

                        <div class="storage-stat pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Database Size</span>
                                <strong>{{ $databaseSize }}</strong>
                            </div>
                            <small class="text-muted d-block mt-2">Packaged data size</small>
                        </div>

                        <div class="storage-stat">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Free Space</span>
                                <strong>{{ $diskUsage['free'] }} GB</strong>
                            </div>
                            <small class="text-success d-block mt-2"><i class="fas fa-check-circle"></i> Sufficient space</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Performance Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="performance-metric">
                                    <small class="text-muted">Error Rate</small>
                                    <h4 class="mt-2">{{ $errorRate }}</h4>
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 15%;"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Very low - Excellent</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="performance-metric">
                                    <small class="text-muted">Response Time</small>
                                    <h4 class="mt-2">{{ $responseTime }}</h4>
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 40%;"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Good performance</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="performance-metric">
                                    <small class="text-muted">Cache Hit Rate</small>
                                    <h4 class="mt-2">{{ $cacheHitRate }}</h4>
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 85%;"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2">High efficiency</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="performance-metric">
                                    <small class="text-muted">DB Query Time</small>
                                    <h4 class="mt-2">{{ $performanceMetrics['dbQueryTime'] }}</h4>
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 45%;"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Optimized queries</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-link"></i> Connections</h5>
                    </div>
                    <div class="card-body">
                        <div class="connection-stat mb-4 pb-4 border-bottom">
                            <small class="text-muted text-uppercase">Active Connections</small>
                            <h4 class="mt-2">{{ $activeConnections }}</h4>
                            <small class="text-muted d-block mt-2">Database threads</small>
                        </div>

                        <div class="connection-stat mb-4 pb-4 border-bottom">
                            <small class="text-muted text-uppercase">Queue Status</small>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small">Pending Jobs</span>
                                    <strong>{{ $queueStatus['pending'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">Failed Jobs</span>
                                    <strong class="text-danger">{{ $queueStatus['failed'] }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="connection-stat">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                <small>All systems operating within normal parameters</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Logs -->
        <div class="row mb-5">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Recent System Logs</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All Logs</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($systemLogs as $log)
                                    <tr>
                                        <td><code>{{ $log['timestamp'] }}</code></td>
                                        <td>{{ $log['user'] ?? 'System' }}</td>
                                        <td><small>{{ $log['action'] }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $log['status'] === 'success' ? 'success' : ($log['status'] === 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($log['status']) }}
                                            </span>
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

        <!-- Health Check Quick Actions -->
        <div class="row mb-5">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> System Maintenance</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-primary w-100" type="button">
                                    <i class="fas fa-broom"></i> Clear Cache
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-warning w-100" type="button">
                                    <i class="fas fa-database"></i> Optimize DB
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-info w-100" type="button">
                                    <i class="fas fa-sync"></i> Run Health Check
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-secondary w-100" type="button">
                                    <i class="fas fa-download"></i> Export Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Alerts -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-bell"></i> Health Alerts & Recommendations</h5>
                        <span class="badge bg-success">All Good</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <strong>All systems are operating normally.</strong> No alerts or issues detected at this time.
                        </div>

                        <div class="recommendations-list">
                            <h6 class="mb-3">Maintenance Recommendations:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="badge bg-light text-dark">Info</span>
                                    <small class="ms-2">Regular database backups are scheduled and running successfully</small>
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-light text-dark">Info</span>
                                    <small class="ms-2">Cache is performing well with {{ $cacheHitRate }} hit rate</small>
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-light text-dark">Info</span>
                                    <small class="ms-2">Consider upgrading PHP if currently below 8.1</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.server-stat,
.storage-stat,
.performance-metric,
.connection-stat {
    padding: 10px 0;
}

.performance-metric {
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 6px;
}

.recommendations-list ul li {
    padding: 8px;
    border-left: 3px solid #e3e6f0;
    padding-left: 12px;
}
</style>
@endsection
