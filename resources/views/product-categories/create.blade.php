@extends('layouts.app')

@section('title', __('Tambah Kategori Produk'))

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">{{ __('Tambah Kategori Produk') }}</h2>
    </div>
    
    <div class="p-6">
        <form method="POST" action="{{ route('product-categories.store') }}">
            @csrf
            
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama Kategori') }} <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border @error('name') border-red-300 ring-red-100 @else border-gray-300 focus:border-orange-500 focus:ring-orange-100 @enderror rounded-lg shadow-sm focus:ring-4 transition-all outline-none"
                    placeholder="Contoh: Sayuran">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Deskripsi') }}</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-4 py-2 border @error('description') border-red-300 ring-red-100 @else border-gray-300 focus:border-orange-500 focus:ring-orange-100 @enderror rounded-lg shadow-sm focus:ring-4 transition-all outline-none"
                    placeholder="Deskripsi kategori...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('product-categories.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all">
                    {{ __('Batal') }}
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5">
                    {{ __('Simpan') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
