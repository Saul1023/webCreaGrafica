@extends('layouts.admin')

@section('title', 'Dashboard - CREAGRAFICA')

@section('content')
<div class="container mx-auto">
    <!-- Encabezado de la página -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Panel de Control</h2>
        <p class="text-gray-600">Resumen general del sistema</p>
    </div>

    <!-- Rejilla de Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Tarjeta Usuarios -->
        <a href="{{ route('admin.users') }}"
            class="block bg-white p-6 rounded-lg shadow border-l-4 border-blue-500 hover:shadow-lg transition duration-300 ease-in-out cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ \App\Models\Usuario::count() }}
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-blue-600 flex items-center">
                <span>Ver detalles</span>
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </div>
        </a>

        <!-- Tarjeta Productos -->
        <a href="{{ route('admin.productos') }}"
            class="block bg-white p-6 rounded-lg shadow border-l-4 border-green-500 hover:shadow-lg transition duration-300 ease-in-out cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Productos</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ \App\Models\Producto::count() }}
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full text-green-600">
                    <i class="fas fa-mug-hot text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-green-600 flex items-center">
                <span>Gestionar inventario</span>
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </div>
        </a>

        <!-- Tarjeta Configuración (No es enlace, pero podría serlo) -->
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Configuración</p>
                    <p class="text-3xl font-bold text-gray-800">Activa</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full text-purple-600">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-purple-600 flex items-center">
                <span>Ajustes generales</span>
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </div>
        </div>
    </div>

    <!-- Mensaje de Bienvenida / Actividad Reciente -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4 border-b pb-4">
            <div class="bg-blue-500 p-2 rounded-md mr-4 text-white">
                <i class="fas fa-info-circle"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Bienvenido al Panel de Administración</h3>
        </div>
        <p class="text-gray-600">
            Hola, <strong>{{ Auth::user()->nombre }}</strong>. Desde aquí puedes gestionar usuarios, productos y la
            configuración general de CREAGRAFICA.
        </p>
    </div>
</div>
@endsection
