<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('motivo');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'atendida'])
                ->default('pendiente');
            $table->timestamps();

            $table->index(['doctor_id', 'fecha_inicio', 'fecha_fin'], 'citas_doctor_rango_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
