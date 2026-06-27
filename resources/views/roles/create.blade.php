@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('roles.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">Informasi Role</h2>
                    <p class="text-sm text-gray-500 mt-1">Buat role baru untuk mengatur akses user</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Role (slug) <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                pattern="[a-z_]+"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror"
                                placeholder="contoh: supervisor">
                            <p class="mt-1 text-xs text-gray-400">Huruf kecil dan underscore saja</p>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('display_name') border-red-400 @enderror"
                                placeholder="contoh: Supervisor">
                            @error('display_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Deskripsi singkat tentang role ini">
                    </div>
                </div>
            </div>

            {{-- Permission Assignment --}}
            {{-- Permission Assignment --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Pilih Permission</h2>
                            <p class="text-sm text-gray-500 mt-1">Centang permission yang dimiliki role ini</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-xl transition-colors border border-indigo-100">Pilih Semua</button>
                            <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold rounded-xl transition-colors border border-gray-200">Hapus Semua</button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                        @forelse($permissions as $group => $perms)
                            @php
                                $groupTotal = $perms->count();
                                $groupChecked = collect(old('permissions', []))->intersect($perms->pluck('id'))->count();
                                $isOpen = $loop->first || $groupChecked > 0;
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all h-fit">
                                <div class="flex items-center justify-between px-5 py-3.5 bg-gray-50 hover:bg-gray-100 cursor-pointer transition-colors" onclick="toggleCollapse('group-{{ Str::slug($group) }}')">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $group }}</h4>
                                            <p class="text-xs font-medium text-gray-500 mt-0.5 group-counter-{{ Str::slug($group) }}" data-total="{{ $groupTotal }}">{{ $groupChecked }} dari {{ $groupTotal }} dipilih</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <button type="button" onclick="event.stopPropagation(); toggleGroupCheckboxes('{{ Str::slug($group) }}')" class="text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 font-bold px-3 py-1.5 bg-white border border-indigo-100 rounded-lg transition-colors shadow-sm" title="Pilih Semua di Grup Ini">
                                            Pilih
                                        </button>
                                        <svg id="icon-group-{{ Str::slug($group) }}" class="w-5 h-5 text-gray-500 transition-transform duration-200 transform {{ $isOpen ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div id="group-{{ Str::slug($group) }}" class="p-4 border-t border-gray-100 {{ $isOpen ? '' : 'hidden' }}">
                                    <div class="flex flex-col gap-3">
                                        @foreach($perms as $permission)
                                            <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:shadow-sm
                                                {{ in_array($permission->id, old('permissions', [])) ? 'border-indigo-300 bg-indigo-50/40 ring-1 ring-indigo-100' : 'border-gray-200 hover:border-indigo-200 hover:bg-gray-50/50' }}">
                                                <div class="pt-0.5">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                        class="perm-checkbox perm-{{ Str::slug($group) }} w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-colors"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                        onchange="updateLabel(this); updateGroupCounter('{{ Str::slug($group) }}')">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 truncate mb-0.5" title="{{ $permission->display_name }}">{{ $permission->display_name }}</p>
                                                    <p class="text-[11px] text-indigo-600 font-mono mb-1.5 truncate" title="{{ $permission->name }}">{{ $permission->name }}</p>
                                                    @if($permission->description)
                                                        <p class="text-xs text-gray-500 leading-snug line-clamp-2" title="{{ $permission->description }}">{{ $permission->description }}</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <p class="text-sm text-gray-400 text-center py-4">Belum ada permission. <a href="{{ route('permissions.create') }}" class="text-indigo-500 hover:underline">Buat permission</a> terlebih dahulu.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-colors">Batal</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-sm hover:shadow-md">
                    Simpan Role
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectAllPermissions() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = true; updateLabel(cb); });
            updateAllCounters();
        }
        function deselectAllPermissions() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = false; updateLabel(cb); });
            updateAllCounters();
        }
        function toggleGroupCheckboxes(group) {
            const checkboxes = document.querySelectorAll('.perm-' + group);
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => { cb.checked = !allChecked; updateLabel(cb); });
            updateGroupCounter(group);
        }
        function toggleCollapse(groupId) {
            const el = document.getElementById(groupId);
            const icon = document.getElementById('icon-' + groupId);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                el.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
        function updateGroupCounter(group) {
            const checkboxes = document.querySelectorAll('.perm-' + group);
            const checkedCount = [...checkboxes].filter(cb => cb.checked).length;
            const counterEl = document.querySelector('.group-counter-' + group);
            if (counterEl) {
                const total = counterEl.getAttribute('data-total');
                counterEl.textContent = `${checkedCount} dari ${total} dipilih`;
            }
        }
        function updateAllCounters() {
            const groups = new Set();
            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                const groupClass = [...cb.classList].find(c => c.startsWith('perm-') && c !== 'perm-checkbox');
                if (groupClass) groups.add(groupClass.replace('perm-', ''));
            });
            groups.forEach(g => updateGroupCounter(g));
        }
        function updateLabel(checkbox) {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('border-indigo-300', 'bg-indigo-50/40', 'ring-1', 'ring-indigo-100');
                label.classList.remove('border-gray-200', 'hover:border-indigo-200', 'hover:bg-gray-50/50');
            } else {
                label.classList.remove('border-indigo-300', 'bg-indigo-50/40', 'ring-1', 'ring-indigo-100');
                label.classList.add('border-gray-200', 'hover:border-indigo-200', 'hover:bg-gray-50/50');
            }
        }
    </script>
@endsection
