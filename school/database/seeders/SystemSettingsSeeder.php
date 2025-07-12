<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Общие настройки
            [
                'setting_key' => 'site_name',
                'setting_value' => 'School CRM',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Название сайта',
                'is_public' => true,
            ],
            [
                'setting_key' => 'site_description',
                'setting_value' => 'Система управления образовательным процессом',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Описание сайта',
                'is_public' => true,
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => 'false',
                'setting_type' => 'boolean',
                'category' => 'general',
                'description' => 'Режим обслуживания',
                'is_public' => true,
            ],

            // Настройки резервного копирования
            [
                'setting_key' => 'backup_enabled',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'backup',
                'description' => 'Включить автоматическое резервное копирование',
                'is_public' => true,
            ],
            [
                'setting_key' => 'backup_frequency',
                'setting_value' => 'daily',
                'setting_type' => 'string',
                'category' => 'backup',
                'description' => 'Частота резервного копирования (daily, weekly, monthly)',
                'is_public' => true,
            ],
            [
                'setting_key' => 'backup_retention_days',
                'setting_value' => '30',
                'setting_type' => 'integer',
                'category' => 'backup',
                'description' => 'Количество дней хранения резервных копий',
                'is_public' => true,
            ],
            [
                'setting_key' => 'backup_include_files',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'backup',
                'description' => 'Включать файлы в резервную копию',
                'is_public' => true,
            ],
            [
                'setting_key' => 'backup_storage_path',
                'setting_value' => 'storage/backups',
                'setting_type' => 'string',
                'category' => 'backup',
                'description' => 'Путь для хранения резервных копий',
                'is_public' => false,
            ],

            // Настройки безопасности
            [
                'setting_key' => 'session_timeout',
                'setting_value' => '120',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Время неактивности сессии в минутах',
                'is_public' => true,
            ],
            [
                'setting_key' => 'max_login_attempts',
                'setting_value' => '5',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Максимальное количество попыток входа',
                'is_public' => true,
            ],
            [
                'setting_key' => 'password_min_length',
                'setting_value' => '8',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Минимальная длина пароля',
                'is_public' => true,
            ],

            // Настройки уведомлений
            [
                'setting_key' => 'email_notifications',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'notifications',
                'description' => 'Включить email уведомления',
                'is_public' => true,
            ],
            [
                'setting_key' => 'notification_smtp_host',
                'setting_value' => 'smtp.gmail.com',
                'setting_type' => 'string',
                'category' => 'notifications',
                'description' => 'SMTP сервер для отправки email',
                'is_public' => false,
            ],
            [
                'setting_key' => 'notification_smtp_port',
                'setting_value' => '587',
                'setting_type' => 'integer',
                'category' => 'notifications',
                'description' => 'Порт SMTP сервера',
                'is_public' => false,
            ],

            // Настройки системы
            [
                'setting_key' => 'max_file_size',
                'setting_value' => '10485760',
                'setting_type' => 'integer',
                'category' => 'system',
                'description' => 'Максимальный размер загружаемого файла (в байтах)',
                'is_public' => true,
            ],
            [
                'setting_key' => 'allowed_file_types',
                'setting_value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar',
                'setting_type' => 'string',
                'category' => 'system',
                'description' => 'Разрешенные типы файлов',
                'is_public' => true,
            ],
            [
                'setting_key' => 'timezone',
                'setting_value' => 'Europe/Moscow',
                'setting_type' => 'string',
                'category' => 'system',
                'description' => 'Часовой пояс системы',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insert($setting);
        }
    }
}
