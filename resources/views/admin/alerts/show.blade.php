@extends('layouts.app')

@section('title', 'Alert Details')

@section('content')
<div class="alert-detail py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-bell"></i> Alert #{{ $alert->id }}</h1>
                <small class="text-muted">{{ $alert->title }}</small>
            </div>
            <div>
                <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Alert Details -->
            <div class="col-lg-8">
                <!-- Alert Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Alert Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Alert Type</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary" style="font-size: 13px;">
                                        {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Severity</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : ($alert->severity === 'medium' ? 'warning' : 'success')) }}" style="font-size: 13px;">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $alert->status === 'pending' ? 'warning' : ($alert->status === 'resolved' ? 'success' : 'info') }}" style="font-size: 13px;">
                                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Created</h6>
                                <p class="mb-0">
                                    {{ $alert->created_at->format('M d, Y H:i:s') }}
                                    <br><small class="text-muted">({{ $alert->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Title</h6>
                            <p class="mb-0"><strong>{{ $alert->title }}</strong></p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $alert->description ?? 'N/A' }}</p>
                        </div>

                        @if($alert->resolved_at)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i> Resolved on {{ $alert->resolved_at->format('M d, Y H:i') }}
                                by <strong>{{ $alert->resolver?->name ?? 'N/A' }}</strong>
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
                                <p class="mb-0">{{ $alert->user?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Email</h6>
                                <p class="mb-0">
                                    <a href="{{ route('admin.users.show', $alert->user_id) }}">
                                        {{ $alert->user?->email ?? 'N/A' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Role</h6>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $alert->user?->role ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Total Alerts</h6>
                                <p class="mb-0">{{ $userAlerts->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response Form -->
                @if($alert->status !== 'resolved' && $alert->status !== 'dismissed')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fas fa-reply"></i> Respond to Alert</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.alerts.respond', $alert->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Action</label>
                                    <select name="action" class="form-select" required>
                                        <option value="">Choose action...</option>
                                        <option value="acknowledge">Acknowledge</option>
                                        <option value="investigate">Investigate</option>
                                        <option value="escalate">Escalate</option>
                                        <option value="resolve">Resolve</option>
                                        <option value="dismiss">Dismiss</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="response_notes" class="form-control" rows="3" placeholder="Add response notes..."></textarea>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUser" value="1">
                                    <label class="form-check-label" for="notifyUser">
                                        Notify user about this response
                                    </label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check"></i> Submit Response
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        @if($alert->status === 'pending')
                            <form method="POST" action="{{ route('admin.alerts.acknowledge', $alert->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> Quick Acknowledge
                                </button>
                            </form>
                        @endif
                        
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#resolveModal">
                            <i class="fas fa-check-double"></i> Quick Resolve
                        </button>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#dismissModal">
                            <i class="fas fa-times-circle"></i> Quick Dismiss
                        </button>
                    </div>
                </div>

                <!-- Response Timeline -->
                @if($responses->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Response Timeline</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="timeline">
                                @foreach($responses as $response)
                                    <div class="timeline-item p-3 border-bottom">
                                        <div class="mb-2">
                                            <span class="badge bg-primary">{{ ucfirst($response->action) }}</span>
                                            <small class="text-muted">
                                                {{ $response->timestamp->format('M d, H:i') }}
                                            </small>
                                        </div>
                                        <p class="mb-1">
                                            <strong>{{ $response->respondedBy?->name ?? 'Unknown' }}</strong>
                                        </p>
                                        @if($response->notes)
                                            <p class="mb-0 text-muted" style="font-size: 13px;">
                                                {{ $response->notes }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i> No responses yet
                    </div>
                @endif

                <!-- Related Alerts -->
                @if($userAlerts->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fas fa-link"></i> Related User Alerts</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($userAlerts as $related)
                                    <a href="{{ route('admin.alerts.show', $related->id) }}" 
                                       class="list-group-item list-group-item-action {{ $related->id === $alert->id ? 'active' : '' }}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <strong>{{ $related->title }}</strong>
                                            <span class="badge bg-{{ $related->severity === 'critical' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($related->severity) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ $related->created_at->diffForHumans() }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.alerts.respond', $alert->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Resolve Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Resolution Notes</label>
                        <textarea name="response_notes" class="form-control" rows="3" placeholder="Explain the resolution..." required></textarea>
                    </div>
                    <input type="hidden" name="action" value="resolve">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Resolve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Dismiss Modal -->
<div class="modal fade" id="dismissModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.alerts.respond', $alert->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dismiss Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dismissal Reason</label>
                        <textarea name="response_notes" class="form-control" rows="3" placeholder="Why dismiss this alert?" required></textarea>
                    </div>
                    <input type="hidden" name="action" value="dismiss">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Dismiss</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
    }

    .timeline-item {
        position: relative;
        padding-left: 20px;
    }

    .timeline-item:before {
        content: "";
        position: absolute;
        left: 0;
        top: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }

    .timeline-item:not(:last-child):after {
        content: "";
        position: absolute;
        left: 4px;
        top: 20px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
</style>
@endsection
