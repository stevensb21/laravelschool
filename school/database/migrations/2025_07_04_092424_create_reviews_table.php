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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id'); // ID отправителя (преподаватель или студент)
            $table->string('sender_type'); // Тип отправителя: 'teacher' или 'student'
            $table->unsignedBigInteger('recipient_id'); // ID получателя (преподаватель или студент)
            $table->string('recipient_type'); // Тип получателя: 'teacher' или 'student'
            $table->text('review_text'); // Текст отзыва
            $table->integer('rating')->default(5); // Рейтинг от 1 до 5
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Статус модерации
            $table->unsignedBigInteger('moderated_by')->nullable(); // ID администратора, который модерировал отзыв
            $table->text('moderation_comment')->nullable(); // Комментарий модератора при отклонении
            $table->timestamp('moderated_at')->nullable(); // Дата модерации
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index(['sender_id', 'sender_type']);
            $table->index(['recipient_id', 'recipient_type']);
            $table->index('status');
            $table->index('moderated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
