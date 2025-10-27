<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->string('id')->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index('sessions_user_id_index');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index('sessions_last_activity_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
