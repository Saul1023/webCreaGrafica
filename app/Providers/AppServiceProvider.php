<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar rutas dinámicas para Livewire si la aplicación se ejecuta en un subdirectorio (XAMPP / Apache)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        $baseDir = str_replace('\\', '/', $baseDir);

        if ($baseDir !== '/' && $baseDir !== '') {
            \Livewire\Livewire::setScriptRoute(function ($handle) use ($baseDir) {
                return \Illuminate\Support\Facades\Route::get($baseDir . '/livewire/livewire.js', $handle);
            });

            \Livewire\Livewire::setUpdateRoute(function ($handle) use ($baseDir) {
                return \Illuminate\Support\Facades\Route::post($baseDir . '/livewire/update', $handle);
            });
        }
    }
}
