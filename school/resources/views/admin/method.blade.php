@extends('admin.layouts.head')
@section('head')

@vite(['resources/css/method.css'])
@include('admin.layouts.adminNav')

@if (session('delete_success'))
    <div id="delete-success-toast" class="toast-success">
        {{ session('delete_success') }}
    </div>
@endif

@if (session('success'))
    <div id="success-toast" class="toast-success">
        {{ session('success') }}
    </div>
@endif
<div class="container">
    <main class="content">
        <div class="container-method">
            <div class="header">
            <h2>Методпакеты</h2>
                <form method="GET" action="{{ route('method.edit') }}" class="edit-form">
                    <input type="hidden" name="edit_mode" value="{{ isset($_GET['edit_mode']) && $_GET['edit_mode'] == 1 ? '0' : '1' }}">
                    <!-- Сохраняем выбранный курс при переключении режима редактирования -->
                    @if(request('course'))
                        <input type="hidden" name="course" value="{{ request('course') }}">
                    @endif
                    <button type="submit" class="edit-button">
                        @if(isset($_GET['edit_mode']) && $_GET['edit_mode'] == 1)
                            ✕ Отменить
                        @else
                            ✎ Изменить
                        @endif
                    </button>
                </form>
            </div>
            <div class="filters" style="margin-bottom: 20px;">
                <form method="GET" action="{{ route('method') }}" class="filters-form">
                    <select name="course" class="filter">
                        <option value="">Все курсы</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->name }}" {{ request('course') == $course->name ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @if(request('course'))
                        <a href="{{ route('method') }}" class="reset-filters">Сбросить фильтр</a>
                    @endif
                    <button type="submit" class="filter-btn">Применить фильтр</button>
                </form>
            </div>
            <div class="table-responsive">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                @if (isset($_GET['edit_mode']) && $_GET['edit_mode'] == 1) 
                                    <th></th>
                                @endif
                                <th>№</th>
                                <th>Описание</th>
                                <th>Домашние задания</th>
                                <th>Уроки</th>
                                <th>Практические задания</th>
                                <th>Книги</th>
                                <th>Видео</th>
                                <th>Презентации</th>
                                <th>Тесты</th>
                                <th>Статьи</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach ($methods as $method)
                            <?php $i += 1; ?>
                                <tr>
                                    @if (isset($_GET['edit_mode']) && $_GET['edit_mode'] == 1)
                                        <td style="position: relative; width: 50px;">
                                            <div style="display: flex; gap: 5px;">
                                                <button type="button" class="edit-method-btn" 
                                                        style="background: none; border: none; color: #2196f3; cursor: pointer; font-size: 16px; padding: 2px 6px;"
                                                        onclick="openEditMethodModal({{ $method->id }}, '{{ addslashes($method->title) }}', '{{ addslashes(json_encode($method->title_homework ?? [])) }}', '{{ addslashes(json_encode($method->homework ?? [])) }}', '{{ addslashes(json_encode($method->title_lesson ?? [])) }}', '{{ addslashes(json_encode($method->lesson ?? [])) }}', '{{ addslashes(json_encode($method->title_exercise ?? [])) }}', '{{ addslashes(json_encode($method->exercise ?? [])) }}', '{{ addslashes(json_encode($method->title_book ?? [])) }}', '{{ addslashes(json_encode($method->book ?? [])) }}', '{{ addslashes(json_encode($method->title_video ?? [])) }}', '{{ addslashes(json_encode($method->video ?? [])) }}', '{{ addslashes(json_encode($method->title_presentation ?? [])) }}', '{{ addslashes(json_encode($method->presentation ?? [])) }}', '{{ addslashes(json_encode($method->title_test ?? [])) }}', '{{ addslashes(json_encode($method->test ?? [])) }}', '{{ addslashes(json_encode($method->title_article ?? [])) }}', '{{ addslashes(json_encode($method->article ?? [])) }}')">
                                                    ✎
                                                </button>
                                                <form method="POST" action="{{ route('method.delete') }}" class="delete-method-form" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="method_id" value="{{ $method->id }}">
                                                    <button type="submit" class="delete-btn" onclick="return confirm('Вы уверены, что хотите удалить этот метод?')">✕</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                    <td>{{ $i }}</td>
                                    <td>{{ $method->title }}</td>
                                    <td>
                                        @if($method->title_homework && is_array($method->title_homework) && $method->homework && is_array($method->homework))
                                            @for($i = 0; $i < min(count($method->title_homework), count($method->homework)); $i++)
                                                <a href="{{ asset(ltrim($method->homework[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_homework[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_lesson && is_array($method->title_lesson) && $method->lesson && is_array($method->lesson))
                                            @for($i = 0; $i < min(count($method->title_lesson), count($method->lesson)); $i++)
                                                <a href="{{ asset(ltrim($method->lesson[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_lesson[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_exercise && is_array($method->title_exercise) && $method->exercise && is_array($method->exercise))
                                            @for($i = 0; $i < min(count($method->title_exercise), count($method->exercise)); $i++)
                                                <a href="{{ asset(ltrim($method->exercise[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_exercise[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_book && is_array($method->title_book) && $method->book && is_array($method->book))
                                            @for($i = 0; $i < min(count($method->title_book), count($method->book)); $i++)
                                                <a href="{{ asset(ltrim($method->book[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_book[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_video && is_array($method->title_video) && $method->video && is_array($method->video))
                                            @for($i = 0; $i < min(count($method->title_video), count($method->video)); $i++)
                                                <a href="{{ $method->video[$i] }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_video[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_presentation && is_array($method->title_presentation) && $method->presentation && is_array($method->presentation))
                                            @for($i = 0; $i < min(count($method->title_presentation), count($method->presentation)); $i++)
                                                <a href="{{ asset(ltrim($method->presentation[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_presentation[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_test && is_array($method->title_test) && $method->test && is_array($method->test))
                                            @for($i = 0; $i < min(count($method->title_test), count($method->test)); $i++)
                                                <a href="{{ asset(ltrim($method->test[$i], '/')) }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_test[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->title_article && is_array($method->title_article) && $method->article && is_array($method->article))
                                            @for($i = 0; $i < min(count($method->title_article), count($method->article)); $i++)
                                                <a href="{{ $method->article[$i] }}" class="btn btn-primary" target="_blank">
                                                    {{ $method->title_article[$i] }}
                                                </a>
                                            @endfor
                                        @else
                                            Нет данных
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if (isset($_GET['edit_mode']) && $_GET['edit_mode'] == 1)
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 20px;">
                                        <button type="button" class="create-btn" 
                                                style="background: var( --btn-primary); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px;"
                                                onclick="openModal('addMethodModal')">
                                            + Добавить новый метод
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>


    @include('admin.layouts.addMethod')

    @vite(['resources/js/app.js'])

    <!-- Модальное окно для редактирования метода -->
    <div id="editMethodModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editMethodModal')">&times;</span>
            <h2>Редактировать метод</h2>
            
            <form method="POST" action="{{ route('method.update') }}" class="edit-method-form" id="editMethodForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="method_id" id="edit_method_id">
                <input type="hidden" name="course" value="{{ request('course', 'C++') }}">
                
                <div class="form-group">
                    <label for="edit_title">Название метода:</label>
                    <input type="text" id="edit_title" name="title" required>
                    @error('title')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_title_homework">Названия домашних заданий (каждое с новой строки):</label>
                    <textarea id="edit_title_homework" name="title_homework" rows="3" placeholder="Homework 1&#10;Homework 2"></textarea>
                    @error('title_homework')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_lesson">Ссылки на уроки (каждое с новой строки):</label>
                    <textarea id="edit_lesson" name="lesson" rows="3" placeholder="methodfile/lesson/file1.pdf&#10;methodfile/lesson/file2.pdf"></textarea>
                    <div class="file-upload-section">
                        <label for="edit_lesson_files" class="file-upload-label">Или загрузите файлы:</label>
                        <input type="file" id="edit_lesson_files" name="lesson_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                        <div class="file-info">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR</div>
                        <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                    </div>
                    @error('lesson')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_title_exercise">Названия практических заданий (каждое с новой строки):</label>
                    <textarea id="edit_title_exercise" name="title_exercise" rows="3" placeholder="Exercise 1&#10;Exercise 2"></textarea>
                    @error('title_exercise')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_exercise">Ссылки на практические задания (каждое с новой строки):</label>
                    <textarea id="edit_exercise" name="exercise" rows="3" placeholder="methodfile/exercise/file1.pdf&#10;methodfile/exercise/file2.pdf"></textarea>
                    <div class="file-upload-section">
                        <label for="edit_exercise_files" class="file-upload-label">Или загрузите файлы:</label>
                        <input type="file" id="edit_exercise_files" name="exercise_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                        <div class="file-info">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR</div>
                        <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                    </div>
                    @error('exercise')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_title_book">Названия книг (каждое с новой строки):</label>
                    <textarea id="edit_title_book" name="title_book" rows="3" placeholder="Book 1&#10;Book 2"></textarea>
                    @error('title_book')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="edit_book">Ссылки на книги (каждое с новой строки):</label>
                    <textarea id="edit_book" name="book" rows="3" placeholder="methodfile/book/file1.pdf&#10;methodfile/book/file2.pdf"></textarea>
                    <div class="file-upload-section">
                        <label for="edit_book_files" class="file-upload-label">Или загрузите файлы:</label>
                        <input type="file" id="edit_book_files" name="book_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                        <div class="file-info">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR</div>
                        <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                    </div>
                    @error('book')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_title_video">Названия видео (каждое с новой строки):</label>
                    <textarea id="edit_title_video" name="title_video" rows="3" placeholder="Video 1&#10;Video 2"></textarea>
                    @error('title_video')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_video">Ссылки на видео (каждое с новой строки):</label>
                    <textarea id="edit_video" name="video" rows="3" placeholder="https://video1.com&#10;https://video2.com"></textarea>
                    @error('video')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_title_presentation">Названия презентаций (каждое с новой строки):</label>
                    <textarea id="edit_title_presentation" name="title_presentation" rows="3" placeholder="Presentation 1&#10;Presentation 2"></textarea>
                    @error('title_presentation')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_presentation">Ссылки на презентации (каждое с новой строки):</label>
                    <textarea id="edit_presentation" name="presentation" rows="3" placeholder="methodfile/presentation/file1.pdf&#10;methodfile/presentation/file2.pdf"></textarea>
                    <div class="file-upload-section">
                        <label for="edit_presentation_files" class="file-upload-label">Или загрузите файлы:</label>
                        <input type="file" id="edit_presentation_files" name="presentation_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                        <div class="file-info">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR</div>
                        <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                    </div>
                    @error('presentation')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_title_test">Названия тестов (каждое с новой строки):</label>
                    <textarea id="edit_title_test" name="title_test" rows="3" placeholder="Test 1&#10;Test 2"></textarea>
                    @error('title_test')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_test">Ссылки на тесты (каждое с новой строки):</label>
                    <textarea id="edit_test" name="test" rows="3" placeholder="methodfile/test/file1.pdf&#10;methodfile/test/file2.pdf"></textarea>
                    <div class="file-upload-section">
                        <label for="edit_test_files" class="file-upload-label">Или загрузите файлы:</label>
                        <input type="file" id="edit_test_files" name="test_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                        <div class="file-info">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR</div>
                        <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                    </div>
                    @error('test')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_title_article">Названия статей (каждое с новой строки):</label>
                    <textarea id="edit_title_article" name="title_article" rows="3" placeholder="Статья 1&#10;Статья 2"></textarea>
                    @error('title_article')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_article">Ссылки на статьи (каждое с новой строки):</label>
                    <textarea id="edit_article" name="article" rows="3" placeholder="methodfile/article/file1.pdf&#10;https://article2.com"></textarea>
                    @error('article')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="save-btn">Сохранить изменения</button>
            </form>
        </div>
    </div>

    <style>
    .toast-success {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 9999;
        background: #4caf50;
        color: #fff;
        padding: 16px 32px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        font-size: 16px;
        opacity: 0.95;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 0.95; transform: translateY(0); }
    }
    </style>
    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    function openEditMethodModal(methodId, title, titleHomework, homework, titleLesson, lesson, titleExercise, exercise, titleBook, book, titleVideo, video, titlePresentation, presentation, titleTest, test, titleArticle, article) {
        try {
            console.log('Открытие модального окна редактирования МЕТОДА для ID:', methodId);
            
            // Устанавливаем ID метода
            const methodIdElement = document.getElementById('edit_method_id');
            if (methodIdElement) {
                methodIdElement.value = methodId;
                console.log('ID метода установлен:', methodId);
            } else {
                console.error('Элемент edit_method_id не найден');
            }
            
            // Устанавливаем название
            const titleElement = document.getElementById('edit_title');
            if (titleElement) {
                titleElement.value = title;
                console.log('Название установлено:', title);
            } else {
                console.error('Элемент edit_title не найден');
            }
            
            // Устанавливаем данные для каждого типа контента
            console.log('Устанавливаем данные для полей метода...');
            setTextareaFromArray('edit_title_homework', JSON.parse(titleHomework));
            setTextareaFromArray('edit_homework', JSON.parse(homework));
            setTextareaFromArray('edit_title_lesson', JSON.parse(titleLesson));
            setTextareaFromArray('edit_lesson', JSON.parse(lesson));
            setTextareaFromArray('edit_title_exercise', JSON.parse(titleExercise));
            setTextareaFromArray('edit_exercise', JSON.parse(exercise));
            setTextareaFromArray('edit_title_book', JSON.parse(titleBook));
            setTextareaFromArray('edit_book', JSON.parse(book));
            setTextareaFromArray('edit_title_video', JSON.parse(titleVideo));
            setTextareaFromArray('edit_video', JSON.parse(video));
            setTextareaFromArray('edit_title_presentation', JSON.parse(titlePresentation));
            setTextareaFromArray('edit_presentation', JSON.parse(presentation));
            setTextareaFromArray('edit_title_test', JSON.parse(titleTest));
            setTextareaFromArray('edit_test', JSON.parse(test));
            setTextareaFromArray('edit_title_article', JSON.parse(titleArticle));
            setTextareaFromArray('edit_article', JSON.parse(article));
            
            // Открываем модальное окно
            const modal = document.getElementById('editMethodModal');
            if (modal) {
                modal.style.display = 'block';
                console.log('Модальное окно редактирования метода открыто');
            } else {
                console.error('Модальное окно editMethodModal не найдено');
            }
        } catch (error) {
            console.error('Ошибка при открытии модального окна редактирования метода:', error);
        }
    }
    
    function setTextareaFromArray(textareaId, array) {
        try {
            console.log(`Устанавливаем значение для ${textareaId}:`, array);
            const textarea = document.getElementById(textareaId);
            if (textarea && Array.isArray(array)) {
                textarea.value = array.join('\n');
                console.log(`Значение установлено для ${textareaId}:`, array.join('\n'));
            } else if (textarea) {
                textarea.value = '';
                console.log(`Очищено значение для ${textareaId}`);
            } else {
                console.warn(`Элемент с ID '${textareaId}' не найден`);
            }
        } catch (error) {
            console.error(`Ошибка при установке значения для ${textareaId}:`, error);
        }
    }
    
    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    // Автоматическое обновление при изменении курса
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM загружен, проверяем элементы для методов...');
        
        // Проверяем наличие модального окна редактирования методов
        const editModal = document.getElementById('editMethodModal');
        if (editModal) {
            console.log('Модальное окно редактирования МЕТОДОВ найдено');
        } else {
            console.error('Модальное окно редактирования МЕТОДОВ НЕ найдено');
        }
        
        // Проверяем наличие основных элементов формы методов
        const methodIdInput = document.getElementById('edit_method_id');
        const titleInput = document.getElementById('edit_title');
        
        if (methodIdInput) {
            console.log('Поле method_id найдено');
        } else {
            console.error('Поле method_id НЕ найдено');
        }
        
        if (titleInput) {
            console.log('Поле title найдено');
        } else {
            console.error('Поле title НЕ найдено');
        }
        
        // Стилизация кнопки редактирования
        const editButton = document.querySelector('.edit-button');
        if (editButton) {
            const isEditMode = new URLSearchParams(window.location.search).get('edit_mode') === '1';
            if (isEditMode) {
                editButton.classList.add('cancel-mode');
            } else {
                editButton.classList.remove('cancel-mode');
            }
        }
        
        const courseSelect = document.querySelector('select[name="course"]');
        if (courseSelect) {
            courseSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
        
        var toast = document.getElementById('delete-success-toast');
        if (toast) {
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3000);
        }
        
        var successToast = document.getElementById('success-toast');
        if (successToast) {
            setTimeout(function() {
                successToast.style.display = 'none';
            }, 3000);
        }
    });
    </script>
