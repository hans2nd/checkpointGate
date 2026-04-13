@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('permissions.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Edit Permission</h2>
                <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $permission->name }}</p>
            </div>

            <form method="POST" action="{{ route('permissions.update', $permission) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Permission (slug) <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" required
                        pattern="[a-z_.]+"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror">
                    <p class="mt-1 text-xs text-gray-400">Huruf kecil, underscore, dan titik saja</p>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $permission->display_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('display_name') border-red-400 @enderror">
                    @error('display_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group <span class="text-red-500">*</span></label>
                    <input type="text" name="group" id="group" value="{{ old('group', $permission->group) }}" required list="groupList"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('group') border-red-400 @enderror">
                    <datalist id="groupList">
                        @foreach($groups as $g)
                            <option value="{{ $g }}">
                        @endforeach
                    </datalist>
                    @error('group')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('permissions.index') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Batal</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
