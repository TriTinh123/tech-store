@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Cảnh báo An ninh</h1>
            <p class="text-gray-600">Quản lý các cảnh báo về hoạt động đăng nhập bất thường trên tài khoản</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <!-- Total Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Tổng cảnh báo</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_count'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Unread Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Chưa đọc</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unread_count'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Critical Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Nghiêm trọng</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['critical_count'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Locked -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Tài khoản khóa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['locked_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <form action="{{ route('profile.alerts.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 w-full">
                    <!-- Severity Filter -->
                    <select name="severity" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả mức độ</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Nghiêm trọng</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Thấp</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Đã đọc</option>
                    </select>

                    <!-- Type Filter -->
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả loại</option>
                        <option value="account_locked" {{ request('type') === 'account_locked' ? 'selected' : '' }}>Tài khoản khóa</option>
                        <option value="new_ip" {{ request('type') === 'new_ip' ? 'selected' : '' }}>IP mới</option>
                        <option value="new_device" {{ request('type') === 'new_device' ? 'selected' : '' }}>Thiết bị mới</option>
                    </select>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Tìm kiếm
                    </button>
                </form>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Loại cảnh báo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Mức độ</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Thông báo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Thời gian</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($alerts as $alert)
                        <tr class="{{ $alert->isRead() ? 'bg-white' : 'bg-blue-50' }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $alert->getAlertTypeLabel() }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $badge = $alert->getSeverityBadge();
                                    $colorClass = match($badge['color']) {
                                        'red' => 'bg-red-100 text-red-800',
                                        'orange' => 'bg-orange-100 text-orange-800',
                                        'yellow' => 'bg-yellow-100 text-yellow-800',
                                        'blue' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $colorClass }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="max-w-xs truncate" title="{{ $alert->message }}">
                                    {{ \Str::limit($alert->message, 50) }}
                                </div>
                                @if (!$alert->isRead())
                                    <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Chưa đọc</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $alert->created_at->format('H:i d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="space-x-2">
                                    <button onclick="viewAlert({{ $alert->id }})" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Xem
                                    </button>
                                    @if ($alert->suspiciousLogin)
                                        <form action="{{ route('profile.alerts.confirm', $alert->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-medium">
                                                Xác nhận
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium">Không có cảnh báo nào</p>
                                <p class="text-sm">Tài khoản của bạn an toàn</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                {{ $alerts->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Alert Detail Modal -->
        <div id="alertModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="flex items-start justify-between">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Chi tiết Cảnh báo</h3>
                    <button onclick="closeAlertModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-4" id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewAlert(alertId) {
        // Fetch alert details
        fetch(`/profile/alerts/${alertId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerText = data.alertTypeLabel;
                document.getElementById('modalContent').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900">Thông báo</label>
                            <p class="mt-1 text-gray-700">${data.message}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Mức độ</label>
                                <p class="mt-1 text-gray-700">${data.severity}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Thời gian</label>
                                <p class="mt-1 text-gray-700">${data.created_at}</p>
                            </div>
                        </div>
                        ${data.suspiciousLogin ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Thông tin đăng nhập</label>
                                <div class="mt-1 space-y-2 text-sm text-gray-700">
                                    <p><strong>IP:</strong> ${data.suspiciousLogin.ip_address}</p>
                                    <p><strong>Vị trí:</strong> ${data.suspiciousLogin.city}, ${data.suspiciousLogin.country}</p>
                                    <p><strong>Thiết bị:</strong> ${data.suspiciousLogin.device_type}</p>
                                    <p><strong>Browser:</strong> ${data.suspiciousLogin.browser}</p>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('alertModal').classList.remove('hidden');
            });
    }

    function closeAlertModal() {
        document.getElementById('alertModal').classList.add('hidden');
    }
</script>
@endsection
