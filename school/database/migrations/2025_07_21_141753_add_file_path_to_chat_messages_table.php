<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Проверяем, существует ли уже колонка file_path
        $hasColumn = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.columns 
            WHERE table_name = 'chat_messages' 
            AND column_name = 'file_path'
        ")[0]->count;

        if ($hasColumn == 0) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->string('file_path')->nullable();
            });
        } else {
            echo "Column file_path already exists in chat_messages table.\n";
        }
    }

    public function down()
    {
        // Проверяем, существует ли колонка перед удалением
        $hasColumn = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.columns 
            WHERE table_name = 'chat_messages' 
            AND column_name = 'file_path'
        ")[0]->count;

        if ($hasColumn > 0) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }
    }
};
