@extends('admin.layouts.app')

@section('title', 'Sửa Người Dùng')
@section('page-title', 'Sửa Người Dùng')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Thông Tin Người Dùng #{{ $user->id }}</h2>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Họ Tên</label>
                <input type="text" id="full_name" name="full_name"
                       value="{{ old('full_name', $user->full_name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       required>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số Điện Thoại</label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Vai Trò</label>
                <select id="role" name="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    <option value="CUSTOMER" {{ old('role', $user->role) === 'CUSTOMER' ? 'selected' : '' }}>Customer</option>
                    <option value="STAFF" {{ old('role', $user->role) === 'STAFF' ? 'selected' : '' }}>Staff</option>
                    <option value="ADMIN" {{ old('role', $user->role) === 'ADMIN' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-sm font-medium text-gray-700">Tài khoản đang hoạt động</span>
                </label>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                    Cập Nhật
                </button>
                <a href="{{ route('admin.users.list') }}"
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
