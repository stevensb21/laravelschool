<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Проверяем, есть ли уже первичный ключ teachers_pkey
        $hasPrimaryKey = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.table_constraints 
            WHERE table_name = 'teachers' 
            AND constraint_name = 'teachers_pkey'
            AND constraint_type = 'PRIMARY KEY'
        ")[0]->count;

        if ($hasPrimaryKey == 0) {
            // Добавляем PRIMARY KEY к id, только если его нет
            DB::statement('ALTER TABLE teachers ADD PRIMARY KEY (id);');
        } else {
            echo "Primary key teachers_pkey already exists on teachers table.\n";
        }
    }

    public function down()
    {
        // Откатить нельзя без пересоздания таблицы, оставляем пустым
    }
}; 