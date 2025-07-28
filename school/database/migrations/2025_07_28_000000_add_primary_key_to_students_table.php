<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Проверяем, есть ли уже первичный ключ students_pkey
        $hasPrimaryKey = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.table_constraints 
            WHERE table_name = 'students' 
            AND constraint_name = 'students_pkey'
            AND constraint_type = 'PRIMARY KEY'
        ")[0]->count;

        if ($hasPrimaryKey == 0) {
            // Добавляем PRIMARY KEY к id, только если его нет
            DB::statement('ALTER TABLE students ADD PRIMARY KEY (id);');
        } else {
            echo "Primary key students_pkey already exists on students table.\n";
        }
    }
    
    public function down()
    {
        // Откат невозможен без пересоздания таблицы
    }
}; 