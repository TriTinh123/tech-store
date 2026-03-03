@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý Phiên</h1>
            <p class="text-gray-600">Xem và quản lý tất cả các phiên đăng nhập của bạn trên các thiết bị khác nhau</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <!-- Active Sessions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Phiên Hoạt động</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_sessions'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Sessions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">Tổng Phiên</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Unique IPs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-gray-600 text-sm">IP Độc nhất</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unique_ips'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="mb-6 flex gap-2">
            <form action="{{ route('profile.sessions.terminate-others') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn kết thúc tất cả phiên khác?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    Kết thúc Tất cả Phiên Khác
                </button>
            </form>
        </div>

        <!-- Sessions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Thiết bị</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vị trí</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Địa chỉ IP</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Đăng nhập</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Trạng thái</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sessions as $session)
                        <tr class="{{ $session->is_current ? 'bg-blue-50' : 'bg-white' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $session->device_type }} · {{ $session->browser }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($session->is_current)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded">Phiên hiện tại</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $session->city }}, {{ $session->country }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                {{ $session->ip_address }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ $session->logged_in_at->format('H:i d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">Hoạt động: {{ $session->last_activity_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($session->isActive())
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if ($session->isActive() && !$session->is_current)
                                    <form action="{{ route('profile.sessions.terminate', $session->id) }}" method="POST" class="inline" onsubmit="return confirm('Kết thúc phiên này?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                            Kết thúc
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium">Không có phiên nào</p>
                                <p class="text-sm">Bạn chưa đăng nhập từ thiết bị nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                {{ $sessions->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Security Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Mẹo bảo mật</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Kiểm tra danh sách phiên thường xuyên để phát hiện hoạt động bất thường</li>
                            <li>Kết thúc các phiên từ thiết bị không còn sử dụng</li>
                            <li>Nếu bạn thấy phiên lạ, hãy đổi mật khẩu ngay lập tức</li>
                            <li>Mỗi phiên được bảo vệ bằng xác thực ba lớp (3FA)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
