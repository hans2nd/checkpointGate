@extends('layouts.app')

@section('title', 'Edit Checkpoint')

@section('content')
    <div class="max-w-4xl mx-auto">
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
                <h2 class="text-lg font-semibold text-gray-800">Edit Data Checkpoint</h2>
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
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span
                                class="text-red-400">*</span></label>
                        <input type="date" id="tanggal" name="tanggal"
                            value="{{ old('tanggal', $checkpoint->tanggal->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- No Polisi --}}
                    <div>
                        <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-1.5">No Polisi <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="no_polisi" name="no_polisi"
                            value="{{ old('no_polisi', $checkpoint->no_polisi) }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Vendor --}}
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="vendor" name="vendor" value="{{ old('vendor', $checkpoint->vendor) }}"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Driver --}}
                    <div>
                        <label for="driver" class="block text-sm font-medium text-gray-700 mb-1.5">Driver <span
                                class="text-red-400">*</span></label>
                        <input type="text" id="driver" name="driver" value="{{ old('driver', $checkpoint->driver) }}"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span
                                class="text-red-400">*</span></label>
                        <select id="tipe" name="tipe" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="EKSTERNAL" {{ old('tipe', $checkpoint->tipe) == 'EKSTERNAL' ? 'selected' : '' }}>
                                EKSTERNAL</option>
                            <option value="INTERNAL" {{ old('tipe', $checkpoint->tipe) == 'INTERNAL' ? 'selected' : '' }}>
                                INTERNAL</option>
                        </select>
                    </div>

                    {{-- Jenis Kendaraan --}}
                    <div>
                        <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis
                            Kendaraan</label>
                        <select id="jenis_kendaraan" name="jenis_kendaraan"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="">-- Pilih --</option>
                            @foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT', 'L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $jk)
                                <option value="{{ $jk }}"
                                    {{ old('jenis_kendaraan', $checkpoint->jenis_kendaraan) == $jk ? 'selected' : '' }}>
                                    {{ $jk }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Waktu Penerimaan Dokumen --}}
                    <div>
                        <label for="waktu_penerimaan_dokumen" class="block text-sm font-medium text-gray-700 mb-1.5">Waktu
                            Penerimaan Dokumen</label>
                        <input type="datetime-local" id="waktu_penerimaan_dokumen" name="waktu_penerimaan_dokumen"
                            value="{{ old('waktu_penerimaan_dokumen', $checkpoint->waktu_penerimaan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Waktu Penyerahan Dokumen --}}
                    <div>
                        <label for="waktu_penyerahan_dokumen" class="block text-sm font-medium text-gray-700 mb-1.5">Waktu
                            Penyerahan Dokumen</label>
                        <input type="datetime-local" id="waktu_penyerahan_dokumen" name="waktu_penyerahan_dokumen"
                            value="{{ old('waktu_penyerahan_dokumen', $checkpoint->waktu_penyerahan_dokumen?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Jenis Barang --}}
                    <div>
                        <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Barang <span
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
                        <label for="aktivitas" class="block text-sm font-medium text-gray-700 mb-1.5">Aktivitas <span
                                class="text-red-400">*</span></label>
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
                        <input type="number" id="gate" name="gate"
                            value="{{ old('gate', $checkpoint->gate) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                class="text-red-400">*</span></label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                            <option value="START" {{ old('status', $checkpoint->status) == 'START' ? 'selected' : '' }}>
                                START</option>
                            <option value="FINISH" {{ old('status', $checkpoint->status) == 'FINISH' ? 'selected' : '' }}>
                                FINISH</option>
                        </select>
                    </div>

                    {{-- Waktu Start --}}
                    <div>
                        <label for="waktu_start" class="block text-sm font-medium text-gray-700 mb-1.5">Waktu
                            Start</label>
                        <input type="datetime-local" id="waktu_start" name="waktu_start"
                            value="{{ old('waktu_start', $checkpoint->waktu_start?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Waktu End --}}
                    <div>
                        <label for="waktu_end" class="block text-sm font-medium text-gray-700 mb-1.5">Waktu End</label>
                        <input type="datetime-local" id="waktu_end" name="waktu_end"
                            value="{{ old('waktu_end', $checkpoint->waktu_end?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Durasi --}}
                    <div>
                        <label for="durasi" class="block text-sm font-medium text-gray-700 mb-1.5">Durasi</label>
                        <input type="text" id="durasi" name="durasi"
                            value="{{ old('durasi', $checkpoint->durasi) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Note/Keterangan --}}
                <div class="mt-5">
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan atau keterangan (opsional)..."
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none">{{ old('note', $checkpoint->note) }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
