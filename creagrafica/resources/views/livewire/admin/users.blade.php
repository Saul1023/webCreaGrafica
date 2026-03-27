@extends('layouts.app')

@section('title', 'Gestión de Usuarios - CREAGRAFICA')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Usuarios</h1>

        <!-- Componente Livewire -->
        @livewire('admin.user-management')
    </div>
</div>
@endsection
