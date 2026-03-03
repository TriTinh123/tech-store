@extends('layouts.app')

@section('title', 'Anomaly Details')

@section('content')
<div class="anomaly-detail py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-shield-alt"></i> Anomaly #{{ $anomaly->id }}</h1>
                <small class="text-muted">Detailed anomaly information and context</small>
            </div>
            <div>
                <a href="{{ route('admin.anomalies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Details -->
            <div class="col-lg-8">
                <!-- Anomaly Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Anomaly Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Type</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary" style="font-size: 14px;">
                                        @switch($anomaly->anomaly_type)
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
                                                <i class="fas fa-exclamation"></i> Multiple Failed Attempts
                                            @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                        @endswitch
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Risk Level</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $anomaly->risk_level === 'critical' ? 'danger' : ($anomaly->risk_level === 'high' ? 'warning' : ($anomaly->risk_level === 'medium' ? 'warning' : 'success')) }}" style="font-size: 14px;">
                                        {{ ucfirst($anomaly->risk_level) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $anomaly->status === 'new' ? 'info' : ($anomaly->status === 'resolved' ? 'success' : ($anomaly->status === 'blocked' ? 'danger' : 'warning')) }}" style="font-size: 14px;">
                                        {{ ucfirst(str_replace('_', ' ', $anomaly->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Detected</h6>
                                <p class="mb-0">
                                    {{ $anomaly->created_at->format('M d, Y H:i:s') }}
                                    <br><small class="text-muted">({{ $anomaly->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $anomaly->description ?? 'N/A' }}</p>
                        </div>

                        @if($anomaly->admin_notes)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Admin Notes</h6>
                                <p class="mb-0 alert alert-info">{{ $anomaly->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-user"></i> User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Name</h6>
                                <p class="mb-0">{{ $anomaly->user?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Email</h6>
                                <p class="mb-0">
                                    <a href="{{ route('admin.users.show', $anomaly->user_id) }}">
                                        {{ $anomaly->user?->email ?? 'N/A' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">User ID</h6>
                                <p class="mb-0">{{ $anomaly->user_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Role</h6>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $anomaly->user?->role ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Network Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-network-wired"></i> Network Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">IP Address</h6>
                                <p class="mb-0">
                                    <code>{{ $anomaly->ip_address }}</code>
                                    @if($anomaly->is_whitelisted)
                                        <br><span class="badge bg-success">Whitelisted</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Device Fingerprint</h6>
                                <p class="mb-0">
                                    <small class="text-muted">{{ substr($anomaly->device_fingerprint ?? 'N/A', 0, 40) }}...</small>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">User Agent</h6>
                                <p class="mb-0">
                                    <small class="text-muted">{{ substr($anomaly->user_agent ?? 'N/A', 0, 60) }}...</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Country</h6>
                                <p class="mb-0">
                                    @if($anomaly->country)
                                        <i class="fas fa-map-pin"></i> {{ $anomaly->country }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">City</h6>
                                <p class="mb-0">{{ $anomaly->city ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Coordinates</h6>
                                <p class="mb-0">{{ $anomaly->latitude ?? 'N/A' }}, {{ $anomaly->longitude ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @if(!$anomaly->is_whitelisted && $anomaly->status !== 'resolved')
                            <div class="btn-group d-flex gap-2" role="group">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resolveModal">
                                    <i class="fas fa-check-circle"></i> Whitelist & Resolve
                                </button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#investigateModal">
                                    <i class="fas fa-searchengin"></i> Mark for Investigation
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#blockModal">
                                    <i class="fas fa-ban"></i> Block IP
                                </button>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> This anomaly has been resolved.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Total User Anomalies</small>
                            <h4>{{ $userAnomalies->count() }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Same IP Anomalies</small>
                            <h4>{{ $similarAnomalies->count() }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Handled By</small>
                            <p class="mb-0">{{ $anomaly->handler?->name ?? 'Not yet handled' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Related User Anomalies -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-history"></i> User's Recent Anomalies</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($userAnomalies->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($userAnomalies as $related)
                                    <a href="{{ route('admin.anomalies.show', $related->id) }}" 
                                       class="list-group-item list-group-item-action {{ $related->id === $anomaly->id ? 'active' : '' }}">
                                        <small>
                                            <span class="badge bg-primary">{{ str_replace('_', ' ', $related->anomaly_type) }}</span>
                                            <span class="badge bg-{{ $related->risk_level === 'critical' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($related->risk_level) }}
                                            </span>
                                            <br>{{ $related->created_at->format('M d H:i') }}
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="mb-0 p-3 text-muted">No other anomalies for this user</p>
                        @endif
                    </div>
                </div>

                <!-- Same IP Anomalies -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-link"></i> Same IP Address</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($similarAnomalies->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($similarAnomalies as $similar)
                                    <a href="{{ route('admin.anomalies.show', $similar->id) }}" 
                                       class="list-group-item list-group-item-action">
                                        <small>
                                            <strong>{{ $similar->user?->email }}</strong>
                                            <br>
                                            <span class="badge bg-primary">{{ str_replace('_', ' ', $similar->anomaly_type) }}</span>
                                            <br>{{ $similar->created_at->format('M d H:i') }}
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="mb-0 p-3 text-muted">No other anomalies from this IP</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.anomalies.resolve', $anomaly->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Whitelist & Resolve</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Why is this trusted?" required></textarea>
                    </div>
                    <input type="hidden" name="action" value="whitelist">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Whitelist & Resolve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Investigate Modal -->
<div class="modal fade" id="investigateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.anomalies.resolve', $anomaly->id) }}">
                @csrf
                <input type="hidden" name="action" value="investigate">
                <div class="modal-header">
                    <h5 class="modal-title">Mark for Investigation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This anomaly will be marked for further investigation.</p>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Investigation notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Mark for Investigation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block Modal -->
<div class="modal fade" id="blockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.anomalies.block-ip', $anomaly->ip_address) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Block IP Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Block IP: <code>{{ $anomaly->ip_address }}</code></p>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Reason for blocking..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (hours)</label>
                        <input type="number" name="duration_hours" class="form-control" value="24" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .list-group-item-action {
        text-decoration: none;
        color: inherit;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }

    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
@endsection
