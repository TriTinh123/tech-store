@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h1 style="color: #667eea; font-weight: bold;">
        <i class="fas fa-users"></i> Quản Lý Người Dùng
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="color: #667eea; font-weight: bold;">Tên</th>
                                <th style="color: #667eea; font-weight: bold;">Email</th>
                                <th style="color: #667eea; font-weight: bold;">Điện Thoại</th>
                                <th style="color: #667eea; font-weight: bold;">Ngày Tạo</th>
                                <th style="color: #667eea; font-weight: bold;">Trạng Thái</th>
                                <th style="color: #667eea; font-weight: bold;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($user->is_blocked)
                                            <span class="badge bg-danger">🔒 Đã Khóa</span>
                                        @else
                                            <span class="badge bg-success">✓ Hoạt Động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm @if($user->is_blocked) btn-success @else btn-warning @endif">
                                                @if($user->is_blocked)
                                                    <i class="fas fa-unlock"></i> Mở
                                                @else
                                                    <i class="fas fa-lock"></i> Khóa
                                                @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa người dùng này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="alert alert-info">Chưa có người dùng nào</div>
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
