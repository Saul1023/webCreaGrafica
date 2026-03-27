<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
            <div class="text-sm text-gray-600">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Aquí puedes agregar la funcionalidad después -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-700">Panel de gestión de usuarios (en construcción)</p>
            <p class="mt-2 text-gray-600">Aquí podrás ver, editar y eliminar usuarios del sistema.</p>

            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-medium text-blue-800">Funcionalidades a implementar:</h3>
                <ul class="mt-2 text-blue-700 list-disc list-inside">
                    <li>Listado de usuarios con paginación</li>
                    <li>Búsqueda de usuarios</li>
                    <li>Cambio de roles (user/admin)</li>
                    <li>Eliminación de usuarios</li>
                </ul>
            </div>
        </div>
    </div>
</div>
