{{-- Sidebar Navigation --}}
<aside id="sidebar"
    class="sidebar-expanded bg-slate-900 text-white flex flex-col shrink-0 transition-all duration-300 ease-in-out fullscreen-hide
              fixed inset-y-0 left-0 z-50
              lg:static lg:translate-x-0
              -translate-x-full">

    {{-- Logo / Brand --}}
    <div class="px-4 py-4 border-b border-slate-700/50 flex items-center justify-between">
        <div class="sidebar-brand flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo-icon.png') }}" alt="Finna" class="h-9 w-9 rounded-lg object-cover shrink-0">
            <span class="sidebar-label whitespace-nowrap">
                <h2 class="text-sm font-bold tracking-wide leading-tight">CHECKPOINT</h2>
                <p class="text-[10px] text-slate-400 font-medium">GIIC Gate System</p>
            </span>
        </div>
        {{-- Close button (mobile only) --}}
        <button onclick="closeSidebar()"
            class="lg:hidden p-1 text-slate-400 hover:text-white rounded-lg hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
        <p class="sidebar-label px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">
            {{ __('Menu') }}</p>

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                  {{ request()->routeIs('dashboard') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            title="Dashboard">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="sidebar-label">{{ __('Dashboard') }}</span>
        </a>

        {{-- Live Monitoring --}}
        <a href="{{ route('livemonitoring') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                  {{ request()->routeIs('livemonitoring') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            title="Live Monitoring">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="sidebar-label">{{ __('Live Monitoring') }}</span>
        </a>



        {{-- Checkpoints --}}
        <a href="{{ route('checkpoints.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                  {{ request()->routeIs('checkpoints.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            title="Data Checkpoint">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span class="sidebar-label">{{ __('Data Checkpoint') }}</span>
        </a>

        {{-- Report --}}
        @if (Auth::user()->hasPermission('report.view'))
            <a href="{{ route('report.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                  {{ request()->routeIs('report.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                title="Report">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="sidebar-label">{{ __('Report') }}</span>
            </a>
        @endif

        <div class="pt-3">
            <p class="sidebar-label px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">
                {{ __('Master Data') }}</p>

            {{-- Gates --}}
            {{-- <a href="{{ route('gates.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('gates.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
               title="Master Gate">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="sidebar-label">Master Gate</span>
            </a> --}}

            {{-- Vehicles --}}
            <a href="{{ route('vehicles.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('vehicles.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                title="Master Kendaraan">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span class="sidebar-label">{{ __('Master Kendaraan') }}</span>
            </a>

            {{-- Vehicle Types --}}
            <a href="{{ route('vehicle-types.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('vehicle-types.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                title="Master Vehicle Type">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="sidebar-label">{{ __('Jenis Kendaraan') }}</span>
            </a>

            {{-- Employees (Admin only) --}}
            @if (Auth::user()->isAdmin())
                <a href="{{ route('employees.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('employees.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    title="Master Employee">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <span class="sidebar-label">{{ __('Master Employee') }}</span>
                </a>
            @endif
        </div>

        <div class="pt-3">
            <p class="sidebar-label px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">
                {{ __('Akun') }}</p>

            {{-- User Management (Admin only) --}}
            @if (Auth::user()->isAdmin())
                <a href="{{ route('users.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('users.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    title="User Management">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="sidebar-label">{{ __('User Management') }}</span>
                </a>

                {{-- Role Management --}}
                <a href="{{ route('roles.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('roles.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    title="Role Management">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="sidebar-label">{{ __('Role Management') }}</span>
                </a>

                {{-- Permission Management --}}
                <a href="{{ route('permissions.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('permissions.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    title="Permission Management">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="sidebar-label">{{ __('Permission') }}</span>
                </a>

                {{-- Maintenance Mode --}}
                <a href="{{ route('maintenance.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('maintenance.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    title="Maintenance Mode">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-label">{{ __('Maintenance Mode') }}</span>
                </a>
            @endif

            {{-- Profile --}}
            <a href="{{ route('profile.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('profile.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                title="Profil">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="sidebar-label">{{ __('Profil') }}</span>
            </a>
        </div>
    </nav>

    {{-- Collapse Toggle (desktop only) --}}
    <div class="hidden lg:block px-2 py-2 border-t border-slate-700/50">
        <button onclick="toggleSidebarCollapse()"
            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
            title="Toggle sidebar">
            <svg id="collapse-icon" class="w-5 h-5 transition-transform duration-300" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <span class="sidebar-label text-xs">{{ __('Tutup Menu') }}</span>
        </button>
    </div>

    {{-- Footer --}}
    <div class="px-3 py-3 border-t border-slate-700/50">
        <div class="flex items-center gap-3 overflow-hidden">
            <div
                class="w-8 h-8 bg-slate-700 rounded-full flex items-center justify-center text-xs font-bold text-orange-400 shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="sidebar-label flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- Overlay for mobile --}}
<div id="sidebar-overlay" onclick="closeSidebar()"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300"></div>

<script>
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function toggleSidebarCollapse() {
        const sidebar = document.getElementById('sidebar');
        const icon = document.getElementById('collapse-icon');
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');

        if (isCollapsed) {
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
            icon.style.transform = '';
            localStorage.setItem('sidebar-collapsed', 'false');
        } else {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            icon.style.transform = 'rotate(180deg)';
            localStorage.setItem('sidebar-collapsed', 'true');
        }
    }

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const icon = document.getElementById('collapse-icon');
        if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 1024) {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    });
</script>
