{{-- resources/views/livewire/auth/login.blade.php --}}
<div>
    <div class="h-full flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo y título -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/10 backdrop-blur-sm mb-3">
                    <i class="fas fa-mug-hot text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-1">CREAGRAFICA</h2>
                <p class="text-blue-200 text-sm">Personalización de alta calidad</p>
            </div>

            <!-- Tarjeta de login -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Bienvenido</h3>
                    <p class="text-blue-100 text-xs mt-0.5">Inicia sesión para continuar</p>
                </div>

                <div class="px-6 py-6">
                    @if (session()->has('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-2 rounded-md text-xs">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit.prevent="login" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-1 text-gray-400 text-xs"></i> Correo o usuario
                            </label>
                            <div class="relative">
                                <input type="text" wire:model="login_input" id="login_input" required
                                    class="w-full pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('login_input') border-red-500 @enderror"
                                    placeholder="ejemplo@correo.com o usuario">
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                            </div>
                            @error('login_input')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                <i class="fas fa-lock mr-1 text-gray-400 text-xs"></i> Contraseña
                            </label>
                            <div class="relative">
                                <input type="password" wire:model="clave" id="clave" required
                                    class="w-full pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('clave') border-red-500 @enderror"
                                    placeholder="••••••••">
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                </div>
                            </div>
                            @error('clave')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="remember" class="h-3 w-3 text-blue-600 border-gray-300 rounded">
                                <span class="ml-1 text-xs text-gray-600">Recordarme</span>
                            </label>
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-700 hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md text-sm">
                            <span wire:loading.remove wire:target="login">
                                <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                            </span>
                            <span wire:loading wire:target="login">
                                <svg class="animate-spin inline h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Iniciando...
                            </span>
                        </button>

                        <div class="relative my-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="px-2 bg-white text-gray-500">¿Nuevo aquí?</span>
                            </div>
                        </div>

                        <a href="{{ route('register') }}"
                            class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-200 transition-all duration-200 text-sm">
                            <i class="fas fa-user-plus text-xs"></i> Crear nueva cuenta
                        </a>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-blue-200 hover:text-white transition-colors text-xs">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
                </a>
            </div>

            <div class="text-center mt-4">
                <p class="text-blue-200 text-xs">&copy; 2026 CREAGRAFICA</p>
            </div>
        </div>
    </div>
</div>
