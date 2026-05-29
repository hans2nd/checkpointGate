@extends('layouts.app')

@section('title', __('Data Checkpoint'))

@section('content')
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