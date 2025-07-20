@extends('admin.layouts.head')
@section('head')

@endsection

@include('teacher.nav')

<div class="container">
    <div class="content">
        <div class="lesson-container">
            <div class="lesson-header">
                <h1 style="font-size: 2.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; text-align: center;">
                    {{ $lesson->subject ?? 'Урок' }} <span style="color: var(--text-secondary); font-weight: 400;">— {{ $lesson->name_group }}</span>
                </h1>
                <p style="color: var(--text-secondary); font-size: 1.1rem; text-align: center; margin-bottom: 32px;">
                    {{ $lesson->start_time }} - {{ $lesson->end_time }}
                </p>
            </div>

            <form method="POST" action="{{ route('teacher.lesson.attendance', ['lesson_id' => $lesson->id]) }}" id="attendance-form" enctype="multipart/form-data">
                @csrf
                
                <!-- Секция посещаемости -->
                <div class="attendance-section">
                    <div class="section-card">
                        <h2 style="font-size: 1.3rem; font-weight: 600; color: var(--text-primary); margin-bottom: 24px; text-align: center;">
                            Посещаемость и оценка за урок
                        </h2>
                        
                        <div class="students-table-container">
                            <!-- Десктопная версия таблицы -->
                            <table class="students-table desktop-table">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Пришел</th>
                                        <th>Оценка за урок</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->fio }}</td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="attendance[{{ $student->id }}]" value="1" {{ old('attendance.' . $student->id, $studentAttendance[$student->id] ?? false) ? 'checked' : '' }}>
                                        </td>
                                        <td style="text-align: center;">
                                            <select name="grade[{{ $student->id }}]" class="grade-select">
                                                <option value="">—</option>
                                                <option value="2" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 2 ? 'selected' : '' }}>2</option>
                                                <option value="3" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 3 ? 'selected' : '' }}>3</option>
                                                <option value="4" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 4 ? 'selected' : '' }}>4</option>
                                                <option value="5" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 5 ? 'selected' : '' }}>5</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Мобильная версия с карточками -->
                            <div class="students-cards mobile-cards">
                                @foreach($students as $student)
                                <div class="student-card">
                                    <div class="student-info">
                                        <h3 class="student-name">{{ $student->fio }}</h3>
                                    </div>
                                    <div class="student-controls">
                                        <div class="control-group">
                                            <label class="control-label">Пришел:</label>
                                            <input type="checkbox" name="attendance[{{ $student->id }}]" value="1" class="attendance-checkbox" {{ old('attendance.' . $student->id, $studentAttendance[$student->id] ?? false) ? 'checked' : '' }}>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Оценка:</label>
                                            <select name="grade[{{ $student->id }}]" class="grade-select-mobile">
                                                <option value="">—</option>
                                                <option value="2" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 2 ? 'selected' : '' }}>2</option>
                                                <option value="3" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 3 ? 'selected' : '' }}>3</option>
                                                <option value="4" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 4 ? 'selected' : '' }}>4</option>
                                                <option value="5" {{ old('grade.' . $student->id, $studentGrades[$student->id] ?? '') == 5 ? 'selected' : '' }}>5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Секция домашнего задания -->
                <div class="homework-section">
                    <div class="section-card">
                        <h2 style="font-size: 1.3rem; font-weight: 600; color: var(--text-primary); margin-bottom: 24px; text-align: center;">
                            Домашнее задание для группы
                        </h2>
                        
                        @if($currentHomework)
                            <div class="current-homework">
                                <div class="homework-info">
                                    <span class="homework-label">Задано:</span>
                                    @if($currentHomework->description === 'Из методпакета')
                                        <span class="homework-type">Из методпакета</span>
                                    @elseif($currentHomework->description === 'Загружено преподавателем')
                                        <span class="homework-type">Файл преподавателя</span>
                                    @endif
                                    @if($currentHomework->file_path)
                                        @php
                                            $filePath = $currentHomework->file_path;
                                            if (strpos($filePath, 'http') === 0) {
                                                $fileUrl = $filePath;
                                            } elseif (strpos($filePath, '/storage/') === 0) {
                                                $fileUrl = asset(ltrim($filePath, '/'));
                                            } elseif (strpos($filePath, 'storage/') === 0) {
                                                $fileUrl = asset($filePath);
                                            } else {
                                                $fileUrl = asset('storage/' . ltrim($filePath, '/'));
                                            }
                                        @endphp
                                        <a href="{{ $fileUrl }}" target="_blank" class="homework-link">Открыть файл</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="homework-options">
                            <div class="homework-option">
                                <label class="option-label">Выбрать из методпакета:</label>
                                <select id="homework_from_method" name="homework_from_method" class="homework-select">
                                    <option value="">— Не выбрано —</option>
                                    @foreach($homeworkTitles as $i => $title)
                                        <option value="{{ $homeworkFiles[$i] ?? '' }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="homework-divider">
                                <span>или</span>
                            </div>
                            
                            <div class="homework-option">
                                <label class="option-label">Загрузить свой файл:</label>
                                <input type="file" id="homework_file" name="homework_file" class="homework-file-input">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="save-btn">Сохранить</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Импорт цветовых переменных */
@import url('./colors.css');

/* Базовые стили */
* {
    box-sizing: border-box;
}

body {
    font-family: 'Source Serif Pro', serif;
    background:  #d5d5d5;    
    margin: 0;
    padding: 0;
}

.content {
    padding: 20px;
    width: 100%;
    min-height: 100vh;
 
}

.lesson-container {
    max-width: 100%;
    margin: 30px auto;
    background-color: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 6px var(--card-shadow);
    width: 100%;
    border: 1px solid var(--card-border);
}

.lesson-header {
    text-align: center;
    margin-bottom: 32px;
}

.attendance-section,
.homework-section {
    margin-bottom: 32px;
}

.section-card {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border-light);
}

.students-table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px var(--card-shadow);
    border: 1px solid var(--table-border);
}

.students-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}

.students-table th {
    background-color: var(--table-header);
    color: var(--text-light);
    font-weight: 600;
    vertical-align: middle;
    text-align: center;
    padding: 12px 15px;
    border-bottom: 2px solid var(--table-border);
}

.students-table td {
    padding: 16px 15px;
    border-bottom: 1px solid var(--table-border);
    color: var(--text-primary);
    vertical-align: middle;
    text-align: center;
}

/* Фиксированная ширина колонок */
.students-table th:nth-child(1),
.students-table td:nth-child(1) {
    width: 50%;
}

.students-table th:nth-child(2),
.students-table td:nth-child(2) {
    width: 25%;
}

.students-table th:nth-child(3),
.students-table td:nth-child(3) {
    width: 25%;
}

/* Мобильная версия с карточками */
.mobile-cards {
    display: none;
    transition: all 0.3s ease;
}

.desktop-table {
    transition: all 0.3s ease;
}

.student-card {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border-light);
    transition: all 0.2s ease;
}

.student-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.student-info {
    margin-bottom: 16px;
}

.student-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.student-controls {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.control-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.control-label {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.attendance-checkbox {
    transform: scale(1.3);
    accent-color: var(--btn-primary);
}

.grade-select-mobile {
    width: 80px;
    text-align: center;
    border-radius: 6px;
    border: 1px solid var(--input-border);
    padding: 6px 8px;
    background: var(--input-bg);
    color: var(--text-primary);
    font-size: 0.95rem;
}

.grade-select-mobile:focus {
    outline: none;
    border-color: var(--input-focus);
}

.students-table tr:nth-child(even) {
    background-color: var(--table-row-even);
}

.students-table tr:nth-child(odd) {
    background-color: var(--table-row-odd);
}

.students-table tr:hover {
    background-color: var(--bg-secondary);
}

.students-table tr:last-child td {
    border-bottom: none;
}

.grade-select {
    width: 60px;
    text-align: center;
    border-radius: 6px;
    border: 1px solid var(--input-border);
    padding: 4px 8px;
    background: var(--input-bg);
    color: var(--text-primary);
    margin: 0 auto;
    display: block;
}

.grade-select:focus {
    outline: none;
    border-color: var(--input-focus);
}

/* Центрирование чекбоксов */
.students-table input[type="checkbox"] {
    margin: 0 auto;
    display: block;
    transform: scale(1.2);
}

.current-homework {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 24px;
    border: 1px solid var(--border-color);
}

.homework-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.homework-label {
    font-weight: 600;
    color: var(--btn-primary);
}

.homework-type {
    color: var(--text-secondary);
}

.homework-link {
    color: var(--btn-primary);
    text-decoration: underline;
    font-weight: 500;
    margin-left: 10px;
}

.homework-link:hover {
    color: var(--btn-primary-hover);
}

.homework-options {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.homework-option {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.option-label {
    font-weight: 500;
    color: var(--text-primary);
}

.homework-select,
.homework-file-input {
    width: 100%;
    border-radius: 8px;
    border: 1.5px solid var(--input-border);
    padding: 12px 14px;
    font-size: 1rem;
    background: var(--input-bg);
    color: var(--text-primary);
    transition: border 0.2s;
}

.homework-select:focus,
.homework-file-input:focus {
    outline: none;
    border-color: var(--input-focus);
}

.homework-divider {
    text-align: center;
    color: var(--text-muted);
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 1px;
    position: relative;
    margin: 20px 0;
}

.homework-divider::before,
.homework-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 45%;
    height: 1px;
    background: var(--border-color);
}

.homework-divider::before {
    left: 0;
}

.homework-divider::after {
    right: 0;
}

.form-actions {
    text-align: center;
    margin-top: 24px;
}

.save-btn {
    width: 100%;
    max-width: 260px;
    padding: 15px 0;
    background: var(--btn-primary);
    color: var(--text-light);
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1.15rem;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(110, 1, 4, 0.08);
    transition: all 0.2s ease;
}

.save-btn:hover {
    background: var(--btn-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(110, 1, 4, 0.15);
}

@media (max-width: 768px) {
    .lesson-container {
        margin: 20px auto;
        padding: 20px;
        border-radius: 12px;
    }
    
    .section-card {
        padding: 20px;
        border-radius: 10px;
    }
    
    .homework-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .homework-link {
        margin-left: 0;
    }
    
    .students-table-container {
        font-size: 14px;
    }
    
    .students-table th,
    .students-table td {
        padding: 10px 12px;
    }
    
    /* Показываем мобильные карточки, скрываем таблицу */
    .mobile-cards {
        display: block;
    }
    
    .desktop-table {
        display: none;
    }
    
    /* Убираем контейнер таблицы для мобильной версии */
    .students-table-container {
        overflow: visible;
        border-radius: 0;
        box-shadow: none;
        border: none;
    }
}

@media (max-width: 1024px) {
    body {  
        padding-top: 50px;
    }
}
</style>

<script>
const select = document.getElementById('homework_from_method');
const fileInput = document.getElementById('homework_file');
const form = document.getElementById('attendance-form');

select.addEventListener('change', function() {
    if (this.value) {
        fileInput.disabled = true;
        fileInput.value = ''; // Очищаем файл
    } else {
        fileInput.disabled = false;
    }
});

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        select.disabled = true;
        select.value = ''; // Очищаем селект
    } else {
        select.disabled = false;
    }
});

// Перед отправкой формы проверяем файл
form.addEventListener('submit', function(e) {
    if (fileInput.files.length === 0) {
        // Если файл не выбран, удаляем поле из формы
        fileInput.remove();
    }
    // Отключаем все input/select в скрытом блоке, чтобы не было дублей
    if (window.innerWidth < 768) {
        // Мобильная версия видна, отключаем все поля в .desktop-table
        document.querySelectorAll('.desktop-table input, .desktop-table select').forEach(el => el.disabled = true);
    } else {
        // Десктопная версия видна, отключаем все поля в .mobile-cards
        document.querySelectorAll('.mobile-cards input, .mobile-cards select').forEach(el => el.disabled = true);
    }
});
</script> 
