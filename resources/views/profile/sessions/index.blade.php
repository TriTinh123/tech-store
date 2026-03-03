@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Phiên Làm Việc</h1>
            <p class="mt-2 text-gray-600">Xem và quản lý tất cả các thiết bị đang đăng nhập vào tài khoản của bạn</p>
        </div>

        <!-- Alert -->
        @if ($sessions->count() > 1)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700">
                    <span class="font-semibold">💡 Mẹo:</span> Bạn có {{ $sessions->count() }} phiên đang hoạt động. 
                    Hãy đảm bảo bạn chỉ đăng nhập từ các thiết bị mà bạn tin tưởng.
                </p>
            </div>
        @endif

        <!-- Sessions Grid -->
        <div class="space-y-4">
            @forelse ($sessions as $session)
                <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <!-- Session Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <!-- Device Icon -->
                            <div class="flex-shrink-0">
                                @if ($session->device_type === 'mobile')
                                    <span class="text-3xl">📱</span>
                                @elseif ($session->device_type === 'tablet')
                                    <span class="text-3xl">📱</span>
                                @else
                                    <span class="text-3xl">🖥️</span>
                                @endif
                            </div>

                            <!-- Device Info -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $session->getDeviceInfo() }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $session->location ?? 'Vị trí không xác định' }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center space-x-2">
                            @if ($session->id === $currentSessionId)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✓ Thiết bị hiện tại
                                </span>
                            @endif

                            @if ($session->is_flagged)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    ⚠️ Đáng ngờ
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Session Details -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-gray-600">Địa chỉ IP</p>
                            <p class="font-mono text-gray-900 break-all">{{ $session->ip_address }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Trình duyệt</p>
                            <p class="text-gray-900">{{ $session->browser ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Hệ điều hành</p>
                            <p class="text-gray-900">{{ $session->os ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Loại thiết bị</p>
                            <p class="text-gray-900">{{ ucfirst($session->device_type) }}</p>
                        </div>
                    </div>

                    <!-- Activity Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                        <div>
                            <p class="text-gray-600">Hoạt động cuối</p>
                            <p class="text-gray-900">{{ $session->getTimeSinceLastActivity() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Đăng nhập lúc</p>
                            <p class="text-gray-900">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Flag Reason -->
                    @if ($session->is_flagged && $session->flag_reason)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                            <p class="text-sm text-red-700">
                                <span class="font-semibold">Lý do cảnh báo:</span> {{ $session->flag_reason }}
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if ($session->id !== $currentSessionId)
                        <form method="POST" action="{{ route('profile.sessions.terminate', $session->id) }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors"
                                onclick="return confirm('Bạn chắc chắn muốn đăng xuất khỏi thiết bị này?');"
                            >
                                🚪 Đăng xuất khỏi thiết bị này
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600">Không có phiên hoạt động nào</p>
                </div>
            @endforelse
        </div>

        <!-- Terminate All Other Sessions -->
        @if ($sessions->count() > 1)
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                    🔐 Đăng xuất khỏi tất cả thiết bị khác
                </h3>
                <p class="text-sm text-yellow-700 mb-4">
                    Điều này sẽ đăng xuất khỏi tất cả các phiên làm việc khác ngoài thiết bị hiện tại.
                </p>
                <form method="POST" action="{{ route('profile.sessions.terminate-others') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors"
                        onclick="return confirm('Bạn chắc chắn muốn đăng xuất khỏi tất cả các thiết bị khác?');"
                    >
                        Đăng xuất khỏi tất cả thiết bị khác
                    </button>
                </form>
            </div>
        @endif

        <!-- Back Link -->
        <div class="mt-8">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Quay lại hồ sơ
            </a>
        </div>
    </div>
</div>
@endsection
