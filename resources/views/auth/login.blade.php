<x-guest-layout>
    <!-- Dark Mode Toggle -->
    <div class="absolute top-4 right-4">
        <x-dark-mode-toggle />
    </div>

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-2xl">RX</span>
                </div>
                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">RecuperaX</span>
            </div>
        </div>

        <!-- Card de Login -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">Entrar no Sistema</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <!-- Email/Username -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('E-mail ou Usuário')" class="text-gray-700 dark:text-gray-300 font-medium mb-2" />
                    <x-text-input id="email" 
                                   class="block mt-1 w-full" 
                                   type="text" 
                                   name="email" 
                                   :value="old('email')" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="seu@email.com ou seu_usuario" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Senha')" class="text-gray-700 dark:text-gray-300 font-medium mb-2" />
                    <x-text-input id="password" 
                                  class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  required 
                                  autocomplete="current-password"
                                  placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me"
                               type="checkbox"
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400"
                               name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline"
                           href="{{ route('password.request') }}">
                            {{ __('Esqueci minha senha') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="loading"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!loading">Entrar</span>
                    <span x-show="loading">Entrando...</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
            © {{ date('Y') }} RecuperaX. Todos os direitos reservados.
        </p>
    </div>
</x-guest-layout>
