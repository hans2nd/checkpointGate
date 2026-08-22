@extends('layouts.app')

@section('title', __('Data Checkpoint'))

@section('content')
<style>
    /* ── Checkpoints Filter Card ── */
    .checkpoint-filter-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        padding: 24px;
    }
    .checkpoint-filter-card .filter-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }
    .checkpoint-filter-card .filter-header .filter-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border-radius: 10px;
        color: #ea580c;
    }
    .checkpoint-filter-card .filter-header h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    .checkpoint-filter-card label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }
    .checkpoint-filter-card input[type="date"],
    .checkpoint-filter-card input[type="text"],
    .checkpoint-filter-card select {
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
    .checkpoint-filter-card input:focus,
    .checkpoint-filter-card select:focus {
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

    /* ── Table wrapper enhancements ── */
    .checkpoint-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        overflow: hidden;
    }
</style>
    <div class="space-y-4">
        @include('checkpoints.partials.header_actions')
        @include('checkpoints.partials.filter_form')
        @include('checkpoints.partials.column_toggle')
        @include('checkpoints.partials.table')
        @include('checkpoints.partials.info_box')
    </div>
    
    @include('checkpoints.partials.modals')
    @include('checkpoints.partials.scripts')
@endsection