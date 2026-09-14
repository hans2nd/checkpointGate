@extends('layouts.app')

@section('title', __('Detail Checkpoint'))

@section('content')
<style>
    .premium-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 32px 40px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .premium-hero::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .detail-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        padding: 24px;
        height: 100%;
    }
    .detail-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
    }
    .action-btn-back {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .action-btn-back:hover {
        background: rgba(255,255,255,0.2);
    }
    .action-btn-edit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border: none;
    }
    .action-btn-edit:hover {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    .action-btn-delete {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .action-btn-delete:hover {
        background: #fee2e2;
    }
</style>

    <div class="max-w-6xl mx-auto space-y-6">
        
        {{-- Premium Hero Header --}}
        <div class="premium-hero flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a href="{{ route('checkpoints.index') }}" class="inline-flex items-center gap-2 text-sm font-medium mb-4 action-btn-back px-4 py-2 rounded-lg transition-all backdrop-blur-sm w-max">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    {{ __('Kembali ke Data') }}
                </a>
                <div class="flex items-center gap-4 flex-wrap">
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">{{ $checkpoint->no_polisi }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $checkpoint->aktivitas === 'INBOUND' ? 'bg-orange-500 text-white' : 'bg-amber-500 text-white' }}">
                        {{ $checkpoint->aktivitas }}
                    </span>
                    @php
                        $statusClass = 'bg-yellow-500/20 text-yellow-300';
                        $dotClass = 'bg-yellow-400';
                        if ($checkpoint->status === 'CANCEL') {
                            $statusClass = 'bg-red-500/20 text-red-300'; $dotClass = 'bg-red-400';
                        } elseif ($checkpoint->status === 'FINISH') {
                            $statusClass = 'bg-emerald-500/20 text-emerald-300'; $dotClass = 'bg-emerald-400';
                        } elseif ($checkpoint->status === 'ON LOADING') {
                            $statusClass = 'bg-blue-500/20 text-blue-300'; $dotClass = 'bg-blue-400 animate-pulse';
                        } elseif (in_array($checkpoint->status, ['WAITING', 'READY'])) {
                            $statusClass = 'bg-indigo-500/20 text-indigo-300'; $dotClass = 'bg-indigo-400';
                        } elseif ($checkpoint->status === 'DOC IN') {
                            $statusClass = 'bg-cyan-500/20 text-cyan-300'; $dotClass = 'bg-cyan-400';
                        } elseif ($checkpoint->status === 'PARKING') {
                            $statusClass = 'bg-gray-500/30 text-gray-300'; $dotClass = 'bg-gray-400';
                        }
                    @endphp
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold border border-white/10 {{ $statusClass }}">
                        <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
                        {{ $checkpoint->status }}
                    </span>
                </div>
                <p class="text-slate-400 mt-2 text-sm font-medium">Record ID #{{ $checkpoint->id }} &bull; Dibuat pada {{ $checkpoint->created_at->format('d M Y, H:i') }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if (Auth::user()->hasPermission('checkpoint.edit'))
                    <a href="{{ route('checkpoints.edit', $checkpoint) }}" class="inline-flex items-center gap-2 px-5 py-2.5 action-btn-edit rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit Data
                    </a>
                @endif
                @if (Auth::user()->hasPermission('checkpoint.delete'))
                    <form method="POST" action="{{ route('checkpoints.destroy', $checkpoint) }}" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 action-btn-delete rounded-xl text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Informasi Kendaraan --}}
            <div class="detail-card">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Informasi Logistik</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="detail-label">{{ __('Vendor') }}</p>
                        <p class="detail-value">{{ $checkpoint->vendor }}</p>
                    </div>
                    <div>
                        <p class="detail-label">{{ __('Driver') }}</p>
                        <p class="detail-value">{{ $checkpoint->driver }}</p>
                    </div>
                    <div>
                        <p class="detail-label">{{ __('Jenis Kendaraan') }}</p>
                        <p class="detail-value">{{ $checkpoint->jenis_kendaraan ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">{{ __('Tipe') }}</p>
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold {{ $checkpoint->tipe === 'INTERNAL' ? 'bg-purple-100 text-purple-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ $checkpoint->tipe }}
                            </span>
                        </div>
                        <div>
                            <p class="detail-label">{{ __('Jenis Barang') }}</p>
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold {{ $checkpoint->jenis_barang === 'FROZEN' ? 'bg-cyan-100 text-cyan-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $checkpoint->jenis_barang }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">{{ __('Type Of Load') }}</p>
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-100 text-indigo-700">
                                {{ $checkpoint->type_of_load ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <p class="detail-label">{{ __('Shipping Type') }}</p>
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold bg-teal-100 text-teal-700">
                                {{ $checkpoint->shipping_type ?? '-' }}
                            </span>
                        </div>
                    </div>
                    @if ($checkpoint->type_of_load === 'Full' && $checkpoint->productCategory)
                    <div>
                        <p class="detail-label">{{ __('Category Product') }}</p>
                        <p class="detail-value">{{ $checkpoint->productCategory->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Timeline Dokumen --}}
            <div class="detail-card">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Dokumen & Gate</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="detail-label">Gate Ditugaskan</p>
                        <div class="inline-flex items-center justify-center px-4 py-1.5 bg-gray-100 border border-gray-200 rounded-lg font-bold text-gray-700">
                            {{ $checkpoint->formatted_gate }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Penerimaan Dokumen</p>
                            <p class="detail-value text-sm">{{ $checkpoint->waktu_penerimaan_dokumen?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Penyerahan Dokumen</p>
                            <p class="detail-value text-sm">{{ $checkpoint->waktu_penyerahan_dokumen?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Waiting Time</p>
                            <p class="detail-value font-mono text-orange-600">
                                @if ($checkpoint->waktu_penerimaan_dokumen)
                                    @php
                                        $waitDiff = $checkpoint->created_at->diff($checkpoint->waktu_penerimaan_dokumen);
                                        $waitHours = $waitDiff->days * 24 + $waitDiff->h;
                                    @endphp
                                    {{ sprintf('%02d:%02d:%02d', $waitHours, $waitDiff->i, $waitDiff->s) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="detail-label">Total Durasi Dokumen</p>
                            <p class="detail-value font-mono text-blue-600">
                                @if ($checkpoint->waktu_penerimaan_dokumen && $checkpoint->waktu_penyerahan_dokumen)
                                    @php
                                        $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
                                        $hours = $diff->days * 24 + $diff->h;
                                    @endphp
                                    {{ sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline Loading --}}
            <div class="detail-card">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Waktu Loading</h3>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Start Loading</p>
                            <p class="detail-value text-sm">{{ $checkpoint->waktu_start?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">End Loading</p>
                            <p class="detail-value text-sm">{{ $checkpoint->waktu_end?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="detail-label">Durasi Loading</p>
                        <p class="detail-value font-mono text-emerald-600 text-lg">{{ $checkpoint->durasi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Waktu Kendaraan Keluar</p>
                        <p class="detail-value text-sm">{{ $checkpoint->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Additional References (Surat Jalan, etc) --}}
        @if ($checkpoint->no_surat_jalan || $checkpoint->purchase_order || $checkpoint->receipt_number || $checkpoint->note || $checkpoint->cancel_note)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if ($checkpoint->no_surat_jalan || $checkpoint->purchase_order || $checkpoint->receipt_number)
            <div class="detail-card">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Referensi Dokumen</h3>
                <div class="space-y-4">
                    @if($checkpoint->no_surat_jalan)
                    <div>
                        <p class="detail-label">No. Surat Jalan</p>
                        <p class="detail-value font-mono bg-gray-50 p-2 rounded-lg border border-gray-100 text-sm">{{ $checkpoint->no_surat_jalan }}</p>
                    </div>
                    @endif
                    @if($checkpoint->purchase_order)
                    <div>
                        <p class="detail-label">
                            {{ $checkpoint->aktivitas === 'INBOUND' ? 'PO VENDOR' : ($checkpoint->aktivitas === 'OUTBOUND' ? 'PO CUSTOMER' : 'Purchase Order') }}
                        </p>
                        <p class="detail-value font-mono bg-gray-50 p-2 rounded-lg border border-gray-100 text-sm">{{ $checkpoint->purchase_order }}</p>
                    </div>
                    @endif
                    @if($checkpoint->receipt_number)
                    <div>
                        <p class="detail-label">Receipt Number</p>
                        <p class="detail-value font-mono bg-gray-50 p-2 rounded-lg border border-gray-100 text-sm">{{ $checkpoint->receipt_number }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if ($checkpoint->note || $checkpoint->cancel_note)
            <div class="detail-card">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Catatan Tambahan</h3>
                <div class="space-y-4">
                    @if($checkpoint->note)
                    <div>
                        <p class="detail-label">Catatan Umum</p>
                        <p class="text-sm text-gray-700 bg-amber-50/50 rounded-lg p-3 border border-amber-100 leading-relaxed">{{ $checkpoint->note }}</p>
                    </div>
                    @endif
                    @if($checkpoint->cancel_note)
                    <div>
                        <p class="detail-label text-red-500">Catatan Cancel</p>
                        <p class="text-sm text-red-700 bg-red-50 rounded-lg p-3 border border-red-100 leading-relaxed">{{ $checkpoint->cancel_note }}</p>
                        @if($checkpoint->canceledByUser)
                            <p class="text-xs text-red-400 mt-2">Dibatalkan oleh: {{ $checkpoint->canceledByUser->name }} pada {{ $checkpoint->canceled_at?->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Photo Identity --}}
        @if ($checkpoint->foto_identitas)
        <div class="detail-card">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Foto Identitas (SIM/KTP)</h3>
            <div class="mt-2">
                <a href="{{ asset('storage/' . $checkpoint->foto_identitas) }}" target="_blank" class="inline-block relative group">
                    <img src="{{ asset('storage/' . $checkpoint->foto_identitas) }}" alt="Foto Identitas" class="max-w-md w-full rounded-xl shadow-sm border border-gray-200 group-hover:shadow-md transition-all">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-xl transition-all">
                        <span class="text-white text-sm font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                            Lihat Gambar Penuh
                        </span>
                    </div>
                </a>
            </div>
        </div>
        @endif
        
        {{-- Log Aktivitas / Dibuat oleh --}}
        <div class="text-center text-xs font-medium text-gray-400 mt-8 mb-4">
            <p>Dibuat oleh: <span class="text-gray-600">{{ $checkpoint->createdByUser?->name ?? 'System' }}</span> &bull; 
               Penerima Dokumen: <span class="text-gray-600">{{ $checkpoint->receivedByUser?->name ?? '-' }}</span> &bull; 
               Start Loading: <span class="text-gray-600">{{ $checkpoint->startedByUser?->name ?? '-' }}</span></p>
        </div>

    </div>
@endsection
