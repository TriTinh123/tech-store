@extends('layouts.app')

@section('title', 'Product Analytics')

@section('content')
<div class="admin-reports-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-box"></i> Product Analytics</h1>
                <small class="text-muted">Inventory and product performance metrics</small>
            </div>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Period Selector -->
        <div class="mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Period</label>
                            <select class="form-select" name="period" onchange="this.form.submit()">
                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Key Product Metrics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Total Products</small>
                        <h3 class="h2 mt-2 mb-0">{{ $totalProducts }}</h3>
                        <small class="text-info"><i class="fas fa-database"></i> In catalog</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Active Products</small>
                        <h3 class="h2 mt-2 mb-0">{{ $activeProducts }}</h3>
                        <small class="text-success"><i class="fas fa-check"></i> Available for sale</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Low Stock</small>
                        <h3 class="h2 mt-2 mb-0">{{ count($lowStockProducts) }}</h3>
                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Needs reorder</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Inventory Value</small>
                        <h3 class="h2 mt-2 mb-0">${{ number_format($inventory['totalValue'], 2) }}</h3>
                        <small class="text-primary"><i class="fas fa-box"></i> Total stock</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Management -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td><code>{{ $product->sku }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock > 5 ? 'warning' : 'danger' }}">
                                            {{ $product->stock }} units
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Reorder</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No low stock items</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Inventory Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="inventory-stat mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Total Items in Stock</span>
                                <strong>{{ number_format($inventory['totalItems']) }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 80%;"></div>
                            </div>
                        </div>

                        <div class="inventory-stat mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Out of Stock</span>
                                <strong class="text-danger">{{ $inventory['outOfStock'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: {{ ($inventory['outOfStock'] / $totalProducts) * 100 }}%;"></div>
                            </div>
                        </div>

                        <div class="inventory-stat">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Low Stock Items</span>
                                <strong>{{ $inventory['lowStockCount'] }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($inventory['lowStockCount'] / $totalProducts) * 100 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Products -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Top Performing Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Sales</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $product->orderItems()->count() }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($product->orderItems()->sum('total_price'), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-turtle"></i> Slow Moving Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Sales</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slowMovers as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-warning">
                                            {{ $product->orderItems()->count() }} sales
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">Review</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Great! All products are selling well</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Visibility -->
        <div class="row mb-5">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Product Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-4">
                                <div class="visibility-card">
                                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                    <h4>{{ $productVisibility['active'] }}</h4>
                                    <small class="text-muted">Active Products</small>
                                    <small class="d-block text-muted mt-2">{{ round(($productVisibility['active'] / $totalProducts) * 100, 1) }}% of catalog</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="visibility-card">
                                    <i class="fas fa-eye-slash fa-2x text-secondary mb-3"></i>
                                    <h4>{{ $productVisibility['inactive'] }}</h4>
                                    <small class="text-muted">Inactive Products</small>
                                    <small class="d-block text-muted mt-2">{{ round(($productVisibility['inactive'] / $totalProducts) * 100, 1) }}% of catalog</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="visibility-card">
                                    <i class="fas fa-star fa-2x text-warning mb-3"></i>
                                    <h4>{{ $productVisibility['featured'] }}</h4>
                                    <small class="text-muted">Featured Products</small>
                                    <small class="d-block text-muted mt-2">{{ round(($productVisibility['featured'] / $totalProducts) * 100, 1) }}% of catalog</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="visibility-card">
                                    <i class="fas fa-layer-group fa-2x text-info mb-3"></i>
                                    <h4>{{ count($categoryAnalysis) }}</h4>
                                    <small class="text-muted">Categories</small>
                                    <small class="d-block text-muted mt-2">Product distribution</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Analysis -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Category Analysis</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Products</th>
                                    <th>Stock</th>
                                    <th>% of Inventory</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryAnalysis as $category)
                                <tr>
                                    <td><strong>{{ $category->category }}</strong></td>
                                    <td>{{ $category->products }}</td>
                                    <td>{{ $category->stock }} units</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ ($category->stock / $inventory['totalItems']) * 100 }}%;"></div>
                                            </div>
                                            <span class="ms-2 text-muted small">{{ round(($category->stock / $inventory['totalItems']) * 100, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.visibility-card {
    padding: 30px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.visibility-card:hover {
    transform: translateY(-4px);
}

.inventory-stat {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
}
</style>
@endsection
