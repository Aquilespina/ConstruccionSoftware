<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('id_usuario')->primary();
            $table->string('nombre_usuario', 100);
            $table->string('correo_electronico', 100);
            $table->string('password');
            $table->enum('tipo_permiso', ['admin', 'staff'])->default('staff');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};