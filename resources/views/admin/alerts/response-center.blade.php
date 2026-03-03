@extends('layouts.app')

@section('title', 'Alert Response Center')

@section('content')
<div class="alert-response-center py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-exclamation-triangle"></i> Response Center</h1>
                <small class="text-muted">Manage pending alerts that require immediate response</small>
            </div>
            <div>
                <span class="badge bg-danger" style="font-size: 14px;">
                    {{ $pendingAlerts->count() }} Pending
                </span>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Response Templates -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-cube"></i> Response Templates</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($responseTemplates as $template)
                                <button type="button" 
                                        class="list-group-item list-group-item-action text-start template-btn"
                                        data-template-id="{{ $template['id'] }}"
                                        data-template-action="{{ $template['action'] }}"
                                        data-template-description="{{ $template['description'] }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <strong style="font-size: 13px;">{{ $template['name'] }}</strong>
                                        <span class="badge bg-primary">{{ ucfirst($template['action']) }}</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">{{ $template['description'] }}</small>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Alerts Queue -->
            <div class="col-lg-9">
                <!-- Summary Statistics -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card border-0 bg-warning bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-0">Critical Pending</h6>
                                <h3 class="text-warning mb-0">{{ $criticalCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-danger bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-0">High Severity</h6>
                                <h3 class="text-danger mb-0">{{ $highCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-info bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-0">Medium Severity</h6>
                                <h3 class="text-info mb-0">{{ $mediumCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-success bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-0">Low Severity</h6>
                                <h3 class="text-success mb-0">{{ $lowCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Pending Alerts</h5>
                        <small class="text-muted">Total: {{ $pendingAlerts->count() }}</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Priority</th>
                                    <th>Alert</th>
                                    <th style="width: 120px;">Type</th>
                                    <th style="width: 100px;">User</th>
                                    <th style="width: 100px;">Created</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingAlerts as $alert)
                                    <tr class="align-middle">
                                        <td>
                                            <i class="fas fa-arrow-up text-{{ 
                                                $alert->severity === 'critical' ? 'danger' : 
                                                ($alert->severity === 'high' ? 'warning' : 'info')
                                            }}"></i>
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($alert->title, 50) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($alert->description, 80) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>{{ $alert->user?->name ?? 'Unknown' }}</strong>
                                                <br>
                                                <span class="text-muted">{{ $alert->user?->email ?? '' }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $alert->created_at->format('M d H:i') }}
                                                <br>
                                                <span class="text-danger">{{ $alert->created_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.alerts.show', $alert->id) }}" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-success quick-respond-btn"
                                                        data-alert-id="{{ $alert->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#quickResponseModal">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle"></i> <strong>Excellent!</strong> No pending alerts requiring response
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Metrics -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-stopwatch"></i> Response Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Avg Response Time</span>
                                <strong>{{ $avgResponseTime ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Response Rate</span>
                                <strong>{{ $responseRate ?? '0' }}%</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $responseRate ?? 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Resolution Rate</span>
                                <strong>{{ $resolutionRate ?? '0' }}%</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $resolutionRate ?? 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Escalation Rate</span>
                                <strong>{{ $escalationRate ?? '0' }}%</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $escalationRate ?? 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Recent Responses</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentResponses as $response)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <strong style="font-size: 13px;">{{ $response->alert?->title ?? 'Unknown' }}</strong>
                                        <span class="badge bg-primary">{{ ucfirst($response->action) }}</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $response->respondedBy?->name ?? 'Unknown' }} | 
                                        <i class="fas fa-clock"></i> {{ $response->timestamp->diffForHumans() }}
                                    </small>
                                </div>
                            @empty
                                <div class="alert alert-info m-3 mb-0">
                                    <small><i class="fas fa-info-circle"></i> No recent responses</small>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Response Modal -->
<div class="modal fade" id="quickResponseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.alerts.respond', 0) }}" id="quickResponseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-reply"></i> Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select" required>
                            <option value="">Select action...</option>
                            <option value="acknowledge">Acknowledge</option>
                            <option value="investigate">Investigate</option>
                            <option value="escalate">Escalate</option>
                            <option value="resolve">Resolve</option>
                            <option value="dismiss">Dismiss</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Response Notes</label>
                        <textarea name="response_notes" class="form-control" rows="3" placeholder="Add response notes..."></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUser" value="1">
                        <label class="form-check-label" for="notifyUser">
                            Notify user about this response
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Template button clicks - pre-fill the form
        document.querySelectorAll('.template-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.templateAction;
                const description = this.dataset.templateDescription;
                
                // Pre-fill action dropdown
                const actionSelect = document.querySelector('select[name="action"]');
                actionSelect.value = action;
                
                // Pre-fill notes textarea
                const notesTextarea = document.querySelector('textarea[name="response_notes"]');
                notesTextarea.value = description;
                
                // You could also trigger the modal here if desired
                // new bootstrap.Modal(document.getElementById('quickResponseModal')).show();
            });
        });

        // Quick respond button - set alert ID in form
        document.querySelectorAll('.quick-respond-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const alertId = this.dataset.alertId;
                const form = document.getElementById('quickResponseForm');
                form.action = form.action.replace('/0', '/' + alertId);
            });
        });
    });
</script>

<style>
    .template-btn {
        padding: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .template-btn:hover {
        background-color: #f8f9fa;
    }

    .template-btn.active {
        background-color: #e7f3ff;
        border-left: 3px solid #007bff;
    }

    .alert-response-center {
        background-color: #f5f7fa;
    }
</style>
@endsection
