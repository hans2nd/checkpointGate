@extends('layouts.app')

@section('title', 'Tambah Permission')

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
                <h2 class="text-lg font-semibold text-gray-800">Tambah Permission Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Buat permission baru untuk mengatur akses fitur</p>
            </div>

            <form method="POST" action="{{ route('permissions.store') }}" class="p-6 space-y-5" id="permissionForm">
                @csrf

                <!-- Mode Information -->
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <p class="text-sm font-medium text-gray-700">Generate Multiple Actions</p>
                    <p class="text-xs text-gray-500 mt-1">Sistem ini memfasilitasi pembuatan permission secara otomatis berbasis action untuk sebuah modul. Anda tidak perlu membuat permission satu per satu secara manual.</p>
                </div>
                <!-- CRUD Mode Fields -->
                <div id="crud-fields" class="space-y-5">
                    <div>
                        <label for="resource" class="block text-sm font-medium text-gray-700 mb-1">Modul / Resource Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="resource" id="resource" value="{{ old('resource') }}"
                            pattern="[a-z_]+"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('resource') border-red-400 @enderror"
                            placeholder="contoh: users">
                        <p class="mt-1 text-xs text-gray-400">Huruf kecil dan underscore saja (contoh: users, report_sales)</p>
                        @error('resource')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="resource_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Modul <span class="text-red-500">*</span></label>
                        <input type="text" name="resource_name" id="resource_name" value="{{ old('resource_name') }}" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('resource_name') border-red-400 @enderror"
                            placeholder="contoh: Manajemen User">
                        @error('resource_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actions (Pilih Hak Akses) <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="view" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">View (Lihat)</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="create" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Create (Tambah)</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="edit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Edit (Ubah)</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="delete" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Delete (Hapus)</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="export" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Export</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="import" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Import</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="approve" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Approve</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="actions[]" value="trigger" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Trigger</span>
                            </label>
                        </div>
                        @error('actions')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group <span class="text-red-500">*</span></label>
                    <input type="text" name="group" id="group" value="{{ old('group') }}" required list="groupList"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('group') border-red-400 @enderror"
                        placeholder="contoh: Checkpoint">
                    <datalist id="groupList">
                        @foreach($groups as $g)
                            <option value="{{ $g }}">
                        @endforeach
                    </datalist>
                    <p class="mt-1 text-xs text-gray-400">Pilih group yang ada atau ketik baru</p>
                    @error('group')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('permissions.index') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Batal</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Simpan Permission
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection
