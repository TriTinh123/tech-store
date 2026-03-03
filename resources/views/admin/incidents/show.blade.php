@extends('layouts.app')

@section('title', 'Incident #' . $incident->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ $incident->title }}</h1>
            <p class="text-muted">#{{ $incident->id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.incidents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6>Severity</h6>
                    <span class="badge bg-{{ $incident->severity == 'critical' ? 'danger' : 'warning' }}">
                        {{ ucfirst($incident->severity) }}
                    </span>

                    <hr>
                    <h6>Status</h6>
                    <span class="badge bg-{{ $incident->status == 'closed' ? 'success' : 'danger' }}">
                        {{ ucfirst($incident->status) }}
                    </span>

                    <hr>
                    <h6>Assigned To</h6>
                    <p class="mb-0">{{ $incident->assignee?->name ?? 'Unassigned' }}</p>

                    <hr>
                    <h6>Created</h6>
                    <p class="mb-0">{{ $incident->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Description -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    {{ $incident->description }}
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($timeline as $event)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker me-3">
                                    <div class="bg-primary rounded-circle" style="width: 12px; height: 12px;"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $event['action'] }}</h6>
                                    <small class="text-muted">{{ $event['timestamp']->diffForHumans() }}</small>
                                    <p class="mb-0 small mt-1">{{ $event['details'] }}</p>
                                    <small class="text-muted">by {{ $event['by'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Affected Users -->
            @if($incident->affected_users)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Affected Users ({{ count($incident->affected_users) }})</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($incident->affected_users as $userId)
                        <li class="list-group-item">
                            User ID: {{ $userId }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
