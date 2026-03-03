@extends('layouts.app')

@section('title', 'Compliance Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Compliance Management</h1>
            <p class="text-muted">Monitor regulatory compliance status</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.compliance.gdpr') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Generate Report
            </a>
        </div>
    </div>

    <!-- Compliance Score -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="text-primary mb-1">{{ $compliance_score }}%</h2>
                    <p class="text-muted mb-0">Overall Compliance Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="text-info mb-1">{{ $total_users }}</h2>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="text-warning mb-1">{{ $security_alerts_this_month }}</h2>
                    <p class="text-muted mb-0">Alerts This Month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="text-success mb-1">{{ $anomalies_resolved }}</h2>
                    <p class="text-muted mb-0">Anomalies Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Frameworks -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shield-alt text-primary"></i> GDPR
                    </h5>
                    <p class="text-muted small">General Data Protection Regulation</p>
                    <hr>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-success">Compliant</span>
                    </p>
                    <p class="mb-3">
                        <strong>Last Audit:</strong> Jan 15, 2026
                    </p>
                    <a href="{{ route('admin.compliance.gdpr') }}" class="btn btn-sm btn-primary w-100">
                        View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-credit-card text-success"></i> PCI DSS
                    </h5>
                    <p class="text-muted small">Payment Card Industry Data Security</p>
                    <hr>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-success">Compliant</span>
                    </p>
                    <p class="mb-3">
                        <strong>Last Audit:</strong> Dec 28, 2025
                    </p>
                    <a href="{{ route('admin.compliance.pcidss') }}" class="btn btn-sm btn-primary w-100">
                        View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-heartbeat text-danger"></i> HIPAA
                    </h5>
                    <p class="text-muted small">Health Insurance Portability  Account</p>
                    <hr>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-success">Compliant</span>
                    </p>
                    <p class="mb-3">
                        <strong>Last Audit:</strong> Nov 10, 2025
                    </p>
                    <a href="{{ route('admin.compliance.hipaa') }}" class="btn btn-sm btn-primary w-100">
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Recent Audit Events</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Mar 03, 2026</td>
                                <td>Security audit completed</td>
                                <td><span class="badge bg-info">Audit</span></td>
                                <td><span class="badge bg-success">Passed</span></td>
                            </tr>
                            <tr>
                                <td>Mar 01, 2026</td>
                                <td>Data backup verification</td>
                                <td><span class="badge bg-secondary">Backup</span></td>
                                <td><span class="badge bg-success">Passed</span></td>
                            </tr>
                            <tr>
                                <td>Feb 28, 2026</td>
                                <td>Encryption key rotation</td>
                                <td><span class="badge bg-warning">Security</span></td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
