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
        Schema::create('pago_honorario', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedInteger('id_honorario');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo_pago', ['Efectivo', 'Tarjeta', 'Transferencia']);
            $table->text('notas')->nullable();
            $table->timestamp('fecha_pago');
            
            $table->foreign('id_honorario')->references('id_honorario')->on('honorario')->onDelete('cascade');
            $table->index(['id_honorario', 'fecha_pago']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_honorario');
    }
};
