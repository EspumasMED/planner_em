<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\MantenimientoProgramado;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            Log::info('Iniciando tarea programada de actualización de capacidad');
            $mantenimientos = MantenimientoProgramado::whereDate('fecha_mantenimiento', today())->get();
            
            if ($mantenimientos->isEmpty()) {
                Log::info('No hay mantenimientos programados para hoy.');
                return;
            }

            foreach ($mantenimientos as $mantenimiento) {
                Log::info("Actualizando capacidad para mantenimiento ID: {$mantenimiento->id}");
                $mantenimiento->actualizarCapacidadSiToca();
            }

            Log::info('Tarea programada de actualización de capacidad completada');
        })->dailyAt('00:01')->name('actualizar-capacidad-diaria'); // Cambiado a everyMinute() para pruebas
    })  //cambiar ->everyMinute()  por ->dailyAt('00:01') para salir del modo de prueva
    ->create();