@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $roles->count() }} role</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('roles.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Role
                </a>
            </div>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('roles.index') }}" class="flex gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari role..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Cari</button>
                <a href="{{ route('roles.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
            </form>
        </div>

        {{-- Role Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($roles as $role)
                @php
                    $colorMap = [
                        'admin' => ['bg' => 'bg-gradient-to-br from-red-500 to-rose-600', 'badge' => 'bg-red-50 text-red-700 border-red-200', 'icon' => 'text-red-100'],
                        'operator' => ['bg' => 'bg-gradient-to-br from-blue-500 to-indigo-600', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'text-blue-100'],
                        'viewer' => ['bg' => 'bg-gradient-to-br from-emerald-500 to-teal-600', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'text-emerald-100'],
                    ];
                    $colors = $colorMap[$role->name] ?? ['bg' => 'bg-gradient-to-br from-slate-500 to-gray-600', 'badge' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'text-slate-100'];
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="{{ $colors['bg'] }} px-5 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white font-bold text-lg">{{ $role->display_name }}</h3>
                                <p class="text-white/70 text-xs mt-0.5 font-mono">{{ $role->name }}</p>
                            </div>
                            <div class="{{ $colors['icon'] }}">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-sm text-gray-500 mb-3">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $role->users_count }} user
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                {{ $role->permissions_count }} permission
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('roles.edit', $role) }}"
                                class="flex-1 text-center px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg transition-colors">
                                Edit & Atur Permission
                            </a>
                            @if ($role->users_count === 0)
                                <form id="deleteRoleForm{{ $role->id }}" method="POST"
                                    action="{{ route('roles.destroy', $role) }}">@csrf @method('DELETE')</form>
                                <button type="button"
                                    onclick="confirmDelete(document.getElementById('deleteRoleForm{{ $role->id }}'))"
                                    class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition-colors">
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <p class="font-medium text-gray-400">Belum ada role</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
