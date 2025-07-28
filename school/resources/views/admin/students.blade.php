@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
@vite(['resources/js/app.js'])

<style>
/* Стили для горизонтальной прокрутки таблиц */
.table-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
    max-width: none !important;
    width: auto !important;
    min-width: 0 !important;
}

/* Убираем ограничения ширины у родительского контейнера */
.students-container {
    max-width: none !important;
    overflow: visible !important;
    width: auto !important;
}

/* Убираем ограничения у students-table */
.students-table {
    max-width: none !important;
    width: auto !important;
    overflow: visible !important;
}

.table-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.table-scroll-container::-webkit-scrollbar-track {
    background: transparent;
}

.table-scroll-container::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

.table-scroll-container::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}

/* Выравнивание ячеек */
.students-container .table-scroll-container .students-table table th,
.students-container .table-scroll-container .students-table table td {
    text-align: left !important;
}
</style>

@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="students-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;">
            <div class="students-header">
                <h2>Управление студентами</h2>
                
                <button class="add-student-btn" onclick="openModal('addStudentModal')">Добавить студента</button>
            </div>
            <div class="students-filters">
                <form method="GET" action="{{ route('student') }}" class="filters-form">
                    <input type="text" name="fio" placeholder="Поиск студента..."  value="{{ request('fio') }}">
                    <select name="group">
                        <option value="">Все группы</option>
                        @foreach ($allGroups as $group)
                            <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                {{$group}}
                            </option>
                        @endforeach
                    </select>
                    <div class="filters-actions">
                        <button type="submit">Применить фильтры</button>
                        @if(request('fio') || request('group'))
                            <a href="{{ route('student') }}" class="reset-filters">Сбросить фильтры</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-scroll-container">
                <div class="students-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ФИО</th>
                                <th>Группа</th>
                                <th>Средний балл</th>
                                <th>Посещаемость</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student->fio; ?></td>
                                <td>
                                    <?php 
                                    // Получаем все группы студента
                                    $studentGroups = $student->groups;
                                    if ($studentGroups && $studentGroups->count() > 0) {
                                        $groupNames = [];
                                        foreach ($studentGroups as $group) {
                                            $groupName = $group->name;
                                            if ($group->pivot->is_primary) {
                                                $groupName .= ' (осн.)';
                                            }
                                            $groupNames[] = $groupName;
                                        }
                                        echo implode(', ', $groupNames);
                                    } else {
                                        echo $student->group_name ?? 'Не указана';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $student->average_performance; ?></td>
                                <td><?php echo $student->average_attendance; ?></td>
                                <td><span class="status active">Активный</span></td>
                                <td>
                                <button class="edit-btn" onclick="openEditStudnetModal(<?= htmlspecialchars(json_encode([
                                    'users_id' => $student->users_id,
                                    'username' => $student->user ? $student->user->name : 'Неизвестно', 
                                    'fio' => $student->fio,
                                    'email' => $student->email,
                                    'numberphone' => $student->numberphone,
                                    'femaleparent' => $student->femaleparent,
                                    'numberparent' => $student->numberparent,
                                    'group_name' => $student->group_name,
                                    'datebirthday' => $student->datebirthday,
                                    'achievements' => implode("\n", is_array($student->achievements) ? $student->achievements : explode(',', trim($student->achievements, '{}')))
                                ])) ?>)">Редактировать</button>
                                <button class="view-profile" onclick="window.location.href='{{ route('admin.student.view', $student->id) }}'">Профиль</button>
                                <button class="groups-btn" onclick="window.location.href='{{ route('admin.student.groups', $student->id) }}'" style="background: var(--info-color); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin: 2px;">Группы</button>
                                <button class="schedule-btn" onclick="window.location.href='{{ route('calendar', ['group' => $student['group_name']]) }}'">Расписание</button>
                                <button class="delete-btn" onclick="openDeleteStudentModal(<?= htmlspecialchars(json_encode([
                                    'users_id' => $student->users_id,
                                    'fio' => $student->fio
                                ])) ?>)">Удалить</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination">
                <button class="prev-page">&lt;</button>
                <span class="page-number">1</span>
                <button class="next-page">&gt;</button>
            </div>
        </div>
    </main>
</div>

    @include('admin.layouts.addStudent')
    @include('admin.layouts.editStudent')
    @include('admin.layouts.deleteStudent')

<script>
// Обработчик колесика мыши для таблиц
document.addEventListener('wheel', (event) => {
    // Проверяем, находится ли курсор над таблицей или её контейнером
    const target = event.target;
    const tableScrollContainer = target.closest('.table-scroll-container');
    
    if (tableScrollContainer) {
        // Предотвращаем вертикальную прокрутку страницы
        event.preventDefault();
        
        // Определяем направление прокрутки
        const scrollAmount = 300;
        if (event.deltaY > 0) {
            tableScrollContainer.scrollLeft += scrollAmount;
        } else {
            tableScrollContainer.scrollLeft -= scrollAmount;
        }
    }
}, { passive: false });
</script>
