@extends('layouts.app')

@section('title', 'Audit Log Details')

@section('content')
<div class="audit-log-detail py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-file-alt"></i> Audit Log #{{ $log->id }}</h1>
                <small class="text-muted">Detailed log entry information</small>
            </div>
            <div>
                <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Logs
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Log Details -->
            <div class="col-lg-8 mb-4">
                <!-- Event Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Event Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Action</h6>
                                <p class="mb-0">
                                    <span class="badge bg-light text-dark" style="font-size: 14px;">{{ $log->action }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}" style="font-size: 14px;">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $log->description }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Timestamp</h6>
                                <p class="mb-0">
                                    <strong>{{ $log->created_at->format('Y-m-d H:i:s') }}</strong><br>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Log ID</h6>
                                <p class="mb-0">
                                    <code>{{ $log->id }}</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-user"></i> User Information</h5>
                    </div>
                    <div class="card-body">
                        @if($log->user)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">User Name</h6>
                                <p class="mb-0">
                                    <strong>{{ $log->user->name }}</strong><br>
                                    <a href="{{ route('audit.user-activity', $log->user->id) }}" class="text-decoration-none small">View user activity</a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Email</h6>
                                <p class="mb-0">{{ $log->user->email }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">User ID</h6>
                                <p class="mb-0"><code>{{ $log->user->id }}</code></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Role</h6>
                                <p class="mb-0">
                                    <span class="badge bg-info">{{ $log->user->role ?? 'user' }}</span>
                                </p>
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">User information not available</p>
                        @endif
                    </div>
                </div>

                <!-- Network Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-network-wired"></i> Network Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">IP Address</h6>
                                <p class="mb-0">
                                    <code>{{ $log->ip_address }}</code>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Country</h6>
                                <p class="mb-0">{{ $log->country ?? 'Unknown' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Method</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary">{{ $log->method ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Route</h6>
                                <p class="mb-0"><code>{{ $log->route ?? 'N/A' }}</code></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Security Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Suspicious Activity</h6>
                                <p class="mb-0">
                                    @if($log->is_suspicious)
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Yes</span>
                                    @else
                                        <span class="badge bg-success">No</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Threat Type</h6>
                                <p class="mb-0">
                                    @if($log->threat_type)
                                        <span class="badge bg-{{ $log->threat_type === 'critical' ? 'danger' : ($log->threat_type === 'high' ? 'warning' : 'info') }}">
                                            {{ ucfirst($log->threat_type) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Risk Assessment</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-{{ $log->is_suspicious ? 'danger' : 'success' }}" style="width: {{ $log->is_suspicious ? '75' : '25' }}%;">
                                    {{ $log->is_suspicious ? 'High Risk' : 'Low Risk' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Data -->
                @if($additionalData)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-database"></i> Additional Data</h5>
                    </div>
                    <div class="card-body">
                        <pre class="mb-0">{{ json_encode($additionalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted">Total User Actions</small>
                            <h4 class="mt-2">
                                @if($log->user)
                                    {{ \App\Models\ActivityLog::where('user_id', $log->user->id)->count() }}
                                @else
                                    N/A
                                @endif
                            </h4>
                        </div>

                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted">Same Day Logs</small>
                            <h4 class="mt-2">{{ $relatedLogs->count() }}</h4>
                        </div>

                        <div>
                            <small class="text-muted">Similar Actions</small>
                            <h4 class="mt-2">{{ $timeline->count() }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Related Activity -->
                @if($relatedLogs->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-link"></i> Related Activity (Same Day)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($relatedLogs as $related)
                            <a href="{{ route('audit.show', $related->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $related->action }}</h6>
                                        <small class="text-muted">{{ $related->created_at->format('H:i:s') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $related->status === 'success' ? 'success' : 'danger' }}">
                                        {{ ucfirst($related->status) }}
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Timeline -->
                @if($timeline->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Action Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($timeline as $item)
                            <div class="timeline-item mb-3 pb-3 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-dot me-3">
                                        <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1">
                                            <a href="{{ route('audit.show', $item->id) }}" class="text-decoration-none">
                                                {{ $item->action }}
                                            </a>
                                        </p>
                                        <small class="text-muted">{{ $item->created_at->format('Y-m-d H:i:s') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.audit-log-detail {
    background-color: #f8f9fa;
}

.timeline-item {
    padding-left: 15px;
    border-left: 2px solid #e3e6f0;
}

.timeline-dot {
    margin-left: -11px;
}

pre {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    max-height: 300px;
    overflow: auto;
    font-size: 12px;
}
</style>
@endsection
