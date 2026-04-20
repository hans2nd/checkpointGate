@extends('layouts.app')

@section('title', 'Detail Checkpoint')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('checkpoints.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="flex items-center gap-2">
            @if(Auth::user()->hasPermission('checkpoint.edit'))
            <a href="{{ route('checkpoints.edit', $checkpoint) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @endif
            @if(Auth::user()->hasPermission('checkpoint.delete'))
            <form method="POST" action="{{ route('checkpoints.destroy', $checkpoint) }}" onsubmit="return confirm('Yakin hapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $checkpoint->no_polisi }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Record #{{ $checkpoint->id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                    {{ $checkpoint->aktivitas === 'INBOUND' ? 'bg-orange-50 text-orange-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $checkpoint->aktivitas }}
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                    {{ $checkpoint->status === 'CANCEL' ? 'bg-red-50 text-red-700' : ($checkpoint->status === 'FINISH' ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700') }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $checkpoint->status === 'CANCEL' ? 'bg-red-500' : ($checkpoint->status === 'FINISH' ? 'bg-blue-500' : 'bg-yellow-500') }}"></span>
                    {{ $checkpoint->status }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Each detail item --}}
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tanggal</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->tanggal->format('d F Y') }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">No Polisi</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->no_polisi }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Vendor</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->vendor }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Driver</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->driver }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tipe</p>
                    <p class="text-sm mt-1">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $checkpoint->tipe === 'INTERNAL' ? 'bg-purple-50 text-purple-700' : 'bg-sky-50 text-sky-700' }}">
                            {{ $checkpoint->tipe }}
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Jenis Kendaraan</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->jenis_kendaraan ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu Penerimaan Dokumen</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->waktu_penerimaan_dokumen?->format('d/m/Y H:i:s') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu Penyerahan Dokumen</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->waktu_penyerahan_dokumen?->format('d/m/Y H:i:s') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Durasi Dokumen</p>
                    <p class="text-sm text-gray-900 mt-1 font-mono font-medium">
                        @if($checkpoint->waktu_penerimaan_dokumen && $checkpoint->waktu_penyerahan_dokumen)
                            @php
                                $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
                                $hours = ($diff->days * 24) + $diff->h;
                            @endphp
                            {{ sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s) }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Jenis Barang</p>
                    <p class="text-sm mt-1">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $checkpoint->jenis_barang === 'FROZEN' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700' }}">
                            {{ $checkpoint->jenis_barang }}
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Gate</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->gate ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu Start</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->waktu_start?->format('d/m/Y H:i:s') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu End</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->waktu_end?->format('d/m/Y H:i:s') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Durasi</p>
                    <p class="text-sm text-gray-900 mt-1 font-mono font-medium">{{ $checkpoint->durasi ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dibuat Oleh</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->createdByUser?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Start Loading Oleh</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->startedByUser?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Cancel Oleh</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->canceledByUser?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu Cancel</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->canceled_at?->format('d/m/Y H:i:s') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dibuat</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->created_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Diperbarui</p>
                    <p class="text-sm text-gray-900 mt-1 font-medium">{{ $checkpoint->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>

            @if($checkpoint->note)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Catatan / Keterangan</p>
                <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $checkpoint->note }}</p>
            </div>
            @endif

            @if($checkpoint->cancel_note)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Catatan Cancel</p>
                <p class="text-sm text-red-700 bg-red-50 rounded-lg p-3">{{ $checkpoint->cancel_note }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
