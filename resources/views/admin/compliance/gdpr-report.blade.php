@extends('layouts.app')

@section('title', 'GDPR Compliance Report')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">GDPR Compliance Report</h1>
            <p class="text-muted">{{ $period }}</p>
        </div>
        <div class="col-md-4 text-end">
            <form method="GET" action="{{ route('admin.compliance.export-report') }}" class="d-inline">
                <input type="hidden" name="type" value="gdpr">
                <select name="format" class="form-select form-select-sm d-inline w-auto">
                    <option value="pdf">PDF</option>
                    <option value="csv">CSV</option>
                    <option value="json">JSON</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-download"></i> Export
                </button>
            </form>
            <a href="{{ route('admin.compliance.dashboard') }}" class="btn btn-sm btn-secondary">
                Back
            </a>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h4>{{ $data_subjects['total_users'] }}</h4>
                    <p class="text-muted">Total Data Subjects</p>
                </div>
                <div class="col-md-3">
                    <h4>{{ $data_subjects['new_users'] }}</h4>
                    <p class="text-muted">New Users This Period</p>
                </div>
                <div class="col-md-3">
                    <h4>{{ $security_incidents['total_incidents'] }}</h4>
                    <p class="text-muted">Security Incidents</p>
                </div>
                <div class="col-md-3">
                    <h4>{{ $security_incidents['resolved_incidents'] }}</h4>
                    <p class="text-muted">Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Processing -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Data Processing Activities</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Audit Logs:</strong> {{ $data_processing['audit_logs'] }} entries
                    </p>
                    <p class="mb-2">
                        <strong>Security Alerts:</strong> {{ $data_processing['security_alerts'] }}
                    </p>
                    <p class="mb-0">
                        <strong>Anomaly Detections:</strong> {{ $data_processing['anomaly_detections'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Data Retention</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Retention Period:</strong> {{ $data_retention['logs_retention_days'] }} days
                    </p>
                    <p class="mb-0">
                        <strong>Old Logs Deleted:</strong> {{ $data_retention['old_audit_logs_deleted'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Recommendations</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($recommendations as $rec)
                <li class="list-group-item">
                    <i class="fas fa-check-circle text-success"></i> {{ $rec }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="text-center text-muted mt-4">
        <small>Generated on {{ $generated_at }}</small>
    </div>
</div>
@endsection
