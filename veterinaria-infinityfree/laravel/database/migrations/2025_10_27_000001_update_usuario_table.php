<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Asegurar AUTO_INCREMENT en id_usuario y ENUM de roles correcto
        DB::statement("ALTER TABLE `usuario` MODIFY `id_usuario` INT NOT NULL AUTO_INCREMENT");
        DB::statement("ALTER TABLE `usuario` MODIFY `tipo_permiso` ENUM('administrador','medico','recepcionista') NOT NULL DEFAULT 'recepcionista'");
        // Asegurar estado con default 1 y correo único (si no lo fuera ya)
        DB::statement("ALTER TABLE `usuario` MODIFY `estado` TINYINT(1) NOT NULL DEFAULT 1");
        DB::statement("ALTER TABLE `usuario` MODIFY `correo_electronico` VARCHAR(100) NOT NULL");
        // El índice único sobre correo_electronico puede existir ya; si no, intenta crearlo
        try {
            DB::statement("CREATE UNIQUE INDEX `correo_electronico` ON `usuario` (`correo_electronico`)");
        } catch (\Throwable $e) {
            // Ignorar si ya existe
        }
    }

    public function down(): void
    {
        // Revertir a un estado más cercano al original (sin AUTO_INCREMENT y enum admin/staff)
        DB::statement("ALTER TABLE `usuario` MODIFY `id_usuario` INT NOT NULL");
        DB::statement("ALTER TABLE `usuario` MODIFY `tipo_permiso` ENUM('admin','staff') NULL DEFAULT 'staff'");
    }
};
