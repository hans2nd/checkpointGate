@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Ubah Profil</h2>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui nama, alamat email, atau password Anda.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="p-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-400">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    <p class="text-xs text-yellow-600 mt-1.5">⚠️ Catatan: Email ini digunakan sebagai kredensial login Anda. Mengubah email akan merubah kredensial login Anda.</p>
                </div>

                <hr class="my-6 border-gray-100">

                <div class="px-4 py-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-700 mb-4">
                    <p class="font-medium">Rubah Password</p>
                    <p class="mt-0.5 text-xs text-blue-600">Kosongkan bagian password di bawah ini jika Anda tidak ingin merubah password saat ini.</p>
                </div>

                {{-- Current Password --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-orange-500">
                    Simpan Perubahan
                </button>
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
