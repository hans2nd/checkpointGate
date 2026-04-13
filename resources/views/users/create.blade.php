@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Tambah User Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Isi data user yang akan ditambahkan</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 @enderror"
                        placeholder="user@email.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 @enderror"
                            placeholder="Min. 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role_id" id="role_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white @error('role_id') border-red-400 @enderror">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }} — {{ $role->description }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="flex items-center gap-3 mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500">
                                </div>
                                <span class="ms-3 text-sm text-gray-600">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Batal</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Simpan User
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
