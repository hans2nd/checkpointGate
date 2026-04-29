@extends('layouts.app')

@section('title', 'Tambah Checkpoint')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('checkpoints.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Data Checkpoint
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Tambah Data Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pilih No Polisi untuk mengisi data otomatis dari master kendaraan.
                </p>
            </div>

            <form method="POST" action="{{ route('checkpoints.store') }}" class="p-6">
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

                {{-- Arrival Date/Time Info --}}
                <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Waktu Kedatangan
                                Kendaraan</p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span class="text-lg font-bold text-gray-800"
                                    id="arrival-date">{{ now()->format('d/m/Y') }}</span>
                                <span class="text-gray-400">|</span>
                                <span class="text-lg font-bold text-orange-600 font-mono"
                                    id="arrival-time">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                        <div class="ml-auto flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs text-gray-500">Live</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- No Polisi with autocomplete --}}
                    <div class="md:col-span-2 relative">
                        <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-1.5">No Polisi <span
                                class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="text" id="no_polisi" name="no_polisi" value="{{ old('no_polisi') }}" required
                                placeholder="Ketik no polisi..." autocomplete="off"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all uppercase">
                            <div id="autocomplete-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>
                        {{-- Autocomplete dropdown --}}
                        <div id="autocomplete-list"
                            class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                        </div>
                        <div id="vehicle-info"
                            class="hidden mt-2 px-3 py-2 bg-orange-50 border border-orange-100 rounded-lg text-sm text-orange-700">
                            <span class="font-medium">✓ Data ditemukan</span> — terisi otomatis dari master kendaraan
                        </div>
                    </div>

                    {{-- Driver (auto-filled) --}}
                    <div>
                        <label for="driver" class="block text-sm font-medium text-gray-700 mb-1.5">Driver <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="driver" name="driver" value="{{ old('driver') }}" required
                            placeholder="Nama driver"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-gray-50"
                            readonly>
                    </div>

                    {{-- Vendor (auto-filled) --}}
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="vendor" name="vendor" value="{{ old('vendor') }}" required
                            placeholder="Nama vendor"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-gray-50"
                            readonly>
                    </div>

                    {{-- Tipe (auto-filled) --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span
                                class="text-red-400">*</span></label>
                        <select id="tipe" name="tipe" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 transition-all"
                            disabled>
                            <option value="EKSTERNAL">EKSTERNAL</option>
                            <option value="INTERNAL">INTERNAL</option>
                        </select>
                        <input type="hidden" name="tipe" id="tipe_hidden" value="{{ old('tipe', 'EKSTERNAL') }}">
                    </div>

                    {{-- Jenis Kendaraan (auto-filled) --}}
                    <div>
                        <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis
                            Kendaraan
                            <span class="text-red-400">*</span></label>
                        <input type="text" id="jenis_kendaraan" name="jenis_kendaraan"
                            value="{{ old('jenis_kendaraan') }}" required placeholder="Jenis kendaraan"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-gray-50"
                            readonly>
                    </div>

                    {{-- Jenis Barang --}}
                    <div>
                        <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Barang
                            <span class="text-red-400">*</span></label>
                        <select id="jenis_barang" name="jenis_barang" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="FROZEN" {{ old('jenis_barang') == 'FROZEN' ? 'selected' : '' }}>FROZEN</option>
                            <option value="DRY" {{ old('jenis_barang') == 'DRY' ? 'selected' : '' }}>DRY</option>
                            <option value="CHILLED" {{ old('jenis_barang') == 'CHILLED' ? 'selected' : '' }}>CHILLED
                            </option>
                        </select>
                    </div>

                    {{-- Aktivitas --}}
                    <div>
                        <label for="aktivitas" class="block text-sm font-medium text-gray-700 mb-1.5">Aktivitas <span
                                class="text-red-400">*</span></label>
                        <select id="aktivitas" name="aktivitas" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="INBOUND" {{ old('aktivitas') == 'INBOUND' ? 'selected' : '' }}>INBOUND</option>
                            <option value="OUTBOUND" {{ old('aktivitas') == 'OUTBOUND' ? 'selected' : '' }}>OUTBOUND
                            </option>
                        </select>
                    </div>

                    {{-- Gate --}}
                    {{-- <div>
                        <label for="gate" class="block text-sm font-medium text-gray-700 mb-1.5">Gate</label>
                        <input type="number" id="gate" name="gate" value="{{ old('gate') }}"
                            placeholder="11" min="1"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div> --}}
                </div>

                {{-- No. Surat Jalan & Purchase Order --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1.5">No. Surat
                            Jalan</label>
                        <input type="text" id="no_surat_jalan" name="no_surat_jalan"
                            value="{{ old('no_surat_jalan') }}" placeholder="Surat Jalan"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label for="purchase_order" class="block text-sm font-medium text-gray-700 mb-1.5">Purchase
                            Order</label>
                        <input type="text" id="purchase_order" name="purchase_order"
                            value="{{ old('purchase_order') }}" placeholder="Purchase Order"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Note/Keterangan --}}
                <div class="mt-5">
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan /
                        Keterangan</label>
                    <textarea id="note" name="note" rows="3" placeholder="Contoh Inputan : Full Botan / 1.500 Ctn / 2 SKU"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none">{{ old('note') }}</textarea>
                </div>

                {{-- Info --}}
                {{-- <div class="mt-5 px-4 py-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-700">
                    <p class="font-medium">💡 Setelah data disimpan:</p>
                    <ul class="mt-1 text-xs space-y-0.5 text-blue-600">
                        <li>• Gunakan tombol <strong>📥 Terima</strong> untuk mencatat waktu penerimaan dokumen</li>
                        <li>• Gunakan tombol <strong>📤 Serah</strong> untuk mencatat waktu penyerahan dokumen</li>
                        <li>• Gunakan tombol <strong>▶ Start</strong> untuk mulai loading</li>
                        <li>• Gunakan tombol <strong>⏹ End</strong> untuk selesai loading (durasi otomatis dihitung)</li>
                    </ul>
                </div> --}}

                {{-- Submit --}}
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        Simpan Data
                    </button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Auto-fill JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const noPolisiInput = document.getElementById('no_polisi');
            const autocompleteList = document.getElementById('autocomplete-list');
            const spinner = document.getElementById('autocomplete-spinner');
            const vehicleInfo = document.getElementById('vehicle-info');
            let debounceTimer;

            // Autocomplete search
            noPolisiInput.addEventListener('input', function() {
                const term = this.value.trim();
                clearTimeout(debounceTimer);

                if (term.length < 2) {
                    autocompleteList.classList.add('hidden');
                    autocompleteList.innerHTML = '';
                    return;
                }

                spinner.classList.remove('hidden');

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('vehicles.search') }}?term=${encodeURIComponent(term)}`)
                        .then(res => res.json())
                        .then(vehicles => {
                            spinner.classList.add('hidden');
                            autocompleteList.innerHTML = '';

                            if (vehicles.length === 0) {
                                autocompleteList.innerHTML =
                                    '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>';
                                autocompleteList.classList.remove('hidden');
                                return;
                            }

                            vehicles.forEach(v => {
                                const item = document.createElement('div');
                                item.className =
                                    'px-3 py-2 hover:bg-orange-50 cursor-pointer text-sm flex justify-between items-center transition-colors';
                                item.innerHTML = `
                            <div>
                                <span class="font-semibold text-gray-900">${v.no_polisi}</span>
                                <span class="text-gray-400 mx-1">—</span>
                                <span class="text-gray-600">${v.driver}</span>
                            </div>
                            <div class="flex gap-1.5">
                                <span class="text-xs px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">${v.vendor}</span>
                                <span class="text-xs px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">${v.jenis_kendaraan}</span>
                            </div>
                        `;
                                item.addEventListener('click', () => selectVehicle(v));
                                autocompleteList.appendChild(item);
                            });

                            autocompleteList.classList.remove('hidden');
                        })
                        .catch(() => {
                            spinner.classList.add('hidden');
                        });
                }, 300);
            });

            function selectVehicle(vehicle) {
                noPolisiInput.value = vehicle.no_polisi;
                document.getElementById('driver').value = vehicle.driver;
                document.getElementById('vendor').value = vehicle.vendor;
                document.getElementById('jenis_kendaraan').value = vehicle.jenis_kendaraan;

                // Set tipe
                document.getElementById('tipe').value = vehicle.tipe;
                document.getElementById('tipe_hidden').value = vehicle.tipe;

                autocompleteList.classList.add('hidden');
                autocompleteList.innerHTML = '';
                vehicleInfo.classList.remove('hidden');
            }

            // Hide autocomplete on click outside
            document.addEventListener('click', function(e) {
                if (!noPolisiInput.contains(e.target) && !autocompleteList.contains(e.target)) {
                    autocompleteList.classList.add('hidden');
                }
            });

            // Clear auto-fill state when no_polisi is manually changed
            noPolisiInput.addEventListener('focus', function() {
                vehicleInfo.classList.add('hidden');
                // Make fields editable again if user wants to type manually
                document.getElementById('driver').removeAttribute('readonly');
                document.getElementById('vendor').removeAttribute('readonly');
                document.getElementById('jenis_kendaraan').removeAttribute('readonly');
                document.getElementById('tipe').removeAttribute('disabled');

                ['driver', 'vendor', 'jenis_kendaraan'].forEach(id => {
                    document.getElementById(id).classList.remove('bg-gray-50');
                });
                document.getElementById('tipe').classList.remove('bg-gray-50');
            });
        });

        // Live clock for arrival time
        setInterval(function() {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('arrival-date').textContent =
                pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear();
            document.getElementById('arrival-time').textContent =
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }, 1000);
    </script>
@endsection
