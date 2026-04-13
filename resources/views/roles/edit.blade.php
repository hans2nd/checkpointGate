@extends('layouts.app')

@section('title', 'Edit Role — ' . $role->display_name)

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('roles.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Edit Role: {{ $role->display_name }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Ubah informasi dan atur permission role ini</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Role (slug) <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                                pattern="[a-z_]+"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror">
                            <p class="mt-1 text-xs text-gray-400">Huruf kecil dan underscore saja</p>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('display_name') border-red-400 @enderror">
                            @error('display_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <input type="text" name="description" id="description" value="{{ old('description', $role->description) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Deskripsi singkat tentang role ini">
                    </div>
                </div>
            </div>

            {{-- Permission Assignment --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Permission</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Centang permission yang dimiliki role ini</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllPermissions()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors">Pilih Semua</button>
                            <button type="button" onclick="deselectAllPermissions()" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg transition-colors">Hapus Semua</button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($permissions as $group => $perms)
                        <div class="mb-6 last:mb-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">{{ $group }}</h4>
                                <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}')" class="text-xs text-indigo-500 hover:text-indigo-700 font-medium">(toggle)</button>
                                @php
                                    $groupTotal = $perms->count();
                                    $groupChecked = $perms->whereIn('id', $rolePermissions)->count();
                                @endphp
                                <span class="text-xs text-gray-400">{{ $groupChecked }}/{{ $groupTotal }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pl-4">
                                @foreach($perms as $permission)
                                    <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors
                                        {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'border-indigo-200 bg-indigo-50/50' : 'border-gray-100 hover:bg-gray-50' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="perm-checkbox perm-{{ Str::slug($group) }} rounded border-gray-300 text-indigo-500 focus:ring-indigo-500"
                                            {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                            onchange="updateLabel(this)">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $permission->display_name }}</p>
                                            <p class="text-xs text-gray-400 font-mono">{{ $permission->name }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada permission.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Batal</a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectAllPermissions() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = true; updateLabel(cb); });
        }
        function deselectAllPermissions() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = false; updateLabel(cb); });
        }
        function toggleGroup(group) {
            const checkboxes = document.querySelectorAll('.perm-' + group);
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => { cb.checked = !allChecked; updateLabel(cb); });
        }
        function updateLabel(checkbox) {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('border-indigo-200', 'bg-indigo-50/50');
                label.classList.remove('border-gray-100');
            } else {
                label.classList.remove('border-indigo-200', 'bg-indigo-50/50');
                label.classList.add('border-gray-100');
            }
        }
    </script>
@endsection
