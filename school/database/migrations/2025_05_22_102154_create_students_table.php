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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('users_id');
            $table->string('fio');
            $table->date('datebirthday');
            $table->date('datewelcome');
            $table->string('numberphone');
            $table->string('email');
            $table->string('numberparent');
            $table->string('femaleparent');
            $table->string('group_name');
            $table->float('average_performance');
            $table->float('average_attendance');
            $table->float('average_exam_score');
            $table->json('achievements')->nullable();
            $table->json('subjects');
        });

        
    }


    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
