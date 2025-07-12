<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Method;
use Illuminate\Http\Request;

class MethodController extends Controller
{
    public function index(Request $request) {
        $courses = Course::all();
        $methods = Method::query();
        
        // Фильтр по курсу
        if ($request->filled('course')) {
            $selectedCourse = $request->input('course');
            $course = Course::where('name', $selectedCourse)->first();
            
            if ($course) {
                $methods->where('course_id', $course->id);
            }
        }
        
        $methods = $methods->get();
        
        return view('admin/method', compact('courses', 'methods'));
    }   

    public function editMode(Request $request) {
        $courses = Course::all();
        $methods = Method::query();
        
        // Фильтр по курсу
        if ($request->filled('course')) {
            $selectedCourse = $request->input('course');
            $course = Course::where('name', $selectedCourse)->first();
            
            if ($course) {
                $methods->where('course_id', $course->id);
            }
        }
        
        $methods = $methods->get();
        
        return view('admin/method', compact('courses', 'methods'));
    }
    
    public function delete(Request $request) {
        $methodId = $request->input('method_id');
        $method = Method::find($methodId);
        
        if ($method) {
            // Удаляем файлы из папки перед удалением записи
            $this->deleteMethodFiles($method);
            
            $method->delete();
            return redirect()->back()->with('delete_success', 'Метод успешно удален');
        }
        
        return redirect()->back()->with('error', 'Метод не найден');
    }
    
    /**
     * Удаляет файлы метода из папки storage
     */
    private function deleteMethodFiles($method) {
        try {
            // Список полей с файлами
            $fileFields = ['homework', 'lesson', 'exercise', 'book', 'presentation', 'test', 'article'];
            
            foreach ($fileFields as $field) {
                if (!empty($method->$field) && is_array($method->$field)) {
                    foreach ($method->$field as $filePath) {
                        // Проверяем, что это локальный файл (начинается с /storage/)
                        if (strpos($filePath, '/storage/methodfile/') === 0) {
                            // Преобразуем путь для доступа к файлу
                            $fullPath = storage_path('app/public' . str_replace('/storage', '', $filePath));
                            
                            // Проверяем существование файла и удаляем его
                            if (file_exists($fullPath)) {
                                if (unlink($fullPath)) {
                                    \Log::info("Файл удален: {$fullPath}");
                                } else {
                                    \Log::error("Не удалось удалить файл: {$fullPath}");
                                }
                            } else {
                                \Log::warning("Файл не найден: {$fullPath}");
                            }
                        }
                    }
                }
            }
            
            \Log::info("Все файлы метода ID {$method->id} обработаны");
            
        } catch (\Exception $e) {
            \Log::error("Ошибка при удалении файлов метода ID {$method->id}: " . $e->getMessage());
        }
    }
    
    public function store(Request $request) {
        try {
            // Логируем все данные запроса для диагностики
            \Log::info("=== НАЧАЛО МЕТОДА STORE ===");
            \Log::info("Все данные запроса: " . json_encode($request->all()));
            \Log::info("Файлы в запросе: " . json_encode($request->allFiles()));
            
            // Логируем настройки PHP для диагностики
            \Log::info("=== НАСТРОЙКИ PHP ===");
            \Log::info("upload_max_filesize: " . ini_get('upload_max_filesize'));
            \Log::info("post_max_size: " . ini_get('post_max_size'));
            \Log::info("max_file_uploads: " . ini_get('max_file_uploads'));
            \Log::info("max_execution_time: " . ini_get('max_execution_time'));
            \Log::info("memory_limit: " . ini_get('memory_limit'));
            \Log::info("=== КОНЕЦ НАСТРОЕК PHP ===");
            
            // Детальное логирование всех файловых полей
            \Log::info("=== ДЕТАЛЬНАЯ ДИАГНОСТИКА ФАЙЛОВ ===");
            $fileFields = ['homework_files', 'lesson_files', 'exercise_files', 'book_files', 'presentation_files', 'test_files', 'article_files'];
            foreach ($fileFields as $field) {
                \Log::info("--- Поле: {$field} ---");
                \Log::info("hasFile({$field}): " . ($request->hasFile($field) ? 'true' : 'false'));
                \Log::info("file({$field}): " . ($request->file($field) ? 'не null' : 'null'));
                
                // Дополнительная диагностика для проблемных полей
                if ($request->file($field) && !$request->hasFile($field)) {
                    \Log::info("!!! ПРОБЛЕМА: file() не null, но hasFile() false !!!");
                    $fileData = $request->file($field);
                    \Log::info("Тип данных: " . gettype($fileData));
                    \Log::info("Содержимое: " . json_encode($fileData));
                    
                    if (is_array($fileData)) {
                        foreach ($fileData as $index => $item) {
                            \Log::info("  Элемент {$index}: " . json_encode($item));
                            
                            // Дополнительная диагностика для пустых объектов
                            if (is_object($item)) {
                                \Log::info("    Тип элемента: " . get_class($item));
                                \Log::info("    Методы: " . json_encode(get_class_methods($item)));
                                
                                // Пытаемся получить свойства файла
                                if (method_exists($item, 'getClientOriginalName')) {
                                    \Log::info("    Имя файла: " . $item->getClientOriginalName());
                                }
                                if (method_exists($item, 'getSize')) {
                                    \Log::info("    Размер: " . $item->getSize());
                                }
                                if (method_exists($item, 'isValid')) {
                                    \Log::info("    Валиден: " . ($item->isValid() ? 'да' : 'нет'));
                                }
                                if (method_exists($item, 'getError')) {
                                    \Log::info("    Ошибка: " . $item->getError());
                                }
                            }
                        }
                    }
                }
                
                if ($request->hasFile($field)) {
                    $files = $request->file($field);
                    \Log::info("Тип файлов: " . gettype($files));
                    \Log::info("is_array: " . (is_array($files) ? 'true' : 'false'));
                    
                    if (is_array($files)) {
                        \Log::info("Количество файлов: " . count($files));
                        foreach ($files as $index => $file) {
                            \Log::info("  Файл {$index}: " . ($file ? $file->getClientOriginalName() : 'null'));
                            if ($file) {
                                \Log::info("    Размер: " . $file->getSize() . " байт");
                                \Log::info("    MIME: " . $file->getMimeType());
                                \Log::info("    Валиден: " . ($file->isValid() ? 'да' : 'нет'));
                                \Log::info("    Ошибка: " . $file->getError());
                            }
                        }
                    } else {
                        \Log::info("Одиночный файл: " . ($files ? $files->getClientOriginalName() : 'null'));
                        if ($files) {
                            \Log::info("  Размер: " . $files->getSize() . " байт");
                            \Log::info("  MIME: " . $files->getMimeType());
                            \Log::info("  Валиден: " . ($files->isValid() ? 'да' : 'нет'));
                            \Log::info("  Ошибка: " . $files->getError());
                        }
                    }
                }
            }
            \Log::info("=== КОНЕЦ ДИАГНОСТИКИ ФАЙЛОВ ===");
            
            // Получаем выбранный курс из запроса или используем первый доступный
            $selectedCourse = $request->input('course', 'C++'); // По умолчанию C++
            $course = Course::where('name', $selectedCourse)->first();
            
            if (!$course) {
                // Если курс не найден, берем первый доступный
                $course = Course::first();
            }
            
            if (!$course) {
                throw new \Exception('Нет доступных курсов');
            }
            
            // Преобразуем текстовые поля в массивы
            $data = $request->all();
            $data['course_id'] = $course->id; // Устанавливаем course_id
            
            // Обрабатываем поля, которые должны быть массивами
            $arrayFields = [
                'title_homework', 'homework', 'title_lesson', 'lesson',
                'title_exercise', 'exercise', 'title_book', 'book',
                'title_video', 'video', 'title_presentation', 'presentation',
                'title_test', 'test', 'title_article', 'article'
            ];
            
            foreach ($arrayFields as $field) {
                if ($request->filled($field)) {
                    $value = $request->input($field);
                    // Очищаем от лишних символов и разбиваем на строки
                    $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $value))));
                    $data[$field] = $lines;
                } else {
                    $data[$field] = [];
                }
            }
            
            // Обрабатываем загруженные файлы
            $fileFields = [
                'homework_files' => 'homework',
                'lesson_files' => 'lesson', 
                'exercise_files' => 'exercise',
                'book_files' => 'book',
                'presentation_files' => 'presentation',
                'test_files' => 'test',
                'article_files' => 'article'
            ];
            
            foreach ($fileFields as $fileField => $targetField) {
                \Log::info("Проверяем поле файлов: {$fileField}");
                
                // Проверяем как hasFile(), так и file() для надежности
                $hasFiles = $request->hasFile($fileField);
                $fileData = $request->file($fileField);
                
                if ($hasFiles || ($fileData && !empty($fileData))) {
                    \Log::info("Найдены файлы в поле: {$fileField}");
                    $uploadedFiles = [];
                    $uploadedTitles = [];
                    $files = $fileData;
                    
                    // Убеждаемся, что это массив
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    \Log::info("Количество файлов в поле {$fileField}: " . count($files));
                    
                    foreach ($files as $index => $file) {
                        \Log::info("=== НАЧАЛО ОБРАБОТКИ ФАЙЛА {$index} ===");
                        \Log::info("Обрабатываем файл {$index} в поле {$fileField}: " . ($file ? $file->getClientOriginalName() : 'null'));
                        
                        if ($file) {
                            // Сначала проверяем размер файла
                            $fileSize = $file->getSize();
                            \Log::info("Размер файла: " . $fileSize . " байт");
                            
                            if ($fileSize <= 0) {
                                \Log::error("Файл имеет нулевой размер: " . $file->getClientOriginalName());
                                \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (НУЛЕВОЙ РАЗМЕР) ===");
                                continue;
                            }
                            
                            \Log::info("Детали файла {$file->getClientOriginalName()}:");
                            \Log::info("  - Размер: " . $fileSize . " байт");
                            \Log::info("  - MIME тип: " . $file->getMimeType());
                            \Log::info("  - Валиден: " . ($file->isValid() ? 'да' : 'нет'));
                            \Log::info("  - Ошибка загрузки: " . $file->getError());
                            \Log::info("  - Путь к временному файлу: " . $file->getPathname());
                            \Log::info("  - Существует временный файл: " . (file_exists($file->getPathname()) ? 'да' : 'нет'));
                            
                            // Проверяем путь к временному файлу
                            if (empty($file->getPathname()) || !file_exists($file->getPathname())) {
                                \Log::error("Временный файл не существует: " . $file->getClientOriginalName());
                                \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (НЕТ ВРЕМЕННОГО ФАЙЛА) ===");
                                continue;
                            }
                            
                            if (!$file->isValid()) {
                                \Log::error("Файл не прошел валидацию: " . $file->getClientOriginalName());
                                \Log::error("Код ошибки: " . $file->getError());
                                \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (НЕ ВАЛИДЕН) ===");
                                continue;
                            }
                        } else {
                            \Log::error("Файл равен null для индекса {$index}");
                            \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (NULL) ===");
                            continue;
                        }
                        
                        if ($file && $file->isValid() && $file->getSize() > 0 && file_exists($file->getPathname())) {
                            try {
                                // Генерируем уникальное имя файла
                                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                // Определяем папку для загрузки
                                $folder = str_replace('_files', '', $fileField);
                                $storagePath = "public/methodfile/{$folder}";
                                
                                \Log::info("Папка для сохранения: {$storagePath}");
                                
                                // Проверяем и создаем папку, если нужно
                                $fullPath = storage_path("app/{$storagePath}");
                                if (!file_exists($fullPath)) {
                                    mkdir($fullPath, 0755, true);
                                    \Log::info("Создана папка: {$fullPath}");
                                }
                                
                                // Проверяем права на запись
                                if (!is_writable($fullPath)) {
                                    \Log::error("Папка не доступна для записи: {$fullPath}");
                                    \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (НЕТ ПРАВ) ===");
                                    continue;
                                }
                                
                                // Сохраняем файл напрямую
                                $filePath = $fullPath . '/' . $fileName;
                                if (move_uploaded_file($file->getPathname(), $filePath)) {
                                    // Добавляем путь к файлу в массив (правильный путь для доступа)
                                    $uploadedFiles[] = "/storage/methodfile/{$folder}/" . $fileName;
                                    
                                    // Генерируем название из имени файла (без расширения)
                                    $originalName = $file->getClientOriginalName();
                                    $title = pathinfo($originalName, PATHINFO_FILENAME);
                                    $uploadedTitles[] = $title;
                                    
                                    \Log::info("Файл успешно загружен: /storage/methodfile/{$folder}/" . $fileName);
                                    \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (УСПЕХ) ===");
                                } else {
                                    \Log::error("Не удалось переместить файл: {$file->getClientOriginalName()}");
                                    \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (ОШИБКА ПЕРЕМЕЩЕНИЯ) ===");
                                }
                            } catch (\Exception $e) {
                                \Log::error("Ошибка загрузки файла: " . $e->getMessage());
                                \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (ИСКЛЮЧЕНИЕ) ===");
                                continue;
                            }
                        } else {
                            \Log::warning("Файл недействителен, имеет нулевой размер или отсутствует временный файл в поле {$fileField}");
                            \Log::info("=== КОНЕЦ ОБРАБОТКИ ФАЙЛА {$index} (НЕ ВАЛИДЕН) ===");
                        }
                    }
                    
                    // Объединяем с существующими ссылками и названиями
                    if (!empty($uploadedFiles)) {
                        $existingLinks = $data[$targetField] ?? [];
                        $data[$targetField] = array_merge($existingLinks, $uploadedFiles);
                        
                        // Объединяем названия
                        $titleField = 'title_' . $targetField;
                        $existingTitles = $data[$titleField] ?? [];
                        $data[$titleField] = array_merge($existingTitles, $uploadedTitles);
                    }
                } else {
                    \Log::info("Файлы не найдены в поле: {$fileField}");
                }
            }
            
            // Обрабатываем ссылки и генерируем названия для них
            $linkFields = ['homework', 'lesson', 'exercise', 'book', 'video', 'presentation', 'test', 'article'];
            
            foreach ($linkFields as $linkField) {
                $titleField = 'title_' . $linkField;
                
                // Если есть ссылки, но нет названий - генерируем названия
                if (!empty($data[$linkField]) && empty($data[$titleField])) {
                    $generatedTitles = [];
                    
                    foreach ($data[$linkField] as $link) {
                        if (filter_var($link, FILTER_VALIDATE_URL)) {
                            // Это URL - извлекаем название сайта
                            $parsedUrl = parse_url($link);
                            $host = $parsedUrl['host'] ?? 'Ссылка';
                            
                            // Убираем www. и .com/.ru и т.д.
                            $host = preg_replace('/^www\./', '', $host);
                            $host = preg_replace('/\.(com|ru|org|net|edu|gov)$/', '', $host);
                            
                            // Делаем первую букву заглавной
                            $title = ucfirst($host);
                        } else {
                            // Это локальный файл - используем имя файла
                            $fileName = basename($link);
                            $title = pathinfo($fileName, PATHINFO_FILENAME);
                        }
                        
                        $generatedTitles[] = $title;
                    }
                    
                    $data[$titleField] = $generatedTitles;
                }
            }
            
            // Создаем метод
            \Log::info("=== СОЗДАНИЕ ЗАПИСИ METHOD ===");
            \Log::info("Данные для создания: " . json_encode($data));
            
            try {
                $method = Method::create($data);
                \Log::info("Запись Method создана успешно с ID: " . $method->id);
                \Log::info("=== КОНЕЦ СОЗДАНИЯ ЗАПИСИ METHOD ===");
                
                return redirect()->back()->with('success', 'Метод успешно создан');
            } catch (\Exception $e) {
                \Log::error("Ошибка создания записи Method: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
                \Log::info("=== КОНЕЦ СОЗДАНИЯ ЗАПИСИ METHOD (ОШИБКА) ===");
                
                return redirect()->back()->with('error', 'Ошибка при создании метода: ' . $e->getMessage())->withInput();
            }
            
        } catch (\Exception $e) {
            \Log::error("Ошибка создания метода: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при создании метода: ' . $e->getMessage())->withInput();
        }
    }
    
    public function update(Request $request) {
        try {
            \Log::info("=== НАЧАЛО МЕТОДА UPDATE ===");
            \Log::info("Все данные запроса: " . json_encode($request->all()));
            \Log::info("Файлы в запросе: " . json_encode($request->allFiles()));
            
            // Получаем ID метода для обновления
            $methodId = $request->input('method_id');
            $method = Method::find($methodId);
            
            if (!$method) {
                return redirect()->back()->with('error', 'Метод не найден');
            }
            
            // Получаем выбранный курс
            $selectedCourse = $request->input('course', 'C++');
            $course = Course::where('name', $selectedCourse)->first();
            
            if (!$course) {
                $course = Course::first();
            }
            
            if (!$course) {
                throw new \Exception('Нет доступных курсов');
            }
            
            // Подготавливаем данные для обновления
            $data = [
                'course_id' => $course->id,
                'title' => $request->input('title'),
                'title_homework' => $this->processTextareaInput($request->input('title_homework')),
                'homework' => $this->processTextareaInput($request->input('homework')),
                'title_lesson' => $this->processTextareaInput($request->input('title_lesson')),
                'lesson' => $this->processTextareaInput($request->input('lesson')),
                'title_exercise' => $this->processTextareaInput($request->input('title_exercise')),
                'exercise' => $this->processTextareaInput($request->input('exercise')),
                'title_book' => $this->processTextareaInput($request->input('title_book')),
                'book' => $this->processTextareaInput($request->input('book')),
                'title_video' => $this->processTextareaInput($request->input('title_video')),
                'video' => $this->processTextareaInput($request->input('video')),
                'title_presentation' => $this->processTextareaInput($request->input('title_presentation')),
                'presentation' => $this->processTextareaInput($request->input('presentation')),
                'title_test' => $this->processTextareaInput($request->input('title_test')),
                'test' => $this->processTextareaInput($request->input('test')),
                'title_article' => $this->processTextareaInput($request->input('title_article')),
                'article' => $this->processTextareaInput($request->input('article'))
            ];
            
            // Обрабатываем загруженные файлы
            $fileFields = [
                'homework_files' => 'homework',
                'lesson_files' => 'lesson', 
                'exercise_files' => 'exercise',
                'book_files' => 'book',
                'presentation_files' => 'presentation',
                'test_files' => 'test',
                'article_files' => 'article'
            ];
            
            foreach ($fileFields as $fileField => $targetField) {
                if ($request->hasFile($fileField)) {
                    $uploadedFiles = [];
                    $uploadedTitles = [];
                    $files = $request->file($fileField);
                    
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    foreach ($files as $file) {
                        if ($file && $file->isValid() && $file->getSize() > 0) {
                            try {
                                // Генерируем уникальное имя файла
                                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                // Определяем папку для загрузки
                                $folder = str_replace('_files', '', $fileField);
                                $storagePath = "public/methodfile/{$folder}";
                                
                                // Проверяем и создаем папку, если нужно
                                $fullPath = storage_path("app/{$storagePath}");
                                if (!file_exists($fullPath)) {
                                    mkdir($fullPath, 0755, true);
                                }
                                
                                // Сохраняем файл
                                $filePath = $fullPath . '/' . $fileName;
                                if (move_uploaded_file($file->getPathname(), $filePath)) {
                                    $uploadedFiles[] = "/storage/methodfile/{$folder}/" . $fileName;
                                    
                                    // Генерируем название из имени файла
                                    $originalName = $file->getClientOriginalName();
                                    $title = pathinfo($originalName, PATHINFO_FILENAME);
                                    $uploadedTitles[] = $title;
                                }
                            } catch (\Exception $e) {
                                \Log::error("Ошибка загрузки файла: " . $e->getMessage());
                                continue;
                            }
                        }
                    }
                    
                    // Объединяем с существующими ссылками и названиями
                    if (!empty($uploadedFiles)) {
                        $existingLinks = $data[$targetField] ?? [];
                        $data[$targetField] = array_merge($existingLinks, $uploadedFiles);
                        
                        // Объединяем названия
                        $titleField = 'title_' . $targetField;
                        $existingTitles = $data[$titleField] ?? [];
                        $data[$titleField] = array_merge($existingTitles, $uploadedTitles);
                    }
                }
            }
            
            // Обрабатываем ссылки и генерируем названия для них
            $linkFields = ['homework', 'lesson', 'exercise', 'book', 'video', 'presentation', 'test', 'article'];
            
            foreach ($linkFields as $linkField) {
                $titleField = 'title_' . $linkField;
                
                // Если есть ссылки, но нет названий - генерируем названия
                if (!empty($data[$linkField]) && empty($data[$titleField])) {
                    $generatedTitles = [];
                    
                    foreach ($data[$linkField] as $link) {
                        if (filter_var($link, FILTER_VALIDATE_URL)) {
                            // Это URL - извлекаем название сайта
                            $parsedUrl = parse_url($link);
                            $host = $parsedUrl['host'] ?? 'Ссылка';
                            
                            // Убираем www. и .com/.ru и т.д.
                            $host = preg_replace('/^www\./', '', $host);
                            $host = preg_replace('/\.(com|ru|org|net|edu|gov)$/', '', $host);
                            
                            // Делаем первую букву заглавной
                            $title = ucfirst($host);
                        } else {
                            // Это локальный файл - используем имя файла
                            $fileName = basename($link);
                            $title = pathinfo($fileName, PATHINFO_FILENAME);
                        }
                        
                        $generatedTitles[] = $title;
                    }
                    
                    $data[$titleField] = $generatedTitles;
                }
            }
            
            // Обновляем метод
            $method->update($data);
            
            \Log::info("Метод успешно обновлен с ID: " . $method->id);
            \Log::info("=== КОНЕЦ МЕТОДА UPDATE ===");
            
            return redirect()->back()->with('success', 'Метод успешно обновлен');
            
        } catch (\Exception $e) {
            \Log::error("Ошибка обновления метода: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при обновлении метода: ' . $e->getMessage())->withInput();
        }
    }
    
    private function processTextareaInput($input) {
        if (empty($input)) {
            return null;
        }
        
        // Разбиваем по строкам и убираем пустые строки
        $lines = array_filter(explode("\n", $input), function($line) {
            return trim($line) !== '';
        });
        
        return empty($lines) ? null : array_values($lines);
    }
}
