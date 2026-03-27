<?php

use App\Livewire\Admin\ProductIndex;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Product\ListProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// Ruta principal (home)
Route::get('/', function () {
    return view('home');
})->name('home');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Ruta para cerrar sesión
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('success', 'Has cerrado sesión correctamente');
})->name('logout')->middleware('auth');

// Rutas de administración (solo admin)
// Cambia 'role:admin' a 'role:1' si prefieres usar ID
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/usuarios', function () {
        // Doble verificación por seguridad
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }
        return view('admin.users');
    })->name('admin.users');

    Route::get('/dashboard', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/usuarios', UsersIndex::class)->name('admin.users');
    Route::get('/productos', ProductIndex::class)->name('products.index');
});

// Ruta para actualizaciones de Livewire
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/livewire/update', $handle);
});
