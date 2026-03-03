@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 style="color: #667eea; font-weight: bold;">
                <i class="fas fa-list"></i> Quản Lý Danh Mục
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Thêm Danh Mục
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="color: #667eea; font-weight: bold;">Tên Danh Mục</th>
                                <th style="color: #667eea; font-weight: bold;">Slug</th>
                                <th style="color: #667eea; font-weight: bold;">Mô Tả</th>
                                <th style="color: #667eea; font-weight: bold;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td><code>{{ $category->slug }}</code></td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.delete', $category) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có danh mục. <a href="{{ route('admin.categories.create') }}">Tạo danh mục mới</a>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
        </a>
    </div>
</div>
@endsection
