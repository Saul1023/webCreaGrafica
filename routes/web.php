<?php

use App\Livewire\Admin\UsersIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Productos\AdminIndex as ProductosAdminIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\DashboardContent;
use App\Livewire\Admin\CategoriasIndex;
use App\Livewire\Admin\PerfilIndex;
use App\Livewire\Admin\ProductosIndex;

// Home - Redirige según el rol
Route::get('/', function () {
    if (Auth::check() && Auth::user()->rol_id == 1) {
        // Si es administrador, va al dashboard
        return redirect()->route('admin.dashboard');
    }
    // Si no es administrador, ve a la página pública
    return view('home');
})->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Tienda pública


// Panel de Administración (solo admin)
// Panel de Administración (solo admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // CORRECCIÓN: Apuntar al componente Livewire para ver la analítica
    Route::get('/dashboard', DashboardContent::class)->name('admin.dashboard');

    Route::get('/usuarios', UsersIndex::class)->name('admin.users');
    Route::get('/categorias', CategoriasIndex::class)->name('admin.categorias');
    Route::get('/productos', ProductosIndex::class)->name('admin.productos');
    Route::get('/perfil', PerfilIndex::class)->name('admin.perfil');
    Route::get('/pedidos', \App\Livewire\Admin\PedidosIndex::class)->name('admin.pedidos');
    Route::get('/reportes', \App\Livewire\Admin\ReportesIndex::class)->name('admin.reportes');
    Route::get('/proveedores', \App\Livewire\Admin\ProveedoresIndex::class)->name('admin.proveedores');
    Route::get('/compras', \App\Livewire\Admin\ComprasIndex::class)->name('admin.compras');
    Route::get('/cupones', \App\Livewire\Admin\CuponesIndex::class)->name('admin.cupones');
    Route::get('/ofertas', \App\Livewire\Admin\OfertasIndex::class)->name('admin.ofertas');
    Route::get('/caja', \App\Livewire\Admin\CajaIndex::class)->name('admin.caja');
});

// Panel de Clientes (cliente o admin para soporte)
Route::middleware(['auth', 'role:cliente,admin'])->prefix('cliente')->group(function () {
    Route::get('/pedidos', \App\Livewire\Cliente\ClienteDashboard::class)->name('cliente.dashboard');
});