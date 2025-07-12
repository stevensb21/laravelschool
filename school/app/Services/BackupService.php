<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupService
{
    protected $backupPath = 'backups/';
    protected $maxBackups = 10; // Максимальное количество резервных копий

    public function createBackup()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}";
            $backupDir = storage_path("app/{$this->backupPath}{$backupName}");
            
            // Создаем директорию для бэкапа
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Создаем бэкап базы данных
            $this->backupDatabase($backupDir);
            
            // Создаем бэкап файлов
            $this->backupFiles($backupDir);
            
            // Создаем метаданные бэкапа
            $this->createBackupMetadata($backupDir, $backupName);
            
            // Удаляем старые бэкапы
            $this->cleanOldBackups();
            
            return [
                'success' => true,
                'backup_name' => $backupName,
                'path' => $backupDir,
                'size' => $this->getBackupSize($backupDir)
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function backupDatabase($backupDir)
    {
        $dbPath = $backupDir . '/database.sql';
        
        // Получаем все таблицы (PostgreSQL)
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $tableNames = array_column($tables, 'tablename');
        // Исключаем таблицу sessions
        $tableNames = array_filter($tableNames, function($name) {
            return $name !== 'sessions';
        });
        
        $sql = '';
        
        // Создаем SQL для каждой таблицы
        foreach ($tableNames as $tableName) {
            // Структура таблицы (PostgreSQL)
            $createTable = DB::select("SELECT column_name, data_type, is_nullable, column_default 
                                     FROM information_schema.columns 
                                     WHERE table_name = ? AND table_schema = 'public' 
                                     ORDER BY ordinal_position", [$tableName]);
            
            $sql .= "\n\n-- Структура таблицы \"{$tableName}\"\n";
            $sql .= "DROP TABLE IF EXISTS \"{$tableName}\" CASCADE;\n";
            $sql .= "CREATE TABLE \"{$tableName}\" (\n";
            
            $columns = [];
            foreach ($createTable as $column) {
                // Если поле автоинкремент (nextval), используем SERIAL/BIGSERIAL
                if ($column->column_default && strpos($column->column_default, 'nextval') !== false) {
                    $serialType = ($column->data_type === 'bigint') ? 'BIGSERIAL' : 'SERIAL';
                    $columnDef = "\"{$column->column_name}\" {$serialType}";
                } else {
                    $columnDef = "\"{$column->column_name}\" {$column->data_type}";
                    if ($column->is_nullable === 'NO') {
                        $columnDef .= " NOT NULL";
                    }
                    if ($column->column_default) {
                        $columnDef .= " DEFAULT {$column->column_default}";
                    }
                }
                $columns[] = $columnDef;
            }
            
            $sql .= implode(",\n", $columns) . "\n);\n\n";
            
            // Данные таблицы
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Данные таблицы \"{$tableName}\"\n";
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array)$row as $colName => $value) {
                        $type = $columnTypes[$colName] ?? '';
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif ($type === 'boolean') {
                            $values[] = ($value === true || $value === 1 || $value === '1' || $value === 'true') ? 'TRUE' : 'FALSE';
                        } elseif ($type === 'bytea') {
                            $values[] = "E'\\x" . bin2hex($value) . "'";
                        } else {
                            if (is_string($value)) {
                                $escaped = str_replace("'", "''", $value);
                                $values[] = "'{$escaped}'";
                            } else {
                                $values[] = $value;
                            }
                        }
                    }
                    $sql .= "INSERT INTO \"{$tableName}\" VALUES (" . implode(', ', $values) . ");\n";
                }
            }
        }
        
        File::put($dbPath, $sql);
    }

    protected function backupFiles($backupDir)
    {
        $filesPath = $backupDir . '/files/';
        File::makeDirectory($filesPath, 0755, true);
        
        // Бэкап загруженных файлов
        $storagePath = storage_path('app/public');
        if (File::exists($storagePath)) {
            File::copyDirectory($storagePath, $filesPath . 'storage');
        }
        
        // Бэкап логов
        $logsPath = storage_path('logs');
        if (File::exists($logsPath)) {
            File::copyDirectory($logsPath, $filesPath . 'logs');
        }
    }

    protected function createBackupMetadata($backupDir, $backupName)
    {
        // Получаем количество таблиц (PostgreSQL)
        $tablesCount = DB::select("SELECT COUNT(*) as count FROM pg_tables WHERE schemaname = 'public'")[0]->count;
        
        $metadata = [
            'name' => $backupName,
            'created_at' => Carbon::now()->toISOString(),
            'version' => '1.0',
            'database' => config('database.default'),
            'tables_count' => $tablesCount,
            'size' => $this->getBackupSize($backupDir)
        ];
        
        File::put($backupDir . '/metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    protected function getBackupSize($backupDir)
    {
        $size = 0;
        $files = File::allFiles($backupDir);
        
        foreach ($files as $file) {
            $size += $file->getSize();
        }
        
        return $this->formatBytes($size);
    }

    protected function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    protected function cleanOldBackups()
    {
        $backupsPath = storage_path("app/{$this->backupPath}");
        
        if (!File::exists($backupsPath)) {
            return;
        }
        
        $backups = File::directories($backupsPath);
        
        if (count($backups) > $this->maxBackups) {
            // Сортируем по времени создания (старые первыми)
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Удаляем старые бэкапы
            $toDelete = array_slice($backups, 0, count($backups) - $this->maxBackups);
            
            foreach ($toDelete as $backup) {
                File::deleteDirectory($backup);
            }
        }
    }

    public function getBackupsList()
    {
        $backupsPath = storage_path("app/{$this->backupPath}");
        
        if (!File::exists($backupsPath)) {
            return [];
        }
        
        $backups = [];
        $directories = File::directories($backupsPath);
        
        foreach ($directories as $directory) {
            $metadataPath = $directory . '/metadata.json';
            
            if (File::exists($metadataPath)) {
                $metadata = json_decode(File::get($metadataPath), true);
                $backups[] = $metadata;
            }
        }
        
        // Сортируем по дате создания (новые первыми)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $backups;
    }

    public function restoreBackup($backupName)
    {
        try {
            $backupDir = storage_path("app/{$this->backupPath}{$backupName}");
            
            if (!File::exists($backupDir)) {
                throw new \Exception("Резервная копия не найдена");
            }
            
            $metadataPath = $backupDir . '/metadata.json';
            if (!File::exists($metadataPath)) {
                throw new \Exception("Метаданные резервной копии не найдены");
            }
            
            $metadata = json_decode(File::get($metadataPath), true);
            
            // Восстанавливаем базу данных
            $this->restoreDatabase($backupDir);
            
            // Восстанавливаем файлы
            $this->restoreFiles($backupDir);
            
            return [
                'success' => true,
                'message' => "Резервная копия '{$backupName}' успешно восстановлена"
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function restoreDatabase($backupDir)
    {
        $dbPath = $backupDir . '/database.sql';
        
        if (!File::exists($dbPath)) {
            throw new \Exception("Файл базы данных не найден");
        }
        
        $sql = File::get($dbPath);
        
        // Разбиваем SQL на отдельные запросы
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        // Отключаем проверку внешних ключей (PostgreSQL)
        DB::statement('SET session_replication_role = replica');
        
        foreach ($queries as $query) {
            if (!empty($query)) {
                DB::unprepared($query);
            }
        }
        
        // Включаем проверку внешних ключей (PostgreSQL)
        DB::statement('SET session_replication_role = DEFAULT');
    }

    protected function restoreFiles($backupDir)
    {
        $filesPath = $backupDir . '/files/';
        
        if (File::exists($filesPath . 'storage')) {
            $storagePath = storage_path('app/public');
            File::deleteDirectory($storagePath);
            File::copyDirectory($filesPath . 'storage', $storagePath);
        }
        
        if (File::exists($filesPath . 'logs')) {
            $logsPath = storage_path('logs');
            File::deleteDirectory($logsPath);
            File::copyDirectory($filesPath . 'logs', $logsPath);
        }
    }
} 