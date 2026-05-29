@extends('layouts.guest')

@section('title', 'Maintenance Mode')

@section('content')
    <div class="w-full max-w-md px-4 mt-8 sm:mt-0 text-center">
        {{-- Maintenance Icon --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-orange-500/20 mb-6">
                <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">{{ __('Sedang Maintenance') }}</h2>
            <p class="text-slate-400 text-sm leading-relaxed">
                {{ __('Aplikasi sedang dalam proses perbaikan atau pemeliharaan rutin. Silakan coba kembali beberapa saat lagi.') }}
            </p>
        </div>

        {{-- Card Actions --}}
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-2xl border border-white/10">
            <a href="{{ route('login') }}"
                class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg border border-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                {{ __('Kembali ke Login') }}
            </a>
            {{-- <p class="mt-4 text-xs text-slate-500">
                Jika Anda adalah Administrator, silakan klik tombol di atas untuk login.
            </p> --}}
        </div>

        <p class="mt-8 text-center text-sm text-slate-400">
            App create by <span class="text-orange-400 font-medium"><a href="https://sociabuzz.com/hans1st">Hans</a></span>
            (EDP Division)
        </p>
    </div>
@endsection
