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
        Schema::table('management', function (Blueprint $table) {
            $table->string('action_type')->nullable(); // backup, restore, settings_update, etc.
            $table->string('action_name');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->json('parameters')->nullable(); // параметры действия
            $table->text('result')->nullable(); // результат выполнения
            $table->timestamp('executed_at')->nullable();
            $table->integer('user_id')->nullable(); // кто выполнил действие
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'action_type',
                'action_name', 
                'description',
                'status',
                'parameters',
                'result',
                'executed_at',
                'user_id'
            ]);
        });
    }
};
