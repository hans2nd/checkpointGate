@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    {{-- Help Button --}}
    <a href="{{ asset('public/dokumentasi.html') }}" target="_blank" title="Buku Panduan / Help"
        class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50 flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 text-white/80 hover:text-white backdrop-blur-md transition-all duration-200 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium text-sm">Help</span>
    </a>

    <div class="w-full max-w-md px-4 mt-8 sm:mt-0">
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

            {{-- Login Mode Switcher --}}
            <div class="flex rounded-xl bg-white/5 p-1 mb-6" id="loginModeSwitcher">
                <button type="button" onclick="switchLoginMode('email')" id="btnModeEmail"
                    class="flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 bg-orange-500 text-white shadow-sm">
                    Email & Password
                </button>
                <button type="button" onclick="switchLoginMode('employee')" id="btnModeEmployee"
                    class="flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:text-white">
                    Employee ID
                </button>
            </div>

            {{-- Email Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="formEmail">
                @csrf
                <input type="hidden" name="login_mode" value="email">

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

            {{-- Employee ID Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5 hidden" id="formEmployee">
                @csrf
                <input type="hidden" name="login_mode" value="employee">

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-slate-300 mb-1.5">Employee ID</label>
                    <input id="employee_id" type="text" name="employee_id" value="{{ old('employee_id') }}" required
                        placeholder="Masukkan Employee ID Anda"
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    <p class="mt-1.5 text-xs text-slate-500">Login tanpa password menggunakan Employee ID</p>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl
                           transition-all duration-200 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                    Masuk dengan Employee ID
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                App create by <span class="text-orange-400 font-medium"><a
                        href="https://sociabuzz.com/hans1st">Hans</a></span> (EDP Division)
            </p>
        </div>
    </div>

    <script>
        function switchLoginMode(mode) {
            const formEmail = document.getElementById('formEmail');
            const formEmployee = document.getElementById('formEmployee');
            const btnEmail = document.getElementById('btnModeEmail');
            const btnEmployee = document.getElementById('btnModeEmployee');

            if (mode === 'email') {
                formEmail.classList.remove('hidden');
                formEmployee.classList.add('hidden');
                btnEmail.className = 'flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 bg-orange-500 text-white shadow-sm';
                btnEmployee.className = 'flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:text-white';
            } else {
                formEmail.classList.add('hidden');
                formEmployee.classList.remove('hidden');
                btnEmployee.className = 'flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 bg-emerald-500 text-white shadow-sm';
                btnEmail.className = 'flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:text-white';
            }
        }

        // Auto-switch to employee mode if there was an employee_id error
        @if(old('login_mode') === 'employee')
            switchLoginMode('employee');
        @endif
    </script>
@endsection
