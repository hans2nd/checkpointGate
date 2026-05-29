@extends('layouts.app')

@section('title', __('Maintenance Mode'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Maintenance Mode') }}</h2>
                        {{-- <p class="text-sm text-gray-500 leading-relaxed max-w-2xl">
                        {{ __('Fitur ini digunakan untuk memblokir sementara akses login bagi seluruh pengguna (kecuali Administrator) saat sistem sedang dalam perbaikan atau update rutin. User yang sedang aktif akan otomatis dikeluarkan (logout).') }}
                    </p> --}}
                    </div>
                    <div class="shrink-0 p-3 bg-{{ $isMaintenance ? 'orange' : 'slate' }}-50 rounded-xl">
                        <svg class="w-8 h-8 text-{{ $isMaintenance ? 'orange' : 'slate' }}-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                </div>

                <hr class="my-8 border-gray-100">

                <div
                    class="flex flex-col sm:flex-row items-center justify-between bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <div class="mb-4 sm:mb-0 text-center sm:text-left">
                        <p class="text-base font-semibold text-gray-900">{{ __('Status Saat Ini') }}</p>
                        <div class="mt-1 flex items-center justify-center sm:justify-start gap-2">
                            <span class="relative flex h-3 w-3">
                                @if ($isMaintenance)
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                                @else
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                @endif
                            </span>
                            <span class="text-sm font-medium {{ $isMaintenance ? 'text-orange-600' : 'text-emerald-600' }}">
                                {{ $isMaintenance ? __('Mode Maintenance') : __('Running Normal') }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('maintenance.toggle') }}" method="POST" id="toggleForm">
                        @csrf
                        <input type="hidden" name="status" value="{{ $isMaintenance ? '0' : '1' }}">
                        @if ($isMaintenance)
                            <button type="button" onclick="confirmToggle(false)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Matikan Maintenance Mode') }}
                            </button>
                        @else
                            <button type="button" onclick="confirmToggle(true)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                {{ __('Aktifkan Maintenance Mode') }}
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmToggle(isActivating) {
            let title, text, confirmColor, confirmText;

            if (isActivating) {
                title = @json(__('Yakin aktifkan Maintenance?'));
                text = @json(__('Semua pengguna (selain Admin) akan otomatis logout dan tidak bisa login kembali hingga mode ini dimatikan.'));
                confirmColor = '#F97316'; // orange-500
                confirmText = @json(__('Ya, Aktifkan!'));
            } else {
                title = @json(__('Matikan Maintenance?'));
                text = @json(__('Aplikasi akan kembali berjalan normal dan semua pengguna dapat login kembali.'));
                confirmColor = '#10B981'; // emerald-500
                confirmText = @json(__('Ya, Normalkan!'));
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6B7280',
                confirmButtonText: confirmText,
                cancelButtonText: @json(__('Batal'))
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('toggleForm').submit();
                }
            });
        }
    </script>
@endsection
