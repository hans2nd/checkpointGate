@extends('layouts.app')

@section('title', __('Edit Checkpoint'))

@section('content')
<style>
    .premium-form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 24px 32px;
        color: white;
        position: relative;
    }
    .form-header::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .form-group label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: block;
    }
    .premium-input {
        width: 100%;
        padding: 10px 14px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .premium-input:focus {
        background-color: #fff;
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        outline: none;
    }
</style>

    <div class="max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('checkpoints.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-orange-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm w-max">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali ke Data Checkpoint') }}
            </a>
        </div>

        <div class="premium-form-card">
            <div class="form-header flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ __('Edit Data Checkpoint') }}</h2>
                    <p class="text-slate-300 mt-1 text-sm font-medium">Record ID #{{ $checkpoint->id }} &bull; {{ $checkpoint->no_polisi }}</p>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/20 text-white backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full {{ $checkpoint->status === 'FINISH' ? 'bg-emerald-400' : 'bg-orange-400' }}"></span>
                        {{ $checkpoint->status }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('checkpoints.update', $checkpoint) }}" class="p-8" enctype="multipart/form-data">
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

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Tanggal --}}
                    <div class="form-group">
                        <label for="tanggal">{{ __('Tanggal') }} <span class="text-orange-500">*</span></label>
                        <input type="date" id="tanggal" name="tanggal"
                            value="{{ old('tanggal', $checkpoint->tanggal->format('Y-m-d')) }}" required
                            class="premium-input">
                    </div>

                    {{-- No Polisi --}}
                    <div class="form-group">
                        <label for="no_polisi">{{ __('No Polisi') }} <span class="text-orange-500">*</span></label>
                        <input type="text" id="no_polisi" name="no_polisi"
                            value="{{ old('no_polisi', $checkpoint->no_polisi) }}" required
                            class="premium-input">
                    </div>

                    {{-- Vendor --}}
                    <div class="form-group">
                        <label for="vendor">Vendor <span class="text-orange-500">*</span></label>
                        <input type="text" id="vendor" name="vendor"
                            value="{{ old('vendor', $checkpoint->vendor) }}" required
                            class="premium-input">
                    </div>

                    {{-- Driver --}}
                    <div class="form-group">
                        <label for="driver">Driver <span class="text-orange-500">*</span></label>
                        <input type="text" id="driver" name="driver"
                            value="{{ old('driver', $checkpoint->driver) }}" required
                            class="premium-input">
                    </div>

                    {{-- Tipe --}}
                    <div class="form-group">
                        <label for="tipe">Tipe <span class="text-orange-500">*</span></label>
                        <select id="tipe" name="tipe" required class="premium-input bg-white">
                            <option value="EKSTERNAL" {{ old('tipe', $checkpoint->tipe) == 'EKSTERNAL' ? 'selected' : '' }}>EKSTERNAL</option>
                            <option value="INTERNAL" {{ old('tipe', $checkpoint->tipe) == 'INTERNAL' ? 'selected' : '' }}>INTERNAL</option>
                        </select>
                    </div>

                    {{-- Jenis Kendaraan --}}
                    <div class="form-group">
                        <label for="jenis_kendaraan">{{ __('Jenis Kendaraan') }}</label>
                        <select id="jenis_kendaraan" name="jenis_kendaraan" class="premium-input bg-white">
                            <option value="">{{ __('-- Pilih --') }}</option>
                            @foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT', 'L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $jk)
                                <option value="{{ $jk }}"
                                    {{ old('jenis_kendaraan', $checkpoint->jenis_kendaraan) == $jk ? 'selected' : '' }}>
                                    {{ $jk }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Waktu Penerimaan Dokumen --}}
                    <div class="form-group">
                        <label for="waktu_penerimaan_dokumen">{{ __('Waktu Penerimaan Dokumen') }}</label>
                        <input type="datetime-local" id="waktu_penerimaan_dokumen" name="waktu_penerimaan_dokumen"
                            value="{{ old('waktu_penerimaan_dokumen', $checkpoint->waktu_penerimaan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="premium-input">
                    </div>

                    {{-- Waktu Penyerahan Dokumen --}}
                    <div class="form-group">
                        <label for="waktu_penyerahan_dokumen">{{ __('Waktu Penyerahan Dokumen') }}</label>
                        <input type="datetime-local" id="waktu_penyerahan_dokumen" name="waktu_penyerahan_dokumen"
                            value="{{ old('waktu_penyerahan_dokumen', $checkpoint->waktu_penyerahan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="premium-input">
                    </div>

                    {{-- Jenis Barang --}}
                    <div class="form-group">
                        <label for="jenis_barang">{{ __('Jenis Barang') }} <span class="text-orange-500">*</span></label>
                        <select id="jenis_barang" name="jenis_barang" required class="premium-input bg-white">
                            <option value="FROZEN" {{ old('jenis_barang', $checkpoint->jenis_barang) == 'FROZEN' ? 'selected' : '' }}>FROZEN</option>
                            <option value="DRY" {{ old('jenis_barang', $checkpoint->jenis_barang) == 'DRY' ? 'selected' : '' }}>DRY</option>
                            <option value="CHILLED" {{ old('jenis_barang', $checkpoint->jenis_barang) == 'CHILLED' ? 'selected' : '' }}>CHILLED</option>
                        </select>
                    </div>

                    {{-- Aktivitas --}}
                    <div class="form-group">
                        <label for="aktivitas">{{ __('Aktivitas') }} <span class="text-orange-500">*</span></label>
                        <select id="aktivitas" name="aktivitas" required class="premium-input bg-white">
                            <option value="INBOUND" {{ old('aktivitas', $checkpoint->aktivitas) == 'INBOUND' ? 'selected' : '' }}>INBOUND</option>
                            <option value="OUTBOUND" {{ old('aktivitas', $checkpoint->aktivitas) == 'OUTBOUND' ? 'selected' : '' }}>OUTBOUND</option>
                        </select>
                    </div>

                    {{-- Gate --}}
                    <div class="form-group">
                        <label for="gate">Gate</label>
                        <select id="gate" name="gate" class="premium-input bg-white">
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
                    <div class="form-group">
                        <label for="status">Status <span class="text-orange-500">*</span></label>
                        <select id="status" name="status" class="premium-input bg-white">
                            @foreach(['START', 'PARKING', 'DOC IN', 'ASSIGN GATE', 'WAITING', 'READY', 'ON LOADING', 'FINISH', 'CANCEL', 'COMPLETED'] as $st)
                                <option value="{{ $st }}" {{ old('status', $checkpoint->status) == $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Waktu Start --}}
                    <div class="form-group">
                        <label for="waktu_start">{{ __('Waktu Start') }}</label>
                        <input type="datetime-local" id="waktu_start" name="waktu_start"
                            value="{{ old('waktu_start', $checkpoint->waktu_start?->format('Y-m-d\TH:i')) }}"
                            class="premium-input">
                    </div>

                    {{-- Waktu End --}}
                    <div class="form-group">
                        <label for="waktu_end">{{ __('Waktu End') }}</label>
                        <input type="datetime-local" id="waktu_end" name="waktu_end"
                            value="{{ old('waktu_end', $checkpoint->waktu_end?->format('Y-m-d\TH:i')) }}"
                            class="premium-input">
                    </div>

                    {{-- Durasi (Read-only, auto-calculated) --}}
                    <div class="form-group">
                        <label>{{ __('Durasi Loading') }}</label>
                        <div class="premium-input bg-gray-50 text-gray-500 border-dashed">
                            @if ($checkpoint->waktu_start && $checkpoint->waktu_end)
                                @php
                                    $diff = $checkpoint->waktu_start->diff($checkpoint->waktu_end);
                                    $hours = $diff->days * 24 + $diff->h;
                                    $durasiDisplay = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
                                @endphp
                                {{ $durasiDisplay }}
                            @else
                                <span class="italic text-xs">{{ __('Otomatis dihitung dari Start – End Loading') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-100">

                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('Informasi Tambahan') }}</h3>
                
                {{-- No. Surat Jalan & Purchase Order --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="no_surat_jalan">{{ __('No. Surat Jalan') }}</label>
                        <input type="text" id="no_surat_jalan" name="no_surat_jalan"
                            value="{{ old('no_surat_jalan', $checkpoint->no_surat_jalan) }}"
                            placeholder="Masukkan no surat jalan (opsional)" class="premium-input">
                    </div>
                    <div class="form-group">
                        <label for="purchase_order">{{ __('Purchase Order') }}</label>
                        <input type="text" id="purchase_order" name="purchase_order"
                            value="{{ old('purchase_order', $checkpoint->purchase_order) }}"
                            placeholder="Masukkan no purchase order (opsional)" class="premium-input">
                    </div>
                </div>

                {{-- Note/Keterangan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="note">{{ __('Catatan / Keterangan') }}</label>
                        <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan atau keterangan (opsional)..."
                            class="premium-input resize-none">{{ old('note', $checkpoint->note) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="cancel_note">{{ __('Catatan Cancel') }}</label>
                        <textarea id="cancel_note" name="cancel_note" rows="3" placeholder="Diisi jika status checkpoint CANCEL..."
                            class="premium-input resize-none">{{ old('cancel_note', $checkpoint->cancel_note) }}</textarea>
                    </div>
                </div>

                {{-- Foto Identitas (SIM/KTP) --}}
                <div class="form-group mb-6">
                    <label for="foto_identitas">{{ __('Foto Identitas (SIM/KTP) - Opsional') }}</label>
                    
                    @if ($checkpoint->foto_identitas)
                        <div class="mb-4 bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-start gap-4">
                            <img src="{{ asset('storage/' . $checkpoint->foto_identitas) }}" alt="Foto Identitas" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Foto saat ini terlampir.</p>
                                <label class="inline-flex items-center cursor-pointer mt-2">
                                    <input type="checkbox" name="remove_foto" value="1" class="rounded border-gray-300 text-red-500 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 w-4 h-4">
                                    <span class="ml-2 text-sm font-medium text-red-600">Hapus lampiran ini</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <input type="file" id="foto_identitas" name="foto_identitas" accept="image/*"
                        class="premium-input bg-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                    <p class="mt-2 text-xs text-slate-400">Maksimal ukuran file 5MB (Format: JPG, PNG, GIF). Pilih file baru untuk mengganti yang lama.</p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        {{ __('Simpan Perubahan') }}
                    </button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-8 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors">
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
