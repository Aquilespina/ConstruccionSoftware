<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('usuario', 'remember_token')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->string('remember_token', 100)->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuario', 'remember_token')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }
    }
};
