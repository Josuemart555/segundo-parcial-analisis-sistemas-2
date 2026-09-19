<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->foreign('doctor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dropColumn('estado');
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('estado_cita_id')->after('motivo')->constrained('estados_cita');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['estado_cita_id']);
            $table->dropColumn('estado_cita_id');
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'atendida'])->default('pendiente');
            $table->foreign('doctor_id')->references('id')->on('doctores')->cascadeOnDelete();
        });
    }
};
