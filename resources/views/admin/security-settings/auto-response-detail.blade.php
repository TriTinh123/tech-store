@extends('layouts.app')

@section('title', 'Chi tiết phản ứng tự động')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Chi tiết phản ứng tự động</h1>
                <p class="mt-2 text-gray-600">ID: {{ $response->id }}</p>
            </div>
            <a href="{{ route('admin.security-settings.auto-responses') }}" class="text-blue-600 hover:text-blue-900">
                ← Quay lại
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Response Overview -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Người dùng</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $response->user->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $response->user->email ?? '' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Trạng thái</p>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @if($response->status === 'executed') bg-green-100 text-green-800
                        @elseif($response->status === 'failed') bg-red-100 text-red-800
                        @elseif($response->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $response->getStatusLabel() }}
                    </span>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Mức độ</p>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @if($response->severity === 'critical') bg-red-100 text-red-800
                        @elseif($response->severity === 'high') bg-orange-100 text-orange-800
                        @elseif($response->severity === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ $response->getSeverityLabel() }}
                    </span>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Loại kích hoạt</p>
                    <p class="text-sm text-gray-900">{{ $response->getTriggerTypeLabel() }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Trigger Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin kích hoạt</h2>
                    <dl class="space-y-4">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Loại kích hoạt</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->getTriggerTypeLabel() }}</dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->trigger_description ?? '-' }}</dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Thời gian kích hoạt</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->triggered_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Response Action Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Hành động phản ứng</h2>
                    <dl class="space-y-4">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Hành động</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $response->getResponseActionLabel() }}
                                </span>
                            </dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Mô tả hành động</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->action_description ?? '-' }}</dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Yêu cầu xác nhận</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($response->requires_user_confirmation) bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif">
                                    @if($response->requires_user_confirmation) Có @else Không @endif
                                </span>
                            </dd>
                        </div>
                        @if ($response->response_action === 'temporary_lockout' || $response->response_action === 'lock_account')
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Khóa đến</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                @if ($response->lockout_until)
                                    {{ $response->lockout_until->format('d/m/Y H:i:s') }}
                                    <span class="text-xs text-gray-500">({{ $response->lockout_until->diffForHumans() }})</span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Execution Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết thực thi</h2>
                    <dl class="space-y-4">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->getStatusLabel() }}</dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Thời gian thực thi</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                @if ($response->executed_at)
                                    {{ $response->executed_at->format('d/m/Y H:i:s') }}
                                @else
                                    Chưa thực thi
                                @endif
                            </dd>
                        </div>
                        @if ($response->execution_result)
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Kết quả</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $response->execution_result }}</dd>
                        </div>
                        @endif
                        @if ($response->error_message)
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Lỗi</dt>
                            <dd class="mt-1 text-sm text-red-600 sm:col-span-2">{{ $response->error_message }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Anomaly Details -->
                @if ($response->anomaly_details)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết bất thường</h2>
                    <dl class="space-y-4">
                        @foreach ($response->anomaly_details as $key => $value)
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Admin Actions -->
                @if ($response->status === 'pending')
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hành động</h3>

                    <!-- Approve Form -->
                    <form action="{{ route('admin.security-settings.approve-response', $response->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Ghi chú
                            </label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Thêm ghi chú..."></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Duyệt
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('admin.security-settings.reject-response', $response->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-gray-700">
                                Lý do từ chối <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập lý do..." required></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Từ chối
                        </button>
                    </form>
                </div>
                @endif

                <!-- Suspicious Login Info -->
                @if ($response->suspiciousLogin)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Đăng nhập bất thường</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">IP</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $response->suspiciousLogin->ip_address }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Thiết bị</dt>
                            <dd class="text-sm text-gray-900">{{ $response->suspiciousLogin->device_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Vị trí</dt>
                            <dd class="text-sm text-gray-900">{{ $response->suspiciousLogin->location }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Điểm rủi ro</dt>
                            <dd class="text-sm text-gray-900">{{ $response->suspiciousLogin->risk_score ?? '-' }}/100</dd>
                        </div>
                    </dl>
                </div>
                @endif

                <!-- Review Info -->
                @if ($response->reviewed_at)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Xem xét bởi</h4>
                    <p class="text-sm text-blue-800">{{ $response->reviewedByAdmin->name ?? 'Admin' }}</p>
                    <p class="text-xs text-blue-600">{{ $response->reviewed_at->format('d/m/Y H:i') }}</p>
                    @if ($response->admin_notes)
                    <p class="text-sm text-blue-700 mt-2">{{ $response->admin_notes }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
