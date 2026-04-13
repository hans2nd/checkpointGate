@extends('layouts.app')

@section('title', 'Permission Management')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $permissions->flatten()->count() }} permission dalam {{ $permissions->count() }} group</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('permissions.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Permission
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('permissions.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari permission..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
                <select name="group"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">Semua Group</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                            {{ $group }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                    <a href="{{ route('permissions.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
                </div>
            </form>
        </div>

        {{-- Permission Groups --}}
        @forelse($permissions as $group => $perms)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">{{ $group ?? 'Tanpa Group' }}</h3>
                        <span class="text-xs text-gray-400 font-normal">({{ $perms->count() }} permission)</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($perms as $permission)
                        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $permission->display_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $permission->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('permissions.edit', $permission) }}"
                                    class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form id="deletePermForm{{ $permission->id }}" method="POST"
                                    action="{{ route('permissions.destroy', $permission) }}">@csrf @method('DELETE')</form>
                                <button type="button"
                                    onclick="confirmDelete(document.getElementById('deletePermForm{{ $permission->id }}'), 'Permission ini akan dihapus dari semua role.')"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <p class="font-medium text-gray-400">Belum ada permission</p>
            </div>
        @endforelse
    </div>
@endsection
