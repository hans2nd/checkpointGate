@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="w-full max-w-md px-4">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="mb-4">
                <img src="{{ asset('images/logo-icon.png') }}" alt="Finna - PT. Pangan Lestari"
                    class="h-20 w-20 mx-auto rounded-2xl object-cover shadow-lg shadow-orange-500/20">
            </div>
            <h2 class="text-2xl font-bold text-white">Checkpoint GIIC</h2>
            <p class="text-slate-400 text-sm mt-1">Masuk ke akun Anda</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl border border-white/10">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-white/20 bg-white/5 text-orange-500 focus:ring-orange-500 focus:ring-offset-0">
                        <span class="text-sm text-slate-400">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl
                           transition-all duration-200 shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50
                           focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                App create by <span class="text-orange-400 font-medium"><a
                        href="https://sociabuzz.com/hans1st">Hans</a></span> (EDP Division)
            </p>
        </div>
    </div>
@endsection
