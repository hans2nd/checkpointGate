@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="w-full max-w-md">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-2xl mb-4 shadow-lg shadow-orange-500/30">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-white">{{ __('Buat Akun Baru') }}</h2>
        <p class="text-slate-400 text-sm mt-1">{{ __('Daftar untuk mengakses Checkpoint GIIC') }}</p>
    </div>

    {{-- Register Card --}}
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/10">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-lg text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('Nama') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('Konfirmasi Password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl
                           transition-all duration-200 shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50
                           focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                {{ __('Daftar') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            {{ __('Sudah punya akun?') }}
            <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300 font-medium transition-colors">{{ __('Masuk') }}</a>
        </p>
    </div>
</div>
@endsection
