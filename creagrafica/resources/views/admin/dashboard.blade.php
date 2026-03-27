@extends('components.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Usuario::count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="mt-4 block text-sm text-blue-600 hover:text-blue-800">
                Ver detalles <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Productos Activos</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ class_exists('\App\Models\Product') ? \App\Models\Product::count() : '0' }}
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full text-green-600">
                    <i class="fas fa-mug-hot text-xl"></i>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="mt-4 block text-sm text-green-600 hover:text-green-800">
                Gestionar inventario <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Configuración</p>
                    <p class="text-3xl font-bold text-gray-800">-</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full text-purple-600">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
            </div>
            <a href="#" class="mt-4 block text-sm text-purple-600 hover:text-purple-800">
                Ajustes generales <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Actividad Reciente</h3>
        <p class="text-gray-600">Aquí se cargaría el contenido principal, tablas de datos, o formularios grandes.</p>

        </div>
@endsection
