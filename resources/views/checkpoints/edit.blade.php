@extends('layouts.app')

@section('title', __('Edit Checkpoint'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('checkpoints.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali ke Data Checkpoint') }}
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('Edit Data Checkpoint') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $checkpoint->no_polisi }} —
                    {{ $checkpoint->tanggal->format('d/m/Y') }}</p>
            </div>

            <form method="POST" action="{{ route('checkpoints.update', $checkpoint) }}" class="p-6">
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

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Tanggal --}}
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Tanggal') }}
                            <span class="text-red-400">*</span></label>
                        <input type="date" id="tanggal" name="tanggal"
                            value="{{ old('tanggal', $checkpoint->tanggal->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- No Polisi --}}
                    <div>
                        <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('No Polisi') }}
                            <span class="text-red-400">*</span></label>
                        <input type="text" id="no_polisi" name="no_polisi"
                            value="{{ old('no_polisi', $checkpoint->no_polisi) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Vendor --}}
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="vendor" name="vendor"
                            value="{{ old('vendor', $checkpoint->vendor) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Driver --}}
                    <div>
                        <label for="driver" class="block text-sm font-medium text-gray-700 mb-1.5">Driver <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="driver" name="driver"
                            value="{{ old('driver', $checkpoint->driver) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span
                                class="text-red-400">*</span></label>
                        <select id="tipe" name="tipe" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="EKSTERNAL"
                                {{ old('tipe', $checkpoint->tipe) == 'EKSTERNAL' ? 'selected' : '' }}>
                                EKSTERNAL</option>
                            <option value="INTERNAL" {{ old('tipe', $checkpoint->tipe) == 'INTERNAL' ? 'selected' : '' }}>
                                INTERNAL</option>
                        </select>
                    </div>

                    {{-- Jenis Kendaraan --}}
                    <div>
                        <label for="jenis_kendaraan"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Jenis Kendaraan') }}</label>
                        <select id="jenis_kendaraan" name="jenis_kendaraan"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="">{{ __('-- Pilih --') }}</option>
                            @foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT', 'L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $jk)
                                <option value="{{ $jk }}"
                                    {{ old('jenis_kendaraan', $checkpoint->jenis_kendaraan) == $jk ? 'selected' : '' }}>
                                    {{ $jk }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Waktu Penerimaan Dokumen --}}
                    <div>
                        <label for="waktu_penerimaan_dokumen"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Waktu Penerimaan Dokumen') }}</label>
                        <input type="datetime-local" id="waktu_penerimaan_dokumen" name="waktu_penerimaan_dokumen"
                            value="{{ old('waktu_penerimaan_dokumen', $checkpoint->waktu_penerimaan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Waktu Penyerahan Dokumen --}}
                    <div>
                        <label for="waktu_penyerahan_dokumen"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Waktu Penyerahan Dokumen') }}</label>
                        <input type="datetime-local" id="waktu_penyerahan_dokumen" name="waktu_penyerahan_dokumen"
                            value="{{ old('waktu_penyerahan_dokumen', $checkpoint->waktu_penyerahan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Jenis Barang --}}
                    <div>
                        <label for="jenis_barang"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Jenis Barang') }} <span
                                class="text-red-400">*</span></label>
                        <select id="jenis_barang" name="jenis_barang" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="FROZEN"
                                {{ old('jenis_barang', $checkpoint->jenis_barang) == 'FROZEN' ? 'selected' : '' }}>FROZEN
                            </option>
                            <option value="DRY"
                                {{ old('jenis_barang', $checkpoint->jenis_barang) == 'DRY' ? 'selected' : '' }}>DRY
                            </option>
                            <option value="CHILLED"
                                {{ old('jenis_barang', $checkpoint->jenis_barang) == 'CHILLED' ? 'selected' : '' }}>CHILLED
                            </option>
                        </select>
                    </div>

                    {{-- Aktivitas --}}
                    <div>
                        <label for="aktivitas" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Aktivitas') }}
                            <span class="text-red-400">*</span></label>
                        <select id="aktivitas" name="aktivitas" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="INBOUND"
                                {{ old('aktivitas', $checkpoint->aktivitas) == 'INBOUND' ? 'selected' : '' }}>INBOUND
                            </option>
                            <option value="OUTBOUND"
                                {{ old('aktivitas', $checkpoint->aktivitas) == 'OUTBOUND' ? 'selected' : '' }}>OUTBOUND
                            </option>
                        </select>
                    </div>

                    {{-- Gate --}}
                    <div>
                        <label for="gate" class="block text-sm font-medium text-gray-700 mb-1.5">Gate</label>
                        <select id="gate" name="gate"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="">{{ __('-- Pilih Gate --') }}</option>
                            <optgroup label="🧊 Frozen (F-1 s/d F-16)" id="gateGroupFrozen">
                                @for ($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" data-jenis="FROZEN"
                                        {{ old('gate', $checkpoint->gate) == $i ? 'selected' : '' }}>
                                        F-{{ $i }}
                                    </option>
                                @endfor
                            </optgroup>
                            <optgroup label="📦 Dry (D-1 s/d D-11)" id="gateGroupDry">
                                @for ($i = 17; $i <= 27; $i++)
                                    <option value="{{ $i }}" data-jenis="DRY"
                                        {{ old('gate', $checkpoint->gate) == $i ? 'selected' : '' }}>
                                        D-{{ $i - 16 }}
                                    </option>
                                @endfor
                            </optgroup>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                class="text-red-400">*</span></label>
                        <select id="status" name="status"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="START" {{ old('status', $checkpoint->status) == 'START' ? 'selected' : '' }}>
                                START</option>
                            <option value="ON LOADING"
                                {{ old('status', $checkpoint->status) == 'ON LOADING' ? 'selected' : '' }}>ON LOADING
                            </option>
                            <option value="FINISH" {{ old('status', $checkpoint->status) == 'FINISH' ? 'selected' : '' }}>
                                FINISH</option>
                            <option value="CANCEL" {{ old('status', $checkpoint->status) == 'CANCEL' ? 'selected' : '' }}>
                                CANCEL</option>
                        </select>
                    </div>

                    {{-- Waktu Start --}}
                    <div>
                        <label for="waktu_start"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Waktu Start') }}</label>
                        <input type="datetime-local" id="waktu_start" name="waktu_start"
                            value="{{ old('waktu_start', $checkpoint->waktu_start?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Waktu End --}}
                    <div>
                        <label for="waktu_end"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Waktu End') }}</label>
                        <input type="datetime-local" id="waktu_end" name="waktu_end"
                            value="{{ old('waktu_end', $checkpoint->waktu_end?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Durasi (Read-only, auto-calculated) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Durasi Loading') }}</label>
                        <div class="w-full px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-600">
                            @if ($checkpoint->waktu_start && $checkpoint->waktu_end)
                                @php
                                    $diff = $checkpoint->waktu_start->diff($checkpoint->waktu_end);
                                    $hours = $diff->days * 24 + $diff->h;
                                    $durasiDisplay = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
                                @endphp
                                {{ $durasiDisplay }}
                            @else
                                <span
                                    class="text-gray-400 italic">{{ __('Otomatis dihitung dari Start – End Loading') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- No. Surat Jalan & Purchase Order --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label for="no_surat_jalan"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('No. Surat Jalan') }}</label>
                        <input type="text" id="no_surat_jalan" name="no_surat_jalan"
                            value="{{ old('no_surat_jalan', $checkpoint->no_surat_jalan) }}"
                            placeholder="Masukkan no surat jalan (opsional)"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label for="purchase_order"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Purchase Order') }}</label>
                        <input type="text" id="purchase_order" name="purchase_order"
                            value="{{ old('purchase_order', $checkpoint->purchase_order) }}"
                            placeholder="Masukkan no purchase order (opsional)"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Note/Keterangan --}}
                <div class="mt-5">
                    <label for="note"
                        class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Catatan / Keterangan') }}</label>
                    <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan atau keterangan (opsional)..."
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none">{{ old('note', $checkpoint->note) }}</textarea>
                </div>

                <div class="mt-5">
                    <label for="cancel_note"
                        class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Catatan Cancel') }}</label>
                    <textarea id="cancel_note" name="cancel_note" rows="3" placeholder="Diisi jika status checkpoint CANCEL..."
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none">{{ old('cancel_note', $checkpoint->cancel_note) }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        {{ __('Simpan Perubahan') }}
                    </button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisBarangSelect = document.getElementById('jenis_barang');
            const gateSelect = document.getElementById('gate');
            const frozenGroup = document.getElementById('gateGroupFrozen');
            const dryGroup = document.getElementById('gateGroupDry');

            function filterGateOptions() {
                const jenis = jenisBarangSelect.value;

                if (jenis === 'FROZEN') {
                    frozenGroup.style.display = '';
                    dryGroup.style.display = 'none';
                    // Reset gate if current selection is a Dry gate
                    if (gateSelect.value && parseInt(gateSelect.value) >= 17) {
                        gateSelect.value = '';
                    }
                } else if (jenis === 'DRY') {
                    frozenGroup.style.display = 'none';
                    dryGroup.style.display = '';
                    // Reset gate if current selection is a Frozen gate
                    if (gateSelect.value && parseInt(gateSelect.value) <= 16) {
                        gateSelect.value = '';
                    }
                } else {
                    // CHILLED or others: show all gates
                    frozenGroup.style.display = '';
                    dryGroup.style.display = '';
                }
            }

            // Filter on page load (respect existing selection)
            filterGateOptions();

            // Filter when jenis_barang changes
            jenisBarangSelect.addEventListener('change', filterGateOptions);

            // Validation Waktu Penerimaan & Penyerahan Dokumen vs Tanggal
            const tanggalInput = document.getElementById('tanggal');
            const penerimaanInput = document.getElementById('waktu_penerimaan_dokumen');
            const penyerahanInput = document.getElementById('waktu_penyerahan_dokumen');
            const waktuStart = document.getElementById('waktu_start');
            const waktuEnd = document.getElementById('waktu_end');

            // Store initial values
            [penerimaanInput, penyerahanInput, waktuStart, waktuEnd].forEach(input => {
                if (input) input.dataset.default = input.value;
            });

            function updateMinDatetime() {
                if (tanggalInput.value) {
                    const minDatetime = tanggalInput.value + 'T00:00';
                    penerimaanInput.min = minDatetime;
                    penyerahanInput.min = minDatetime;
                    waktuStart.min = minDatetime;
                    waktuEnd.min = minDatetime;
                }
            }

            function validateDatetimeInput(inputElem, label) {
                if (tanggalInput.value && inputElem.value) {
                    const minDatetime = tanggalInput.value + 'T00:00';
                    if (inputElem.value < minDatetime) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: label + ' tidak boleh kurang dari Tanggal.',
                            confirmButtonColor: '#f97316'
                        });
                        inputElem.value = inputElem.dataset.default || '';
                    } else {
                        inputElem.dataset.default = inputElem.value;
                    }
                }
            }

            tanggalInput.addEventListener('change', function() {
                updateMinDatetime();
                validateDatetimeInput(penerimaanInput, 'Waktu Penerimaan Dokumen');
                validateDatetimeInput(penyerahanInput, 'Waktu Penyerahan Dokumen');
                validateDatetimeInput(waktuStart, 'Waktu Start');
                validateDatetimeInput(waktuEnd, 'Waktu End');
            });

            penerimaanInput.addEventListener('change', function() {
                validateDatetimeInput(this, 'Waktu Penerimaan Dokumen');
            });

            penyerahanInput.addEventListener('change', function() {
                validateDatetimeInput(this, 'Waktu Penyerahan Dokumen');
            });

            waktuStart.addEventListener('change', function() {
                validateDatetimeInput(this, 'Waktu Start');
            });

            waktuEnd.addEventListener('change', function() {
                validateDatetimeInput(this, 'Waktu End');
            });

            // Initialize on load
            updateMinDatetime();
        });
    </script>
@endsection
