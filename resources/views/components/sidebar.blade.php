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

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Empresas -->
            <a href="{{ route('empresas.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('empresas.*') ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Empresas</span>
            </a>

            <!-- Menu Gerenciamento -->
            <div x-data="{ gerenciamentoOpen: false }">
                <button @click="gerenciamentoOpen = !gerenciamentoOpen"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs(['devedores.*', 'boletos.*', 'emitir-boletos', 'processar-emissao-boleto', 'titulos.quitados', 'relatorios.honorarios', 'agendamentos.*', 'acordos.*', 'logs.*', 'tabelas.*']) ? 'bg-gray-800 dark:bg-gray-700 text-white' : 'text-gray-300 dark:text-gray-400 hover:bg-gray-800 dark:hover:bg-gray-700 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Gerenciamento</span>
                    </div>
                    <svg :class="{ 'rotate-180': gerenciamentoOpen }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="gerenciamentoOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="mt-2 ml-4 space-y-1">

                    <a href="{{ route('devedores.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('devedores.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Devedores</span>
                    </a>

                    <a href="{{ route('emitir-boletos') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('emitir-boletos') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Boletos a emitir</span>
                    </a>

                    <a href="{{ route('boletos.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('boletos.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Boletos emitidos</span>
                    </a>

                    <a href="{{ route('titulos.quitados') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('titulos.quitados') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Lista de Quitados</span>
                    </a>

                    <a href="{{ route('relatorios.honorarios') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('relatorios.honorarios') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Honorários</span>
                    </a>

                    <a href="{{ route('agendamentos.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('agendamentos.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Agendamentos</span>
                    </a>

                    <a href="{{ route('acordos.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('acordos.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Acordos & Pagamentos</span>
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isGestor())
                    <a href="{{ route('logs.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('logs.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Logs de Acesso</span>
                    </a>
                    @endif

                    <a href="{{ route('mensagens-whatsapp.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('mensagens-whatsapp.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Mensagens WhatsApp</span>
                    </a>

                    <a href="{{ route('whatsapp-templates.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('whatsapp-templates.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Modelos de Mensagens</span>
                    </a>

                    <a href="{{ route('whatsapp.conectar-pendentes') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('whatsapp.conectar-pendentes') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Conectar WhatsApp — Pendentes</span>
                    </a>

                    <a href="{{ route('whatsapp.conectar-negociados') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('whatsapp.conectar-negociados') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Conectar WhatsApp — Negociados</span>
                    </a>

                    <a href="{{ route('tabelas.index') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tabelas.*') ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 hover:text-white' }}">
                        <span>Tabelas de Remuneração</span>
                    </a>

                </div>
            </div>













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
