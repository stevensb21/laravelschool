<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\GroupChat;
use App\Models\UserChat;

class AddTeachersToChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teachers:add-to-chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет всех преподавателей в чат преподавателей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем добавление преподавателей в чат...');

        // Находим или создаем чат преподавателей
        $teachersChat = GroupChat::where('name', 'Чат с преподавателями')->first();
        
        if (!$teachersChat) {
            $teachersChat = GroupChat::create([
                'name' => 'Чат с преподавателями',
                'group_id' => null
            ]);
            $this->info('Создан новый чат с преподавателями');
        }

        // Получаем всех преподавателей
        $teachers = User::where('role', 'teacher')->get();
        
        if ($teachers->isEmpty()) {
            $this->warn('Преподаватели не найдены');
            return;
        }

        $this->info("Найдено преподавателей: {$teachers->count()}");

        $addedCount = 0;
        $alreadyInChatCount = 0;

        foreach ($teachers as $teacher) {
            $userChat = UserChat::where('group_chat_id', $teachersChat->id)
                ->where('user_id', $teacher->id)
                ->first();

            if (!$userChat) {
                UserChat::create([
                    'group_chat_id' => $teachersChat->id,
                    'user_id' => $teacher->id
                ]);
                $addedCount++;
                $this->line("Добавлен преподаватель: {$teacher->name}");
            } else {
                $alreadyInChatCount++;
            }
        }

        $this->info("Добавлено новых преподавателей: {$addedCount}");
        $this->info("Уже в чате: {$alreadyInChatCount}");
        $this->info('Операция завершена успешно!');
    }
}
