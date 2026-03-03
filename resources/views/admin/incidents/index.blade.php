@extends('layouts.app')

@section('title', 'All Incidents')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">All Incidents</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Incident
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="severity" class="form-select" onchange="this.form.submit()">
                        <option value="">All Severity</option>
                        @foreach($severities as $severity)
                        <option value="{{ $severity }}" {{ request('severity') == $severity ? 'selected' : '' }}>
                            {{ ucfirst($severity) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="assignee" class="form-select" onchange="this.form.submit()">
                        <option value="">All Assignees</option>
                        @foreach($team_members as $member)
                        <option value="{{ $member->id }}" {{ request('assignee') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                    <tr>
                        <td>#{{ $incident->id }}</td>
                        <td>{{ $incident->title }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }}</td>
                        <td>
                            <span class="badge bg-{{ $incident->severity == 'critical' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($incident->severity) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $incident->status == 'closed' ? 'success' : ($incident->status == 'open' ? 'danger' : (in_array($incident->status, ['investigating', 'contained']) ? 'warning' : 'secondary')) }}">
                                {{ ucfirst($incident->status) }}
                            </span>
                        </td>
                        <td>
                            {{ $incident->assignee?->name ?? '-' }}
                        </td>
                        <td>{{ $incident->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.incidents.show', $incident->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No incidents found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $incidents->links() }}
    </div>
</div>
@endsection
