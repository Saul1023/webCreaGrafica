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
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('message') }}
                </div>
            @endif

            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="sr-only">Nombre completo</label>
                    <input id="name" wire:model="name" type="text" autocomplete="name" required
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('name') border-red-500 @enderror"
                           placeholder="Nombre completo">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <input id="email" wire:model="email" type="email" autocomplete="email" required
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror"
                           placeholder="Correo electrónico">
                    @error('email')
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
                    <input id="password_confirmation" wire:model="password_confirmation" type="password" autocomplete="new-password" required
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
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:bg-green-400">
                    <span wire:loading.remove wire:target="register">
                        Crear Cuenta
                    </span>
                    <span wire:loading wire:target="register">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
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
