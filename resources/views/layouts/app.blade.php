<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RecuperaX') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Inicializar dark mode antes do Alpine -->
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors"
          x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
          @toggle-sidebar.window="sidebarOpen = !sidebarOpen">

        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Conteúdo Principal -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <x-header />

                <!-- Conteúdo -->
                <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 sm:p-6 transition-colors">
                    @if (session('status'))
                        <script>
                            window.addEventListener('DOMContentLoaded', () => {
                                window.toast.success('{{ session('status') }}');
                            });
                        </script>
                    @endif

                    @if (session('success'))
                        <script>
                            window.addEventListener('DOMContentLoaded', () => {
                                window.toast.success('{{ session('success') }}');
                            });
                        </script>
                    @endif

                    @if (session('error'))
                        <script>
                            window.addEventListener('DOMContentLoaded', () => {
                                window.toast.error('{{ session('error') }}');
                            });
                        </script>
                    @endif

                    @if ($errors->any())
                        <script>
                            window.addEventListener('DOMContentLoaded', () => {
                                @foreach ($errors->all() as $error)
                                    window.toast.error('{{ $error }}');
                                @endforeach
                            });
                        </script>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Toast Container -->
        <x-toast-container />
        @stack('scripts')
    </body>
</html>
