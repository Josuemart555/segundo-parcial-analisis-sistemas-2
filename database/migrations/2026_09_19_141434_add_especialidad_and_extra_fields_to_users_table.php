<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('especialidad_id')->nullable()->after('id')->constrained('especialidades')->nullOnDelete();
            $table->string('telefono')->nullable()->after('email');
            $table->boolean('activo')->default(true)->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('especialidad_id');
            $table->dropColumn(['telefono', 'activo']);
        });
    }
};
