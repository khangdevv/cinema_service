@extends('admin.layouts.app')

@section('title', 'Sửa Phim')
@section('page-title', 'Sửa Phim')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Thông Tin Phim #{{ $movie->id }}</h2>
        </div>

        <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tên Phim <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $movie->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       placeholder="Nhập tên phim"
                       required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="duration_min" class="block text-sm font-medium text-gray-700 mb-2">Thời Lượng (phút) <span class="text-red-500">*</span></label>
                    <input type="number" id="duration_min" name="duration_min"
                           value="{{ old('duration_min', $movie->duration_min) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                           placeholder="120"
                           min="1"
                           required>
                </div>

                <div>
                    <label for="rating_code" class="block text-sm font-medium text-gray-700 mb-2">Rating Code</label>
                    <select id="rating_code" name="rating_code"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        <option value="">-- Chọn --</option>
                        <option value="P" {{ old('rating_code', $movie->rating_code) === 'P' ? 'selected' : '' }}>P - Phổ biến</option>
                        <option value="C13" {{ old('rating_code', $movie->rating_code) === 'C13' ? 'selected' : '' }}>C13 - Cấm dưới 13 tuổi</option>
                        <option value="C16" {{ old('rating_code', $movie->rating_code) === 'C16' ? 'selected' : '' }}>C16 - Cấm dưới 16 tuổi</option>
                        <option value="C18" {{ old('rating_code', $movie->rating_code) === 'C18' ? 'selected' : '' }}>C18 - Cấm dưới 18 tuổi</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="genre" class="block text-sm font-medium text-gray-700 mb-2">Thể Loại</label>
                <input type="text" id="genre" name="genre"
                       value="{{ old('genre', $movie->genre) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       placeholder="Hành động, Kinh dị, Tình cảm...">
            </div>

            <div>
                <label for="poster" class="block text-sm font-medium text-gray-700 mb-2">URL Poster</label>
                @if($movie->poster)
                    <div class="mb-3">
                        <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-32 h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif
                <input type="text" id="poster" name="poster"
                       value="{{ old('poster', $movie->poster) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       placeholder="https://example.com/poster.jpg">
                <p class="mt-1 text-sm text-gray-500">Nhập URL hình ảnh poster của phim</p>
            </div>

            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $movie->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-sm font-medium text-gray-700">Đang chiếu</span>
                </label>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                    Cập Nhật
                </button>
                <a href="{{ route('admin.movies.list') }}"
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
