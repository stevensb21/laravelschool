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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('grade_lesson', 3, 2)->nullable(); // оценка за урок (например, 4.50)
            $table->decimal('homework', 3, 2)->nullable(); // оценка за домашнее задание
            $table->boolean('attendance')->default(true); // посещаемость
            $table->text('notes')->nullable(); // заметки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
