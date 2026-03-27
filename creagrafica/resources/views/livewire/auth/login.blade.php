<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Iniciar Sesión en CREAGRAFICA
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                O
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    crear una nueva cuenta
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit.prevent="login">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('message') }}
                </div>
            @endif

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="nombre_usuario" class="sr-only">Correo electrónico</label>
                    <input id="nombre_usuario" wire:model="nombre_usuario" type="nombre_usuario" autocomplete="nombre_usuario" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('nombre_usuario') border-red-500 @enderror"
                           placeholder="Nombre de usuario">
                    @error('nombre_usuario')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" wire:model="password" type="password" autocomplete="current-password" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                           placeholder="Contraseña">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" wire:model="remember" type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Recordarme
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </div>

            @error('nombre_usuario')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $message }}
                </div>
            @enderror

            <div>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-400">
                    <span wire:loading.remove wire:target="login">
                        Iniciar Sesión
                    </span>
                    <span wire:loading wire:target="login">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Iniciando...
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
