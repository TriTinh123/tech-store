@extends('layouts.app')

@section('title', 'Incident Response Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Incident Response Center</h1>
            <p class="text-muted">Manage and track security incidents</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Incident
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-1">{{ $total_incidents }}</h2>
                    <p class="text-muted mb-0">Total Incidents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-danger border-2">
                <div class="card-body">
                    <h2 class="text-danger mb-1">{{ $active_incidents }}</h2>
                    <p class="text-muted mb-0">Active Incidents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-warning border-2">
                <div class="card-body">
                    <h2 class="text-warning mb-1">{{ $critical_incidents }}</h2>
                    <p class="text-muted mb-0">Critical Incidents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-info mb-1">{{ $average_resolution_time }}h</h2>
                    <p class="text-muted mb-0">Avg Resolution Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Incidents</h5>
                    <a href="{{ route('admin.incidents.index') }}" class="btn btn-sm btn-link">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_incidents as $incident)
                            <tr>
                                <td>#{{ $incident->id }}</td>
                                <td>{{ $incident->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $incident->severity == 'critical' ? 'danger' : ($incident->severity == 'high' ? 'warning' : 'info') }}">
                                        {{ ucfirst($incident->severity) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $incident->status == 'closed' ? 'success' : ($incident->status == 'open' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($incident->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $incident->assignee?->name ?? 'Unassigned' }}
                                </td>
                                <td>{{ $incident->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('admin.incidents.show', $incident->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No incidents
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidents by Severity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Incidents by Severity</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-danger">{{ $incidents_by_severity['critical'] ?? 0 }}</h4>
                            <p class="text-muted">Critical</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $incidents_by_severity['high'] ?? 0 }}</h4>
                            <p class="text-muted">High</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">This Week Statistics</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>New Incidents:</strong> {{ $incidents_this_week }}
                    </p>
                    <p class="mb-0">
                        <strong>Trend:</strong> 
                        <span class="badge bg-success">Stable</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
