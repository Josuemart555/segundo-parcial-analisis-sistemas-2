<?php

use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EstadoCitaController;
use App\Http\Controllers\Api\PacienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('doctores', [DoctorController::class, 'index']);
    Route::get('pacientes', [PacienteController::class, 'index']);
    Route::get('estados-cita', [EstadoCitaController::class, 'index']);

    Route::get('citas', [CitaController::class, 'index']);
    Route::post('citas', [CitaController::class, 'store']);
    Route::get('citas/{cita}', [CitaController::class, 'show']);
    Route::put('citas/{cita}', [CitaController::class, 'update']);
    Route::patch('citas/{cita}/estado', [CitaController::class, 'cambiarEstado']);
});
