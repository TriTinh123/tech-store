@extends('layouts.app')

@section('title', 'Thống kê bảo mật')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Thống kê bảo mật</h1>
            <p class="mt-2 text-gray-600">Phân tích và báo cáo bảo mật trong 30 ngày qua</p>
        </div>

        <!-- Export Actions -->
        <div class="mb-8 flex gap-4">
            <form action="{{ route('admin.security-settings.export-report') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="report_type" value="all">
                <input type="hidden" name="format" value="json">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Xuất JSON
                </button>
            </form>
            <form action="{{ route('admin.security-settings.export-report') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="report_type" value="all">
                <input type="hidden" name="format" value="csv">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Xuất CSV
                </button>
            </form>
        </div>

        <!-- Main Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Login Attempts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tổng đăng nhập</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $loginAttempts }}</p>
                    </div>
                </div>
            </div>

            <!-- Successful Logins -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đăng nhập thành công</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $successfulLogins }}</p>
                    </div>
                </div>
            </div>

            <!-- Failed Logins -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đăng nhập thất bại</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $failedLogins }}</p>
                    </div>
                </div>
            </div>

            <!-- Suspicious Logins -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M9 3h6a9 9 0 019 9v6a9 9 0 01-9 9H9a9 9 0 01-9-9v-6a9 9 0 019-9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đăng nhập bất thường</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $suspiciousLogins }}</p>
                    </div>
                </div>
            </div>

            <!-- Locked Accounts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tài khoản bị khóa</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lockedAccounts }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Blocks -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">IP đang bị chặn</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeBlocks }}</p>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tỷ lệ thành công</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $loginAttempts > 0 ? round(($successfulLogins / $loginAttempts) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Daily Trend -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Xu hướng 7 ngày qua</h3>
                <div class="space-y-4">
                    @forelse ($dailyTrend as $day)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $day->date }}</span>
                                <span class="text-sm text-gray-500">{{ $day->total }} đăng nhập</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $day->total > 0 ? ($day->success / $day->total) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Không có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Risk Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Phân bố mức độ rủi ro</h3>
                <div class="space-y-4">
                    @if (!empty($riskDistribution))
                        @foreach ($riskDistribution as $level => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($level) }}</span>
                                    <span class="text-sm text-gray-500">{{ $count }} IP</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="@if($level === 'critical') bg-red-600 @elseif($level === 'high') bg-orange-600 @elseif($level === 'medium') bg-yellow-600 @else bg-green-600 @endif h-2 rounded-full" style="width: {{ $count > 0 ? ($count / max(array_values($riskDistribution)) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Không có dữ liệu</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Suspicious Users -->
        @if ($topSuspiciousUsers->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Người dùng đáng ngờ nhất (30 ngày qua)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hoạt động đáng ngờ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($topSuspiciousUsers as $suspicious)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $suspicious->user->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $suspicious->user->email ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $suspicious->count }} lần
                                    </span>
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
