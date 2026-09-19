<?php

use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\EstadoCitaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/calendario');
});

Route::middleware('auth')->group(function () {
    Route::get('/calendario', function () {
        return view('calendario.index');
    })->name('calendario');

    Route::get('/dashboard', fn () => redirect()->route('calendario'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->group(function () {
        Route::resource('especialidades', EspecialidadController::class)
            ->except(['show'])
            ->parameters(['especialidades' => 'especialidad']);
        Route::resource('estados-cita', EstadoCitaController::class)
            ->except(['show'])
            ->parameters(['estados-cita' => 'estado_cita']);
        Route::resource('usuarios', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
