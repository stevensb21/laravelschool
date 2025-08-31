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
        Schema::table('home_works', function (Blueprint $table) {
            // Сначала удаляем ограничение внешнего ключа
            $table->dropForeign(['method_id']);
            
            // Делаем поле nullable
            $table->foreignId('method_id')->nullable()->change();
            
            // Добавляем обратно ограничение внешнего ключа с поддержкой null
            $table->foreign('method_id')->references('id')->on('methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_works', function (Blueprint $table) {
            // Удаляем ограничение внешнего ключа
            $table->dropForeign(['method_id']);
            
            // Возвращаем поле как не nullable
            $table->foreignId('method_id')->nullable(false)->change();
            
            // Добавляем обратно ограничение внешнего ключа
            $table->foreign('method_id')->references('id')->on('methods')->onDelete('cascade');
        });
    }
};
