<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Добавляем PRIMARY KEY к id, если его нет
        DB::statement('ALTER TABLE teachers ADD PRIMARY KEY (id);');
    }

    public function down()
    {
        // Откатить нельзя без пересоздания таблицы, оставляем пустым
    }
}; 