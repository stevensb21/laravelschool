<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Получаем всех студентов с их группами
        $students = DB::table('students')->whereNotNull('group_name')->get();
        
        foreach ($students as $student) {
            // Находим группу по имени
            $group = DB::table('groups')->where('name', $student->group_name)->first();
            
            if ($group) {
                // Добавляем связь в новую таблицу
                DB::table('student_group')->insert([
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                    'is_primary' => true, // Делаем существующую группу основной
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Очищаем связующую таблицу
        DB::table('student_group')->truncate();
    }
}; 