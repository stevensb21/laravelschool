@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/teachers.css'])
@vite(['resources/js/app.js'])


@include('admin.layouts.adminNav')

<div class="container">
        
        <main class="content">
            <div class="teachers-container">
                <div class="teachers-header">
                    <h2>Управление преподавателями</h2>
                    <button class="add-teacher-btn" onclick="openModal('addTeacherModal')">Добавить преподавателя</button>
                </div>
                <div class="teachers-filters">
                    <form method="GET" action="{{ route('teacher') }}" class="filters-form">
                        <div class="select-group">
                            <input type="text" name="fio" placeholder="Поиск преподавателя..." value="{{ request('fio') }}">
                                <select name="subject">
                                    <option value="">Все предметы</option>
                                        @foreach ($allSubjects as $subject): 
                                            <option value="{{ $subject }}" 
                                            {{ request('subject') == $subject ? 'selected' : '' }}>
                                                {{$subject}}
                                            </option>
                                        @endforeach
                                </select>  
                        </div>
                        <div class="filters-actions">
                            <button type="submit">Применить фильтры</button>
                            @if(request('fio') || request('subject'))
                                <a href="{{ route('teacher') }}" class="reset-filters">Сбросить фильтры</a>
                            @endif  
                        </div>
                    </form>
                </div>
                <div class="teachers-grid">
                    <?php if (empty($teachers)): ?>
                        <div class="no-results">
                            <p>Преподаватели не найдены</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($teachers as $teacher): ?>
                        <div class="teacher-card">
                            <div class="teacher-header">
                            <img src="{{ asset('images/man.png') }}" alt="{{ htmlspecialchars($teacher['fio']) }}">
                                <div class="teacher-status active">Активный</div>
                            </div>
                            <div class="teacher-info">
                                    <h3><?= htmlspecialchars($teacher['fio']) ?></h3>
                                    <p class="subject">
                                        <?php 
                                        $subjects = is_array($teacher['subjects']) ? $teacher['subjects'] : explode(',', trim($teacher['subjects'], '{}'));
                                       echo htmlspecialchars(implode(', ', $subjects));
                                        ?>
                                    </p>
                                    <p class="rating">Рейтинг: {{ $teacher->average_rating !== null ? number_format($teacher->average_rating, 1) : '0.0' }}</p>
                                    <div class="teacher-stats">
                                        <div class="stat">
                                            <div class="stat-value"><?= number_format($teacher['average_attendance'], 1) ?>%</div>
                                            <div class="stat-label">Посещаемость</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-value"><?= number_format($teacher['average_exam_score'], 1) ?></div>
                                            <div class="stat-label">Ср. балл экзаменов</div>
                                        </div>
                                    </div>
                                    <div class="teacher-education">
                                        <?php 
                                        $education = is_array($teacher['education']) ? $teacher['education'] : explode(',', trim($teacher['education'], '{}'));
                                        foreach ($education as $edu): ?>
                                            <span class="education-tag"><?= htmlspecialchars($edu) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                            </div>
                            <div class="teacher-actions">
                                    <button class="edit-btn" onclick="openEditModal(<?= htmlspecialchars(json_encode([
                                        'users_id' => $teacher['users_id'],
                                        'name' => $teacher->user->name,
                                        'fio' => $teacher['fio'],
                                        'job_title' => $teacher['job_title'],
                                        'email' => $teacher['email'],
                                        'subjects' => implode("\n", is_array($teacher['subjects']) ? $teacher['subjects'] : explode(',', trim($teacher['subjects'], '{}'))),
                                        'education' => implode("\n", is_array($teacher['education']) ? $teacher['education'] : explode(',', trim($teacher['education'], '{}'))) ,
                                        'achievements' => implode("\n", is_array($teacher['achievements']) ? $teacher['achievements'] : explode(',', trim($teacher['achievements'], '{}')))
                                    ])) ?>)">Редактировать</button>
                                    <button class="view-profile" onclick="window.location.href='{{ route('admin.teacher.profile', $teacher['users_id']) }}'">Профиль</button>
                                    <button class="schedule-btn" onclick="window.location.href='{{ route('calendar', ['teacher' => $teacher['fio']]) }}'">Расписание</button>
                                    <button class="delete-btn" onclick="openDeleteModal(<?= htmlspecialchars(json_encode([
                                        'users_id' => $teacher['users_id'],
                                        'fio' => $teacher['fio']
                                    ])) ?>)">Удалить</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>


    @include('admin.layouts.addTeacher')
    @include('admin.layouts.editTeacher')
    @include('admin.layouts.deleteTeacher')



@endsection