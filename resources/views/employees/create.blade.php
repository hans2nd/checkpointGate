@extends('layouts.app')

@section('title', 'Tambah Employee')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employees.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Master Employee
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Tambah Employee Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Employee dapat login menggunakan Employee ID</p>
            </div>

            <form method="POST" action="{{ route('employees.store') }}" class="p-6">
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

                <div class="space-y-5">
                    {{-- Employee ID --}}
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1.5">Employee ID <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required
                            placeholder="Contoh: EMP001, STF-123"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-400">ID unik sebagai credential login (tanpa password)</p>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Employee <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1.5">Role <span
                                class="text-red-400">*</span></label>
                        <select id="role_id" name="role_id" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white transition-all">
                            <option value="">-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                            <span class="text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>

                    {{-- Create linked user option --}}
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="flex items-start gap-3">
                            <input type="hidden" name="create_user" value="0">
                            <input type="checkbox" name="create_user" value="1"
                                class="w-4 h-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500 mt-0.5">
                            <div>
                                <span class="text-sm font-medium text-blue-800">Tambahkan juga sebagai User</span>
                                <p class="text-xs text-blue-600 mt-0.5">Jika dicentang, employee ini juga akan terdaftar di User Management dengan role yang sama.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Simpan Employee
                    </button>
                    <a href="{{ route('employees.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
