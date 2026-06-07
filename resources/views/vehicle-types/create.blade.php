@extends('layouts.app')

@section('title', __('Tambah Jenis Kendaraan'))

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('vehicle-types.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali ke Master Vehicle Type') }}
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('Tambah Jenis Kendaraan Baru') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('Jenis kendaraan akan otomatis diubah menjadi huruf kapital.') }}</p>
            </div>

            <form method="POST" action="{{ route('vehicle-types.store') }}" class="p-6">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Nama Jenis Kendaraan') }}
                        <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: WINGBOX"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all uppercase">
                </div>

                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('vehicle-types.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
