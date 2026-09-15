@extends('layouts.app')

@section('title', __('Report Checkpoint'))

@section('content')
<style>
    /* ── Report Page Premium Styles ── */
    .filter-actions .btn-export {
        padding: 9px 16px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }
    .filter-actions .btn-export:hover {
        box-shadow: 0 4px 12px rgba(16,185,129,0.35);
        transform: translateY(-1px);
    }

    /* ── Filter Card ── */
    .report-filter-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        padding: 24px;
    }
    .report-filter-card .filter-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }
    .report-filter-card .filter-header .filter-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border-radius: 10px;
        color: #ea580c;
    }
    .report-filter-card .filter-header h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    .report-filter-card .filter-header h3 span {
        font-size: 0.7rem;
        font-weight: 500;
        color: #94a3b8;
        display: block;
        margin-top: 1px;
    }
    .report-filter-card label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }
    .report-filter-card input[type="date"],
    .report-filter-card input[type="text"],
    .report-filter-card select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.82rem;
        color: #334155;
        background: #f8fafc;
        transition: all 0.2s;
        outline: none;
    }
    .report-filter-card input:focus,
    .report-filter-card select:focus {
        border-color: #f97316;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }
    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }
    .filter-actions .btn-filter {
        flex: 1;
        padding: 9px 16px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .filter-actions .btn-filter:hover {
        box-shadow: 0 4px 12px rgba(249,115,22,0.35);
        transform: translateY(-1px);
    }
    .filter-actions .btn-reset {
        padding: 9px 12px;
        background: #f1f5f9;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .filter-actions .btn-reset:hover {
        background: #e2e8f0;
        color: #334155;
    }

    /* ── Summary Cards Row ── */
    .report-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
    }
    @media (max-width: 1024px) {
        .report-summary-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 640px) {
        .report-summary-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .summary-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        padding: 18px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.25s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }
    .summary-card .card-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .summary-card .card-icon-wrap svg {
        width: 22px;
        height: 22px;
    }
    .summary-card .card-info .card-value {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .summary-card .card-info .card-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* ── Active Filter Tags ── */
    .active-filters-bar {
        display: none;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        font-size: 0.75rem;
    }
    .active-filters-bar.has-filters { display: flex; }
    .active-filters-bar .tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        background: #fff;
        border: 1px solid #f59e0b;
        color: #92400e;
        border-radius: 20px;
        font-weight: 600;
    }

    /* ── Table wrapper enhancements ── */
    .report-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .report-table-card .table-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .report-table-card .table-header-bar .table-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .report-table-card .table-header-bar .table-title .count-badge {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
    }
</style>

    <div class="space-y-5">

        {{-- Filter Form --}}
        <div class="report-filter-card">
            <div class="filter-header">
                <div class="filter-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </div>
                <h3>{{ __('Filter') }} &amp; {{ __('Search (License Plate, Vendor, Delivery Note, PO, Note)') }}<span>{{ __('Laporan data checkpoint berdasarkan periode dan filter') }}</span></h3>
            </div>
            <form method="GET" action="{{ route('report.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-8 gap-4 items-end">
                <div>
                    <label>{{ __('Tanggal Mulai') }}</label>
                    <input type="date" name="start_date" value="{{ $startDate }}">
                </div>
                <div>
                    <label>{{ __('Tanggal Akhir') }}</label>
                    <input type="date" name="end_date" value="{{ $endDate }}">
                </div>
                <div>
                    <label>{{ __('Aktivitas') }}</label>
                    <select name="aktivitas">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="INBOUND" {{ $aktivitas === 'INBOUND' ? 'selected' : '' }}>INBOUND</option>
                        <option value="OUTBOUND" {{ $aktivitas === 'OUTBOUND' ? 'selected' : '' }}>OUTBOUND</option>
                    </select>
                </div>
                <div>
                    <label>{{ __('Jenis Barang') }}</label>
                    <select name="jenis_barang">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="FROZEN" {{ $jenisBarang === 'FROZEN' ? 'selected' : '' }}>FROZEN</option>
                        <option value="DRY" {{ $jenisBarang === 'DRY' ? 'selected' : '' }}>DRY</option>
                        <option value="CHILLED" {{ $jenisBarang === 'CHILLED' ? 'selected' : '' }}>CHILLED</option>
                    </select>
                </div>
                <div>
                    <label>{{ __('Status') }}</label>
                    <select name="status">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="PARKING" {{ $status === 'PARKING' ? 'selected' : '' }}>PARKING</option>
                        <option value="START" {{ $status === 'START' ? 'selected' : '' }}>START</option>
                        <option value="ON LOADING" {{ $status === 'ON LOADING' ? 'selected' : '' }}>ON LOADING</option>
                        <option value="FINISH" {{ $status === 'FINISH' ? 'selected' : '' }}>FINISH</option>
                        <option value="CANCEL" {{ $status === 'CANCEL' ? 'selected' : '' }}>CANCEL</option>
                        <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label>{{ __('Search keywords...') }}</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="{{ __('Search keywords...') }}">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('report.index') }}" class="btn-reset" title="{{ __('Reset') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                    @if (Auth::user()->hasPermission('report.export'))
                        <a href="{{ route('report.export', request()->query()) }}" class="btn-export" title="{{ __('Export Excel') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('Export') }}</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Active Filter Tags (only visible when filters are applied) --}}
        @php
            $hasActiveFilter = $aktivitas || $jenisBarang || $status || $search;
        @endphp
        <div class="active-filters-bar {{ $hasActiveFilter ? 'has-filters' : '' }}">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            <span class="text-amber-700 font-bold text-xs uppercase tracking-wider">{{ __('Difilter Berdasarkan') }}:</span>
            <span class="text-amber-600 text-xs">
                {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </span>
            @if($aktivitas)<span class="tag">{{ __('Aktivitas') }}: {{ $aktivitas }}</span>@endif
            @if($jenisBarang)<span class="tag">{{ __('Jenis Barang') }}: {{ $jenisBarang }}</span>@endif
            @if($status)<span class="tag">{{ __('Status') }}: {{ $status }}</span>@endif
            @if($search)<span class="tag">🔍 {{ $search }}</span>@endif
        </div>

        {{-- Summary Cards --}}
        <div class="report-summary-grid">
            <div class="summary-card">
                <div class="card-icon-wrap" style="background: linear-gradient(135deg, #fff7ed, #fed7aa);">
                    <svg style="color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div class="card-info">
                    <div class="card-value" style="color: #ea580c;">{{ number_format($totalKendaraan) }}</div>
                    <div class="card-label">{{ __('Total Kendaraan') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon-wrap" style="background: linear-gradient(135deg, #ecfdf5, #a7f3d0);">
                    <svg style="color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="card-info">
                    <div class="card-value" style="color: #059669;">{{ number_format($totalFinish) }}</div>
                    <div class="card-label">{{ __('Finish') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon-wrap" style="background: linear-gradient(135deg, #eff6ff, #bfdbfe);">
                    <svg style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="card-info">
                    <div class="card-value" style="color: #2563eb;">{{ number_format($totalOnLoading) }}</div>
                    <div class="card-label">{{ __('On Loading') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon-wrap" style="background: linear-gradient(135deg, #fef2f2, #fecaca);">
                    <svg style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div class="card-info">
                    <div class="card-value" style="color: #dc2626;">{{ number_format($totalCancel) }}</div>
                    <div class="card-label">{{ __('Cancel') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon-wrap" style="background: linear-gradient(135deg, #faf5ff, #e9d5ff);">
                    <svg style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="card-info">
                    <div class="card-value font-mono" style="color: #7c3aed; font-size: 1.1rem;">{{ $avgDurasi ?? '-' }}</div>
                    <div class="card-label">{{ __('Avg Durasi Loading') }}</div>
                </div>
            </div>
        </div>


        {{-- Data Table --}}
        <div class="report-table-card">
            <div class="table-header-bar">
                <div class="table-title">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ __('Data Checkpoint') }}
                    <span class="count-badge">{{ $checkpoints->total() }} {{ __('record') }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #f97316, #ea580c);" class="text-left">
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('No') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Tanggal') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('No Polisi') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Vendor') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Driver') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Tipe') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Jenis Kendaraan') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Jenis Barang') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Aktivitas') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Gate') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Status') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Dibuat') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Penerimaan Dokumen') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Start Loading') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu End Loading') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Penyerahan Dokumen') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Keluar') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Tunggu') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Durasi Loading/Unloading') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Durasi Dokumen') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Waktu Cancel') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                {{ __('Diperbarui') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('No. Surat Jalan') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Purchase Order') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Receipt Number') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Dibuat Oleh') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Diterima Oleh') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Start Loading Oleh') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Cancel Oleh') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Note') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Type Of Load') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Category Product') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Shipping Type') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Is Cross Dock') }}</th>
                            <th
                                class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">
                                {{ __('Is Generated Cross Dock') }}</th>


                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($checkpoints as $index => $cp)
                            @php
                                $gateLabel = '-';
                                if ($cp->gate) {
                                    if ($cp->gate >= 1 && $cp->gate <= 16) {
                                        $gateLabel = 'F-' . $cp->gate;
                                    } elseif ($cp->gate >= 17 && $cp->gate <= 27) {
                                        $gateLabel = 'D-' . ($cp->gate - 16);
                                    } else {
                                        $gateLabel = 'Gate-' . $cp->gate;
                                    }
                                }
                                $waktuTunggu = '';
                                if ($cp->created_at && $cp->waktu_penerimaan_dokumen) {
                                    $diffTunggu = $cp->created_at->diff($cp->waktu_penerimaan_dokumen);
                                    $hoursTunggu = $diffTunggu->days * 24 + $diffTunggu->h;
                                    $waktuTunggu = sprintf(
                                        '%02d:%02d:%02d',
                                        $hoursTunggu,
                                        $diffTunggu->i,
                                        $diffTunggu->s,
                                    );
                                }
                                $durasiDok = '';
                                if ($cp->waktu_penerimaan_dokumen && $cp->waktu_penyerahan_dokumen) {
                                    $diffDok = $cp->waktu_penerimaan_dokumen->diff($cp->waktu_penyerahan_dokumen);
                                    $hoursDok = $diffDok->days * 24 + $diffDok->h;
                                    $durasiDok = sprintf('%02d:%02d:%02d', $hoursDok, $diffDok->i, $diffDok->s);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2.5 text-gray-500 text-xs">
                                    {{ ($checkpoints->currentPage() - 1) * $checkpoints->perPage() + $index + 1 }}</td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs whitespace-nowrap">
                                    {{ $cp->tanggal ? $cp->tanggal->format('d/m/Y') : '—' }}</td>
                                <td class="px-3 py-2.5 font-medium text-gray-900 text-xs whitespace-nowrap">
                                    {{ $cp->no_polisi }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->vendor }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->driver }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->tipe }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->jenis_kendaraan }}
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->jenis_barang === 'FROZEN' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700' }}">{{ $cp->jenis_barang }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->aktivitas }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-center text-xs">{{ $gateLabel }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if ($cp->status === 'CANCEL')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> CANCEL
                                        </span>
                                    @elseif ($cp->status === 'FINISH')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> FINISH
                                        </span>
                                    @elseif ($cp->status === 'ON LOADING')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> ON
                                            LOADING
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-yellow-50 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            {{ $cp->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->created_at ? $cp->created_at->format('d/m/Y H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->waktu_start ? $cp->waktu_start->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->waktu_end ? $cp->waktu_end->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->waktu_keluar ? $cp->waktu_keluar->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap text-center">
                                    {{ $waktuTunggu ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">
                                    {{ $cp->durasi ?? '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">
                                    {{ $durasiDok ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->updated_at ? $cp->updated_at->format('d/m/Y H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $cp->no_surat_jalan ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $cp->purchase_order ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $cp->receipt_number ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ optional($cp->createdByUser)->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ optional($cp->receivedByUser)->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ optional($cp->startedByUser)->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    {{ optional($cp->canceledByUser)->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">
                                    {{ $cp->canceled_at ? $cp->canceled_at->format('d/m/Y H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->note ?: '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->type_of_load ?: '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ optional($cp->productCategory)->name ?: '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->shipping_type ?: '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    @if($cp->is_cross_dock)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-50 text-green-700">TRUE</span>
                                    @else
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-50 text-gray-700">FALSE</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                    @if($cp->is_generated_cross_dock)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700">TRUE</span>
                                    @else
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-50 text-gray-700">FALSE</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="30" class="px-4 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="font-medium">{{ __('Tidak ada data') }}</p>
                                        <p class="text-sm mt-1">{{ __('Ubah filter untuk melihat data checkpoint') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($checkpoints->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $checkpoints->links() }}</div>
            @endif
        </div>
    </div>
@endsection
