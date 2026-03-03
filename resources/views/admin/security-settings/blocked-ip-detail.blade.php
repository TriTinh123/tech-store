@extends('layouts.app')

@section('title', 'Chi tiết IP bị chặn')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-mono">{{ $blockedIp->ip_address }}</h1>
                <p class="mt-2 text-gray-600">Chi tiết về IP bị chặn và hoạt động liên quan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.security-settings.blocked-ips') }}" class="text-blue-600 hover:text-blue-900">
                    ← Quay lại
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- IP Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Thông tin IP</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Vị trí</p>
                    <p class="text-lg text-gray-900">{{ $blockedIp->location ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Quốc gia</p>
                    <p class="text-lg text-gray-900">{{ $blockedIp->country_code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Mức độ rủi ro</p>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @if($blockedIp->risk_level === 'critical') bg-red-100 text-red-800
                        @elseif($blockedIp->risk_level === 'high') bg-orange-100 text-orange-800
                        @elseif($blockedIp->risk_level === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ $blockedIp->getRiskLevelLabel() }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Loại chặn</p>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $blockedIp->getBlockTypeLabel() }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Chi tiết chặn</h3>
                <dl class="space-y-4">
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Lý do chặn</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $blockedIp->reason ?? '-' }}</dd>
                    </div>
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Thời gian chặn</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $blockedIp->blocked_at->format('d/m/Y H:i:s') }}</dd>
                    </div>
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Kiểu chặn</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            @if ($blockedIp->is_permanent)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Vĩnh viễn
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Tạm thời
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if (!$blockedIp->is_permanent && $blockedIp->unblock_at)
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Bỏ chặn lúc</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ $blockedIp->unblock_at->format('d/m/Y H:i:s') }}
                            <span class="text-xs text-gray-500">({{ $blockedIp->unblock_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                    @endif
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Chặn bởi</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ $blockedIp->blockedByAdmin->name ?? 'System' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Statistics -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Thống kê</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tổng cố gắng</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $blockedIp->total_login_attempts }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cố gắng thất bại</p>
                        <p class="text-lg font-semibold text-red-600">{{ $blockedIp->failed_attempts }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Hoạt động đáng ngờ</p>
                        <p class="text-lg font-semibold text-yellow-600">{{ $blockedIp->suspicious_activities_count }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Lần cố gắng cuối</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if ($blockedIp->last_attempt_at)
                                {{ $blockedIp->last_attempt_at->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 mt-6 flex gap-3">
                <form action="{{ route('admin.security-settings.unblock-ip', $blockedIp->ip_address) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700" onclick="return confirm('Bạn chắc chắn muốn bỏ chặn IP này?')">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bỏ chặn IP
                    </button>
                </form>
            </div>
        </div>

        <!-- Login Attempts from this IP -->
        @if ($relatedAttempts->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Các cố gắng đăng nhập từ IP này (50 gần nhất)</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thiết bị</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($relatedAttempts as $attempt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if ($attempt->user)
                                            {{ $attempt->user->email }}
                                        @else
                                            Unknown
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($attempt->status === 'success') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($attempt->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $attempt->device_type ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $attempt->created_at->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
