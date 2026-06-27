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

        {{-- Search & Filter --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('roles.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari role berdasarkan nama atau deskripsi..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('roles.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Role Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($roles as $role)
                @php
                    $colorMap = [
                        'admin' => ['bg' => 'bg-gradient-to-br from-red-500 to-rose-600', 'icon' => 'text-red-100'],
                        'supervisor' => ['bg' => 'bg-gradient-to-br from-violet-500 to-purple-600', 'icon' => 'text-violet-100'],
                        'staff_admin' => ['bg' => 'bg-gradient-to-br from-cyan-500 to-blue-600', 'icon' => 'text-cyan-100'],
                        'operator' => ['bg' => 'bg-gradient-to-br from-amber-500 to-orange-600', 'icon' => 'text-amber-100'],
                        'security' => ['bg' => 'bg-gradient-to-br from-emerald-500 to-teal-600', 'icon' => 'text-emerald-100'],
                    ];
                    $colors = $colorMap[$role->name] ?? ['bg' => 'bg-gradient-to-br from-slate-600 to-gray-700', 'icon' => 'text-slate-100'];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                    <div class="{{ $colors['bg'] }} px-5 py-5 relative overflow-hidden">
                        {{-- Decorative background element --}}
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <div class="flex items-start justify-between relative z-10">
                            <div class="pr-4">
                                <h3 class="text-white font-bold text-lg leading-tight">{{ $role->display_name }}</h3>
                                <p class="text-white/70 text-[11px] mt-1 font-mono uppercase tracking-wider">{{ $role->name }}</p>
                            </div>
                            <div class="{{ $colors['icon'] }} shrink-0">
                                <svg class="w-10 h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-5 flex-1 flex flex-col bg-slate-50/30">
                        <p class="text-sm text-gray-500 mb-4 flex-1">{{ $role->description ?? 'Tidak ada deskripsi tersedia untuk role ini.' }}</p>
                        
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Users</p>
                                    <p class="font-bold text-gray-700">{{ $role->users_count }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Perms</p>
                                    <p class="font-bold text-gray-700">{{ $role->permissions_count }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-auto">
                            <a href="{{ route('roles.edit', $role) }}"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Atur Role
                            </a>
                            @if ($role->users_count === 0 && !in_array($role->name, ['admin', 'supervisor', 'staff_admin', 'operator', 'security']))
                                <form id="deleteRoleForm{{ $role->id }}" method="POST"
                                    action="{{ route('roles.destroy', $role) }}">@csrf @method('DELETE')</form>
                                <button type="button"
                                    onclick="confirmDelete(document.getElementById('deleteRoleForm{{ $role->id }}'))"
                                    class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-xl transition-all border border-red-100"
                                    title="Hapus Role">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada role</h3>
                        <p class="text-gray-500 text-sm">Gunakan form di atas untuk menambahkan role baru.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
