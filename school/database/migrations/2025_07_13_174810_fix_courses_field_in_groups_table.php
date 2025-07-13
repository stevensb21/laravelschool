<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Для PostgreSQL: преобразуем все элементы массива courses к числам
        DB::statement(<<<'SQL'
            UPDATE groups
            SET courses = to_jsonb(ARRAY(
                SELECT value::int
                FROM jsonb_array_elements_text(courses::jsonb) AS t(value)
            ));
        SQL);
    }

    public function down(): void
    {
        // Откат: преобразуем обратно в строки (если потребуется)
        DB::statement(<<<'SQL'
            UPDATE groups
            SET courses = to_jsonb(ARRAY(
                SELECT value::text
                FROM jsonb_array_elements(courses::jsonb) AS t(value)
            ));
        SQL);
    }
};
