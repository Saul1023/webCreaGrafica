<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-user-cog text-blue-600"></i> Mi Perfil
        </h2>
        <p class="text-sm text-gray-500">Gestiona tus datos personales y configuraciones de seguridad.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div
                    class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-center text-3xl font-bold text-white mx-auto mb-4 shadow-md">
                    {{ substr($nombre ?? 'A', 0, 1) }}{{ substr($apellido ?? '', 0, 1) }}
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ $nombre }} {{ $apellido }}</h3>
                <p class="text-sm font-mono text-gray-400 mt-1">@<span>{{ $nombre_usuario }}</span></p>

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center">
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 uppercase">
                        {{ Auth::user()->rol->nombre ?? 'Administrador' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-gray-400"></i> Información Personal
                </h4>

                @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded text-sm shadow-sm">
                    {{ session('message') }}
                </div>
                @endif

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" wire:model="nombre"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                            <input type="text" wire:model="apellido"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                            @error('apellido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario</label>
                            <input type="text" wire:model="text"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm font-mono focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                            @error('nombre_usuario') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" wire:model="correo"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                            @error('correo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-2 rounded-lg shadow-sm transition flex items-center gap-2 disabled:opacity-50">
                            <i class="fas fa-save" wire:loading.remove wire:target="updateProfile"></i>
                            <i class="fas fa-spinner fa-spin" wire:loading wire:target="updateProfile"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-gray-400"></i> Seguridad de la Cuenta
                </h4>

                @if (session()->has('password_message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded text-sm shadow-sm">
                    {{ session('password_message') }}
                </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                        <input type="password" wire:model="current_password"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                        @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                            <input type="password" wire:model="password"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva
                                Contraseña</label>
                            <input type="password" wire:model="password_confirmation"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                            class="bg-gray-900 hover:bg-gray-800 text-white font-medium text-sm px-5 py-2 rounded-lg shadow-sm transition flex items-center gap-2 disabled:opacity-50">
                            <i class="fas fa-lock" wire:loading.remove wire:target="updatePassword"></i>
                            <i class="fas fa-spinner fa-spin" wire:loading wire:target="updatePassword"></i>
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
