<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Checkpoint GIIC') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Fullscreen mode */
        body.fullscreen-mode .fullscreen-hide {
            display: none !important;
        }

        body.fullscreen-mode .fullscreen-main {
            padding: 0 !important;
        }

        body.fullscreen-mode .fullscreen-main>main {
            padding: 12px !important;
        }
    </style>
</head>

<body class="h-full">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 fullscreen-main">
            {{-- Top Header --}}
            <header
                class="fullscreen-hide bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="openSidebar()"
                        class="lg:hidden p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    @yield('header-actions')

                    {{-- Language Switcher --}}
                    <div class="relative" id="langSwitcherWrap">
                        <button type="button" onclick="document.getElementById('langDropdown').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200"
                            id="langSwitcherBtn">
                            <span class="text-base leading-none">{{ app()->getLocale() === 'id' ? '🇮🇩' : '🇬🇧' }}</span>
                            <span class="hidden sm:inline text-xs font-semibold uppercase">{{ app()->getLocale() }}</span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="langDropdown"
                            class="hidden absolute right-0 mt-1 w-40 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-1 overflow-hidden">
                            <form method="POST" action="{{ route('locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="id">
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm transition-colors {{ app()->getLocale() === 'id' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span class="text-base leading-none">🇮🇩</span>
                                    <span>{{ __('Bahasa Indonesia') }}</span>
                                    @if(app()->getLocale() === 'id')
                                        <svg class="w-4 h-4 ml-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="en">
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm transition-colors {{ app()->getLocale() === 'en' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span class="text-base leading-none">🇬🇧</span>
                                    <span>{{ __('English') }}</span>
                                    @if(app()->getLocale() === 'en')
                                        <svg class="w-4 h-4 ml-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>

                    <span class="hidden sm:inline text-sm text-gray-500">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                            title="{{ __('Logout') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                @yield('content')
            </main>
            {{-- Footer --}}
            <footer class="shrink-0 bg-white border-t border-gray-200 px-4 sm:px-6 py-2.5">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-1 text-xs text-gray-400">
                    <span><strong class="text-gray-500">PT. Pangan Lestari</strong></span>
                    <span>&copy; {{ date('Y') }} Hans &mdash; Divisi EDP. All rights reserved.</span>
                </div>
            </footer>
        </div>
    </div>

    {{-- SweetAlert Flash Messages --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: @json(__('Berhasil!')),
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false,
                position: 'center'
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: @json(__('Gagal!')),
                text: @json(session('error')),
                timer: 5000,
                showConfirmButton: true,
                position: 'center'
            });
        </script>
    @endif
    @if (session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: @json(__('Perhatian!')),
                text: @json(session('warning')),
                showConfirmButton: true,
                confirmButtonColor: '#F59E0B'
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: @json(__('Validasi gagal!')),
                html: @json($errors->all()).map(error => `<div>${error}</div>`).join(''),
                showConfirmButton: true,
                position: 'center'
            });
        </script>
    @endif

    {{-- Reusable SweetAlert helpers --}}
    <script>
        function confirmDelete(formOrUrl, message) {
            Swal.fire({
                title: @json(__('Yakin hapus?')),
                text: message || @json(__('Data yang dihapus tidak bisa dikembalikan.')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: @json(__('Ya, hapus!')),
                cancelButtonText: @json(__('Batal'))
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof formOrUrl === 'string') {
                        window.location.href = formOrUrl;
                    } else {
                        formOrUrl.submit();
                    }
                }
            });
        }

        function confirmBulkDelete(url, ids, message) {
            Swal.fire({
                title: @json(__('Yakin hapus?')).replace('?', ' ' + ids.length + ' data?'),
                text: message || @json(__('Data yang dihapus tidak bisa dikembalikan.')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: @json(__('Yakin hapus semua!')),
                cancelButtonText: @json(__('Batal'))
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrf);
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        // Fullscreen toggle
        function toggleFullscreen() {
            document.body.classList.toggle('fullscreen-mode');
            const isFs = document.body.classList.contains('fullscreen-mode');
            localStorage.setItem('checkpoint_fullscreen', isFs ? '1' : '0');
            // Update button icons
            document.querySelectorAll('.fs-icon-expand').forEach(el => el.style.display = isFs ? 'none' : 'block');
            document.querySelectorAll('.fs-icon-compress').forEach(el => el.style.display = isFs ? 'block' : 'none');
            document.querySelectorAll('.fs-exit-btn').forEach(el => el.style.display = isFs ? 'flex' : 'none');
        }
        // Restore fullscreen on load
        if (localStorage.getItem('checkpoint_fullscreen') === '1') {
            document.body.classList.add('fullscreen-mode');
        }
        // ESC to exit fullscreen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('fullscreen-mode')) {
                toggleFullscreen();
            }
        });
        // Close language dropdown on click outside
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('langSwitcherWrap');
            const dd = document.getElementById('langDropdown');
            if (wrap && !wrap.contains(e.target)) dd.classList.add('hidden');
        });
    </script>
</body>

</html>
