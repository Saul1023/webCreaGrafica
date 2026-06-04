{{-- resources/views/livewire/auth/register.blade.php --}}
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Crear Cuenta en CREAGRAFICA
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                O
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    inicia sesión si ya tienes cuenta
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit.prevent="register">
            @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif

            <div class="rounded-md shadow-sm space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="sr-only">Nombre</label>
                        <input id="nombre" wire:model="nombre" type="text" autocomplete="given-name" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('nombre') border-red-500 @enderror"
                            placeholder="Nombre">
                        @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apellido" class="sr-only">Apellido</label>
                        <input id="apellido" wire:model="apellido" type="text" autocomplete="family-name"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('apellido') border-red-500 @enderror"
                            placeholder="Apellido (opcional)">
                        @error('apellido')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="nombre_usuario" class="sr-only">Nombre de usuario</label>
                    <input id="nombre_usuario" wire:model="nombre_usuario" type="text" autocomplete="username"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('nombre_usuario') border-red-500 @enderror"
                        placeholder="Nombre de usuario (opcional)">
                    @error('nombre_usuario')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Si lo dejas vacío, se generará automáticamente</p>
                </div>

                <div>
                    <label for="correo" class="sr-only">Correo electrónico</label>
                    <input id="correo" wire:model="correo" type="email" autocomplete="email" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('correo') border-red-500 @enderror"
                        placeholder="Correo electrónico">
                    @error('correo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefono" class="sr-only">Teléfono</label>
                    <input id="telefono" wire:model="telefono" type="tel" autocomplete="tel"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('telefono') border-red-500 @enderror"
                        placeholder="Teléfono (opcional)">
                    @error('telefono')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" wire:model="password" type="password" autocomplete="new-password" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="Contraseña (mínimo 6 caracteres)">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="sr-only">Confirmar contraseña</label>
                    <input id="password_confirmation" wire:model="password_confirmation" type="password"
                        autocomplete="new-password" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Confirmar contraseña">
                </div>
            </div>

            <div class="text-sm text-gray-600">
                <p>Al registrarte, aceptas nuestros
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Términos de servicio</a>
                    y
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Política de privacidad</a>.
                </p>
            </div>

            <div>
                <button type="submit" wire:loading.attr="disabled"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:bg-green-400">
                    <span wire:loading.remove wire:target="register">
                        Crear Cuenta
                    </span>
                    <span wire:loading wire:target="register">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Creando cuenta...
                    </span>
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('home') }}" class="font-medium text-gray-600 hover:text-gray-500">
                ← Volver al inicio
            </a>
        </div>
    </div>
</div>
