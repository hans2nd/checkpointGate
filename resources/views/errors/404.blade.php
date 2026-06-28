@extends('layouts.guest')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="w-full max-w-lg text-center backdrop-blur-xl bg-white/5 border border-white/10 p-10 sm:p-14 rounded-3xl shadow-2xl relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-24 -left-24 w-48 h-48 bg-orange-500/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl"></div>

    <div class="relative z-10">
        <h1 class="text-8xl sm:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-rose-400 drop-shadow-lg mb-4">
            404
        </h1>
        
        <h2 class="text-2xl sm:text-3xl font-semibold text-white mb-4">
            Halaman Tidak Ditemukan
        </h2>
        
        <p class="text-white/70 mb-10 leading-relaxed text-sm sm:text-base">
            Maaf, halaman yang Anda cari mungkin telah dihapus, diubah namanya, atau tidak tersedia untuk saat ini.
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/30 transform transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 focus:ring-offset-slate-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
