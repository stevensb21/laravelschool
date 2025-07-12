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
        Schema::create('methods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->json('title_homework')->nullable();
            $table->json('homework')->nullable();
            $table->json('title_lesson')->nullable();
            $table->json('lesson')->nullable();
            $table->json('title_exercise')->nullable();
            $table->json('exercise')->nullable();
            $table->json('title_book')->nullable();
            $table->json('book')->nullable();
            $table->json('title_video')->nullable();
            $table->json('video')->nullable();
            $table->json('title_presentation')->nullable();
            $table->json('presentation')->nullable();
            $table->json('title_test')->nullable();
            $table->json('test')->nullable();
            $table->json('title_article')->nullable();
            $table->json('article')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methods');
    }
};
