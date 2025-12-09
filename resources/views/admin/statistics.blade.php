@extends('admin.layouts.app')

@section('title', 'Thống Kê')
@section('page-title', 'Thống Kê')

@section('content')
<!-- Overview Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Tổng Doanh Thu</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalRevenue) }}đ</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Đơn Đã Thanh Toán</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($paidOrders) }} / {{ number_format($totalOrders) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Phim Đang Chiếu</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($activeMovies) }} / {{ number_format($totalMovies) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Tổng Người Dùng</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Orders by Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Đơn Hàng Theo Trạng Thái</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($ordersByStatus as $status)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($status->status === 'PAID')
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-gray-700">Đã thanh toán</span>
                            @elseif($status->status === 'INIT')
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-gray-700">Chờ xử lý</span>
                            @else
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Đã hủy</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-gray-900">{{ number_format($status->count) }} đơn</span>
                            <span class="text-gray-500 text-sm ml-2">({{ number_format($status->total ?? 0) }}đ)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Orders by Channel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Đơn Hàng Theo Kênh</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($ordersByChannel as $channel)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($channel->channel === 'WEB')
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Website</span>
                            @else
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">POS (Quầy)</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-gray-900">{{ number_format($channel->count) }} đơn</span>
                            <span class="text-gray-500 text-sm ml-2">({{ number_format($channel->total ?? 0) }}đ)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Orders by Payment Method -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Phương Thức Thanh Toán</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($ordersByPayment as $payment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($payment->payment_method === 'CASH')
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <span class="text-lg">💵</span>
                                </div>
                                <span class="text-gray-700">Tiền mặt</span>
                            @elseif($payment->payment_method === 'CARD')
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-lg">💳</span>
                                </div>
                                <span class="text-gray-700">Thẻ</span>
                            @else
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-lg">📱</span>
                                </div>
                                <span class="text-gray-700">Ví điện tử</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-gray-900">{{ number_format($payment->count) }} đơn</span>
                            <span class="text-gray-500 text-sm ml-2">({{ number_format($payment->total ?? 0) }}đ)</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Chưa có dữ liệu thanh toán</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Users by Role -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Người Dùng Theo Vai Trò</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($usersByRole as $role)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($role->role === 'ADMIN')
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Admin</span>
                            @elseif($role->role === 'STAFF')
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Nhân viên</span>
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Khách hàng</span>
                            @endif
                        </div>
                        <span class="font-semibold text-gray-900">{{ number_format($role->count) }} người</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Top Movies -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Top Phim Doanh Thu Cao Nhất</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Vé Bán</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh Thu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($topMovies as $index => $movie)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($index === 0)
                                <span class="text-2xl">🥇</span>
                            @elseif($index === 1)
                                <span class="text-2xl">🥈</span>
                            @elseif($index === 2)
                                <span class="text-2xl">🥉</span>
                            @else
                                <span class="text-gray-500 font-medium">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($movie->poster)
                                    <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-10 h-14 object-cover rounded">
                                @else
                                    <div class="w-10 h-14 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-lg">🎬</span>
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900">{{ $movie->title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ number_format($movie->ticket_count) }} vé</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-green-600">{{ number_format($movie->revenue) }}đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Chưa có dữ liệu doanh thu
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
