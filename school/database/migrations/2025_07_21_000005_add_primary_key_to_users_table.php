<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (id);');
    }
    public function down()
    {
        // Откат невозможен без пересоздания таблицы
    }
}; 