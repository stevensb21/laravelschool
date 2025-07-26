@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
    @include('teacher.nav')
@endif
<div class="container">
    <div class="content">
        @if(isset($isAdmin) && $isAdmin)
            <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: var(--bg-secondary); border-radius: 8px; max-width: 800px; margin: 20px auto 0 auto;">
                <h2 style="margin: 0; color: var(--text-primary);">Уроки преподавателя: {{ $teacher->fio }}</h2>
                <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: var(--btn-primary); color: var(--text-light); padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
            </div>
        @endif
        
        <div class="lessons-container">
            <div class="lessons-header">
                <h1 style="font-size: 2.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; text-align: center;">
                    @if(isset($isAdmin) && $isAdmin)
                        Уроки преподавателя {{ $teacher->fio }}
                    @else
                        Мои уроки
                    @endif
                </h1>
                <p style="color: var(--text-secondary); font-size: 1.1rem; text-align: center; margin-bottom: 32px;">
                    Планирование и проведение уроков
                </p>
            </div>

            <div class="lessons-content" style="background:var(--card-bg); border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px var(--card-shadow); border: 1px solid var(--card-border);">
                <div class="lessons-card">
                    <h2 style="font-size: 1.3rem; font-weight: 600; color: #020202; margin-bottom: 24px; text-align: center;">
                        Сегодняшние уроки
                    </h2>
                    
                    @if($lessons->isEmpty())
                        <div class="no-lessons">
                            <div class="no-lessons-icon">📚</div>
                            <h3 style="color: var(--text-secondary); font-size: 1.2rem; margin: 16px 0 8px 0;">Сегодня уроков нет</h3>
                            <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Отдохните или подготовьтесь к завтрашним занятиям</p>
                        </div>
                    @else
                        <div class="lessons-list">
                            @foreach($lessons as $lesson)
                                <div class="lesson-item">
                                    <div class="lesson-info">
                                        <div class="lesson-main">
                                            <h3 class="lesson-subject">{{ $lesson->subject ?? $lesson->course ?? 'Без предмета' }}</h3>
                                            <p class="lesson-group">Группа: {{ $lesson->name_group ?? '—' }}</p>
                                        </div>
                                        <div class="lesson-time">
                                            <div class="time-display">{{ $lesson->start_time }} - {{ $lesson->end_time }}</div>
                                            <a href="{{ route('teacher.lesson.students', ['lesson_id' => $lesson->id]) }}"
                                            class="start-lesson-btn">
                                                Начать урок
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
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

.lessons-container {
    max-width: 100%;
    
    background-color: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 6px var(--card-shadow);
    width: 100%;
    border: 1px solid var(--card-border);
}

.lessons-header {
    text-align: center;
    margin-bottom: 32px;
}

.lessons-content {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 24px var(--card-shadow);
    border: 1px solid var(--card-border);
}

.lessons-card {
    width: 100%;
}

.no-lessons {
    text-align: center;
    padding: 48px 20px;
    color: var(--text-muted);
}

.no-lessons-icon {
    font-size: 4rem;
    margin-bottom: 16px;
}

.lessons-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.lesson-item {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border-light);
    transition: all 0.2s ease;
}

.lesson-item:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.lesson-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.lesson-main {
    flex: 1;
}

.lesson-subject {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.lesson-group {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin: 0;
}

.lesson-time {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
}

.time-display {
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 500;
    background: var(--bg-primary);
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.start-lesson-btn {
    display: inline-block;
    padding: 10px 20px;
    background: var(--btn-primary);
    color: var(--text-light);
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(110, 1, 4, 0.1);
}

.start-lesson-btn:hover {
    background: var(--btn-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(110, 1, 4, 0.15);
}

@media (max-width: 768px) {
    .lessons-container {
      
        padding: 0 16px;
    }
    
    .lessons-content {
        padding: 24px 20px;
        border-radius: 12px;
    }
    
    .lesson-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .lesson-time {
        text-align: left;
        align-items: flex-start;
        width: 100%;
    }
    
    .start-lesson-btn {
        width: 100%;
        text-align: center;
    }
    
    .time-display {
        align-self: flex-start;
    }
}


</style>



