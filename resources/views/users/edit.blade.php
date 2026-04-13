@extends('layouts.app')

@section('title', 'Edit User')

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
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Edit User</h2>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 bg-amber-50 rounded-lg border border-amber-100">
                    <p class="text-xs font-semibold text-amber-700 mb-3">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ubah Password (kosongkan jika tidak ingin mengubah)
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-600 mb-1">Password
                                Baru</label>
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 @enderror"
                                placeholder="Min. 8 karakter">
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role_id" id="role_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white @error('role_id') border-red-400 @enderror">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="sr-only peer"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500">
                                </div>
                                <span class="ms-3 text-sm text-gray-600">
                                    {{ $user->id === auth()->id() ? 'Aktif (tidak bisa diubah untuk akun sendiri)' : 'Aktif' }}
                                </span>
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
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
