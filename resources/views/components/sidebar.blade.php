<div x-data="{ 
    sidebarOpen: window.innerWidth >= 1024,
    init() {
        this.$watch('sidebarOpen', value => {
            if (value) {
                document.body.classList.add('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-open');
            }
        });
        
        window.addEventListener('toggle-sidebar', () => {
            this.sidebarOpen = !this.sidebarOpen;
        });
    }
}" 
     :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
     class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-900 dark:bg-gray-800 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0">
    
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 dark:bg-indigo-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">RX</span>
                </div>
                <span class="text-lg font-bold">RecuperaX</span>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white dark:text-gray-300 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('devedores.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('devedores.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Devedores</span>
            </a>

            <a href="{{ route('empresas.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('empresas.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Empresas</span>
            </a>

            <a href="{{ route('titulos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('titulos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Títulos</span>
            </a>

            <a href="{{ route('clientes.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('clientes.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Clientes</span>
            </a>

            <a href="{{ route('dividas.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('dividas.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Dívidas</span>
            </a>

            <a href="{{ route('followups.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('followups.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span>Follow-ups</span>
            </a>

            @if(auth()->user()->canViewAllDividas())
            <a href="{{ route('carteiras.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('carteiras.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Carteiras</span>
            </a>
            @endif

            <a href="{{ route('acordos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('acordos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Acordos</span>
            </a>

            <a href="{{ route('parcelamentos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('parcelamentos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Parcelamentos</span>
            </a>

            <a href="{{ route('agendamentos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('agendamentos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Agendamentos</span>
            </a>

            <a href="{{ route('boletos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('boletos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Boletos</span>
            </a>

            <a href="{{ route('pagamentos.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('pagamentos.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Pagamentos</span>
            </a>

            <a href="{{ route('cobrancas.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('cobrancas.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Cobranças</span>
            </a>

            <a href="{{ route('tabelas.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('tabelas.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span>Tabelas Remuneração</span>
            </a>

            <a href="{{ route('relatorios.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('relatorios.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Relatórios</span>
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isGestor())
            <a href="{{ route('logs.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('logs.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Logs de Acesso</span>
            </a>
            @endif
        </nav>
    </div>

    <!-- Overlay para mobile -->
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 lg:hidden z-40"></div>
</div>
