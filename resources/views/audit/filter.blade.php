@extends('layouts.app')

@section('title', 'Advanced Audit Log Filter')

@section('content')
<div class="audit-filter py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-sliders-h"></i> Advanced Filter</h1>
                <small class="text-muted">Build complex queries to find specific logs</small>
            </div>
            <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>

        <div class="row">
            <!-- Filter Builder -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-funnel"></i> Build Filter</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" id="advancedFilterForm">
                            <div id="filtersContainer">
                                <div class="filter-group mb-3" data-filter-id="0">
                                    <div class="mb-2">
                                        <label class="form-label">Field</label>
                                        <select class="form-select filter-field" name="filters[0][field]">
                                            <option value="">Select Field...</option>
                                            @foreach($availableFields as $field => $label)
                                            <option value="{{ $field }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Operator</label>
                                        <select class="form-select filter-operator" name="filters[0][operator]">
                                            <option value="">Select Operator...</option>
                                            <option value="equals">Equals</option>
                                            <option value="not_equals">Not Equals</option>
                                            <option value="contains">Contains</option>
                                            <option value="not_contains">Does Not Contain</option>
                                            <option value="starts_with">Starts With</option>
                                            <option value="ends_with">Ends With</option>
                                            <option value="greater_than">Greater Than</option>
                                            <option value="less_than">Less Than</option>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Value</label>
                                        <input type="text" class="form-control filter-value" name="filters[0][value]" placeholder="Enter value...">
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-danger remove-filter d-none">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-3" id="addFilterBtn">
                                <i class="fas fa-plus"></i> Add Another Filter
                            </button>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Apply Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        @if($logs->total() > 0)
                        <div class="alert alert-success m-4 mb-4">
                            <i class="fas fa-check-circle"></i>
                            Found {{ $logs->total() }} results matching your criteria
                        </div>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>IP</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $log->user?->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $log->action }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $log->ip_address }}</code>
                                        </td>
                                        <td>
                                            <a href="{{ route('audit.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="m-4">
                            {{ $logs->links() }}
                        </div>
                        @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-search fa-3x mb-3 d-block"></i>
                            <p>No logs found matching your filter criteria</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.audit-filter {
    background-color: #f8f9fa;
}

.filter-group {
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
}

.filter-group.mb-3 {
    margin-bottom: 12px !important;
}
</style>

@push('scripts')
<script>
let filterCount = 1;

document.getElementById('addFilterBtn').addEventListener('click', function() {
    const container = document.getElementById('filtersContainer');
    const newFilter = document.createElement('div');
    newFilter.className = 'filter-group mb-3';
    newFilter.dataset.filterId = filterCount;
    newFilter.innerHTML = `
        <div class="mb-2">
            <label class="form-label">Field</label>
            <select class="form-select filter-field" name="filters[${filterCount}][field]">
                <option value="">Select Field...</option>
                @foreach($availableFields as $field => $label)
                <option value="{{ $field }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">Operator</label>
            <select class="form-select filter-operator" name="filters[${filterCount}][operator]">
                <option value="">Select Operator...</option>
                <option value="equals">Equals</option>
                <option value="not_equals">Not Equals</option>
                <option value="contains">Contains</option>
                <option value="not_contains">Does Not Contain</option>
                <option value="starts_with">Starts With</option>
                <option value="ends_with">Ends With</option>
                <option value="greater_than">Greater Than</option>
                <option value="less_than">Less Than</option>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">Value</label>
            <input type="text" class="form-control filter-value" name="filters[${filterCount}][value]" placeholder="Enter value...">
        </div>

        <button type="button" class="btn btn-sm btn-outline-danger remove-filter w-100">
            <i class="fas fa-trash"></i> Remove Filter
        </button>
    `;

    container.appendChild(newFilter);
    filterCount++;
    attachRemoveListener();
});

function attachRemoveListener() {
    document.querySelectorAll('.remove-filter').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.filter-group').remove();
        });
    });
}

attachRemoveListener();
</script>
@endpush
@endsection
