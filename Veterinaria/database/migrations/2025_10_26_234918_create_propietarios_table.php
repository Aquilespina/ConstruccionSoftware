<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::create('propietario', function (Blueprint $table) {
            $table->id('id_propietario'); // Equivale a BIGINT auto_increment PRIMARY KEY
            $table->string('nombre', 100);
            $table->string('telefono', 15)->nullable();
            $table->string('correo_electronico', 100)->nullable();
            $table->text('direccion')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            
            // Índices
            $table->index('nombre', 'idx_propietario_nombre');
            
            // Timestamps automáticos de Laravel (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propietario');
    }
};
