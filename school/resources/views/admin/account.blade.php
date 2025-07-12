@extends('admin.layouts.head')
@section('head')
<div class="container">

    @include('admin.layouts.adminNav')
        
    <main class="content">
        <div class="admin-dashboard">
            <h2>Панель управления</h2>
            <div class="user-info" style="margin-bottom: 20px;">
                <p><strong>Имя:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Роль:</strong> {{ $user->role }}</p>
            </div>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Всего студентов</h3>
                    <p>{{ $totalStudents }}</p>
                    <span class="trend positive">+5%</span>
                </div>
                <div class="stat-card">
                    <h3>Всего преподавателей</h3>
                    <p>{{ $totalTeachers }}</p>
                    <span class="trend positive">+2%</span>
                </div>
                <div class="stat-card">
                    <h3>Активные группы</h3>
                    <p>{{ $activeGroups }}</p>
                    <span class="trend neutral">0%</span>
                </div>
                <div class="stat-card">
                    <h3>Новые обращения</h3>
                    <p>{{ $newAppeals }}</p>
                    <span class="trend negative">-3%</span>
                </div>
            </div>
            <div class="recent-activity">
                <h3>Последние действия</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <span class="time">10:30</span>
                        <span class="action">Создана новая группа ИТ-101</span>
                    </div>
                    <div class="activity-item">
                        <span class="time">09:15</span>
                        <span class="action">Добавлен новый преподаватель</span>
                    </div>
                    <div class="activity-item">
                        <span class="time">08:45</span>
                        <span class="action">Новое обращение от студента</span>
                    </div>
                    <div class="activity-item">
                        <span class="time">08:30</span>
                        <span class="action">Обновлен учебный план</span>
                    </div>
                </div>
            </div>
            <div class="quick-actions">
                <h3>Быстрые действия</h3>
                <div class="actions-grid">
                    <a href="{{ route('management') }}" class="action-btn">Создать группу</a>
                    <a href="{{ route('teacher') }}" class="action-btn">Добавить преподавателя</a>
                    <a href="{{ route('student') }}" class="action-btn">Добавить студента</a>
                    <a href="{{ route('calendar') }}" class="action-btn">Создать расписание</a>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection