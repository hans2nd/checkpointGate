<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Checkpoint GIIC') }} — @yield('title', 'Login')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    {{-- Language Switcher for Guest Pages --}}
    <div class="fixed top-4 left-4 sm:top-6 sm:left-6 z-50">
        <div class="relative" id="guestLangWrap">
            <button type="button" onclick="document.getElementById('guestLangDropdown').classList.toggle('hidden')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 text-white/80 hover:text-white backdrop-blur-md transition-all duration-200 shadow-lg">
                <span class="text-base leading-none">{{ app()->getLocale() === 'id' ? '🇮🇩' : '🇬🇧' }}</span>
                <span class="text-xs font-semibold uppercase">{{ app()->getLocale() }}</span>
                <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="guestLangDropdown"
                class="hidden absolute left-0 mt-2 w-44 bg-white/10 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl z-50 py-1 overflow-hidden">
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="id">
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm transition-colors {{ app()->getLocale() === 'id' ? 'bg-white/20 text-white font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <span class="text-base leading-none">🇮🇩</span>
                        <span>Bahasa Indonesia</span>
                        @if(app()->getLocale() === 'id')
                            <svg class="w-4 h-4 ml-auto text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                </form>
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm transition-colors {{ app()->getLocale() === 'en' ? 'bg-white/20 text-white font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <span class="text-base leading-none">🇬🇧</span>
                        <span>English</span>
                        @if(app()->getLocale() === 'en')
                            <svg class="w-4 h-4 ml-auto text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="min-h-full flex items-center justify-center px-4 py-12">
        @yield('content')
    </div>

    <script>
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('guestLangWrap');
            const dd = document.getElementById('guestLangDropdown');
            if (wrap && !wrap.contains(e.target)) dd.classList.add('hidden');
        });
    </script>
</body>
</html>
