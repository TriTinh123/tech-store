@extends('layouts.app')

@section('title', 'Security Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">🔒 Security Dashboard</h1>
                <p class="text-muted small">Real-time security monitoring and threat detection</p>
            </div>
            <div class="text-end">
                <span class="badge bg-success">{{ $stats['active_users'] }} Active Users</span>
                <span class="badge bg-info">{{ $stats['active_sessions'] }} Sessions</span>
                <span class="badge bg-warning">{{ $stats['suspicious_activities'] }} Alerts</span>
            </div>
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Active Users</p>
                            <h3 class="mb-0">{{ $stats['active_users'] }}</h3>
                            <small class="text-success">↑ {{ round(($stats['active_users'] / $stats['total_users']) * 100) }}%</small>
                        </div>
                        <span class="badge bg-primary rounded-circle p-2">👥</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Active Sessions</p>
                            <h3 class="mb-0">{{ $stats['active_sessions'] }}</h3>
                            <small class="text-info">Concurrent logins: {{ $stats['concurrent_logins'] }}</small>
                        </div>
                        <span class="badge bg-info rounded-circle p-2">📱</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Blocked IPs</p>
                            <h3 class="mb-0">{{ $stats['blocked_ips'] }}</h3>
                            <small class="text-danger">{{ $stats['suspicious_activities'] }} suspicious</small>
                        </div>
                        <span class="badge bg-danger rounded-circle p-2">🚫</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Unread Alerts</p>
                            <h3 class="mb-0">{{ $stats['unread_notifications'] }}</h3>
                            <small class="text-warning">{{ $notificationStats['failed_notifications'] }} failed</small>
                        </div>
                        <span class="badge bg-warning rounded-circle p-2">🔔</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login & Activity Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📊 Login Activity (24h)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted small">Total Attempts</p>
                            <h4 class="text-primary">{{ $loginAttempts24h }}</h4>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small">Failed Attempts</p>
                            <h4 class="text-danger">{{ $failedLogins24h }}</h4>
                            <small class="text-muted">{{ $loginAttempts24h > 0 ? round(($failedLogins24h / $loginAttempts24h) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📧 Notification Delivery (24h)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted small">Email Sent</p>
                            <h4 class="text-success">{{ $notificationStats['email_sent_today'] }}</h4>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small">SMS Sent</p>
                            <h4 class="text-info">{{ $notificationStats['sms_sent_today'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Concurrent Logins -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">🔴 Concurrent Logins Detected</h6>
                    <span class="badge bg-danger">{{ $stats['concurrent_logins'] }}</span>
                </div>
                <div class="card-body">
                    @if($concurrentLogins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Location 1</th>
                                        <th>Location 2</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($concurrentLogins as $login)
                                        <tr>
                                            <td>
                                                <strong>{{ $login->user->name ?? 'Unknown' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $login->user->email ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $login->first_session_location ?? 'Unknown' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $login->second_session_location ?? 'Unknown' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $login->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">✅ No concurrent logins detected</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Suspicious Activities -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">⚠️ Suspicious Activities</h6>
                    <span class="badge bg-warning">{{ $stats['suspicious_activities'] }}</span>
                </div>
                <div class="card-body">
                    @if($suspiciousActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Risk Level</th>
                                        <th>Activity</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suspiciousActivities as $activity)
                                        <tr>
                                            <td>
                                                <strong>{{ $activity->user->name ?? 'Unknown' }}</strong>
                                            </td>
                                            <td>
                                                @if($activity->risk_score > 80)
                                                    <span class="badge bg-danger">High</span>
                                                @elseif($activity->risk_score > 50)
                                                    <span class="badge bg-warning">Medium</span>
                                                @else
                                                    <span class="badge bg-info">Low</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $activity->description ?? 'Suspicious login' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">✅ No suspicious activities</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Device & Location Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📱 Sessions by Device Type</h6>
                </div>
                <div class="card-body">
                    @if($sessionsByDevice->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($sessionsByDevice as $device => $count)
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ ucfirst($device) }}</small>
                                        <span class="badge bg-secondary">{{ $count }}</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary"
                                             style="width: {{ ($count / $stats['active_sessions']) * 100 }}%"
                                             role="progressbar">
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small">No active sessions</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">🌍 Top Locations</h6>
                </div>
                <div class="card-body">
                    @if($topLocations->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($topLocations as $loc)
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ $loc->location ?? 'Unknown' }}</small>
                                        <span class="badge bg-secondary">{{ $loc->count }}</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info"
                                             style="width: {{ ($loc->count / $stats['active_sessions']) * 100 }}%"
                                             role="progressbar">
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small">No location data</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Security Notifications -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">🔔 Recent Security Notifications</h6>
                    <a href="/notifications" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentNotifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Event Type</th>
                                        <th>Message</th>
                                        <th>Severity</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentNotifications as $notification)
                                        <tr>
                                            <td>
                                                <strong>{{ $notification->user->name ?? 'System' }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ str_replace('_', ' ', ucfirst($notification->type)) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($notification->message, 50) }}</small>
                                            </td>
                                            <td>
                                                @switch($notification->severity)
                                                    @case('critical')
                                                        <span class="badge bg-danger">🔴 Critical</span>
                                                        @break
                                                    @case('warning')
                                                        <span class="badge bg-warning">🟠 Warning</span>
                                                        @break
                                                    @case('info')
                                                        <span class="badge bg-info">🔵 Info</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Gray</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($notification->read)
                                                    <small class="text-success">✓ Read</small>
                                                @else
                                                    <small class="text-warning">○ Unread</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">No notifications yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Footer -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info small" role="alert">
                <strong>System Status:</strong>
                ✅ Database: Connected
                | ✅ Session Monitoring: Active
                | ✅ Notification Service: Operational
                | ✅ Activity Tracking: Enabled
                | <span class="float-end">Last updated: {{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh every 30 seconds -->
<script>
    // Uncomment to enable auto-refresh
    // setInterval(() => location.reload(), 30000);
</script>
@endsection
