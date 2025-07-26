@extends('admin.layouts.head')
@section('head')

<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/homework.css'])

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
@include('teacher.nav')
@endif


<div class="container">
        
        <main class="content">
            @if(isset($isAdmin) && $isAdmin)
                <div class="admin-header" >
                    <h2 style="margin: 0; color: #333;">Домашние задания преподавателя: {{ $teacher->fio }}</h2>
                    <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
                </div>
            @endif
            
            <div class="homework-container" >
                <div class="homework-header">
                    <h2>@if(isset($isAdmin) && $isAdmin)Домашние задания преподавателя{{ $teacher->fio }}@elseУправление домашними заданиями@endif</h2>

                </div>
                <div class="homework-filters">
                    <form method="GET" action="{{ route('teacher.homework') }}" id="filterForm">
                        @if(isset($isAdmin) && $isAdmin)
                            <input type="hidden" name="teacher_id" value="{{ $teacher->users_id }}">
                        @endif
                        <div class="select-group">
                        <input type="text" name="search" placeholder="Поиск заданий..." value="{{ request('search') }}">
                        <select name="course">
                            <option value="">Все предметы</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="group">
                            <option value="">Все группы</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status">
                            <option value="">Все статусы</option>
                            <option value="Активно" {{ request('status') == 'Активно' ? 'selected' : '' }}>Активное</option>
                            <option value="Завершено" {{ request('status') == 'Завершено' ? 'selected' : '' }}>Завершено</option>
                            <option value="Просрочено" {{ request('status') == 'Просрочено' ? 'selected' : '' }}>Просрочено</option>
                        </select>

                        <button type="submit" class="filter-btn">Применить фильтры</button>
                        <button type="button" class="clear-btn" onclick="clearFilters()">Очистить</button>
                        </div>
                    </form>
                </div>
                <div class="homework-list">
                    @foreach ($homeworks as $homework)
                        <div class="homework-item">
                            <div class="homework-header">
                                <h3>{{ $homework->name }}</h3>
                                <span class="status active">{{ $homework->status }}</span>
                            </div>
                            <div class="homework-details">
                                <p><strong>Предмет:</strong> {{ $homework->course->name ?? 'Не указан' }}</p>
                                <p><strong>Преподаватель:</strong> {{ $homework->teacher->fio ?? 'Не указан' }}</p>
                                <p><strong>Группа:</strong> {{ $homework->group->name ?? 'Не указана' }}</p>
                                <p><strong>Срок сдачи:</strong> {{ $homework->deadline }}</p>
                                <p><strong>Описание:</strong> {{ $homework->description }}</p>
                                <p><strong>Прогресс:</strong> {{ $homework->homeWorkStudents->whereNotNull('grade')->count() }}/{{ $homework->group->students->count() }} студентов выполнили</p>
                                @php
                                    $totalStudents = $homework->group->students->count();
                                    $needGrade = $homework->homeWorkStudents->whereNull('grade')->count();
                                @endphp
                                <p><strong>Требует оценки:</strong> {{ $needGrade }} / {{ $totalStudents }}</p>
                            </div>
                            <div class="homework-actions">
                               
                                    <!-- <button class="view-results">Просмотр результатов</button>
                                    <button class="export-results">Экспорт</button> --> 
                             
                                    <a href="{{ route('homework.submissions', $homework->id) }}" class="view-submissions">Просмотр работ</a>
                                    <a class="view-submissions" href="{{ asset('storage/' . ltrim($homework->file_path, '/')) }}" target="_blank">Просмотр задания</a>
                                    <button class="extend-btn" onclick="openExtendModal({{ $homework->id }}, '{{ $homework->deadline }}')">Продлить срок</button>
                                    <form method="POST" action="{{ route('homework.destroy', $homework->id) }}" style="display:inline;" onsubmit="return confirm('Удалить это домашнее задание?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">Удалить</button>
                                    </form>

                               
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- <div class="pagination">
                    <button class="prev-page">&lt;</button>
                    <span class="page-number">1</span>
                    <button class="next-page">&gt;</button>
                </div> -->
            </div>
        </main>
    </div>

    <!-- Toast уведомление -->
<div id="toast" class="toast"></div>

<style>
.toast {
    position: fixed;
    top: 24px;
    left: 24px;
    min-width: 220px;
    max-width: 350px;
    background: #22223b;
    color: #fff;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    font-size: 1rem;
    opacity: 0;
    z-index: 9999;
    pointer-events: none;
    transition: opacity 0.3s, transform 0.3s;
    transform: translateY(-30px);
}
.toast.show {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}
</style>

   

   

    <script>
    function openExtendModal(homeworkId, currentDeadline) {
        document.getElementById('homeworkId').value = homeworkId;
        document.getElementById('currentDeadline').value = currentDeadline;
        
        // Устанавливаем минимальную дату как завтра
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        document.getElementById('newDeadline').min = tomorrowStr;
        
        document.getElementById('extendModal').style.display = 'flex';
    }

    function closeExtendModal() {
        document.getElementById('extendModal').style.display = 'none';
    }

    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        const modal = document.getElementById('extendModal');
        if (event.target == modal) {
            closeExtendModal();
        }
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2000);
    }

    // Обработка отправки формы продления срока
    document.getElementById('extendForm').onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const homeworkId = formData.get('homework_id');
        const newDeadline = formData.get('new_deadline');
        
        fetch(`/homework/${homeworkId}/extend-deadline`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeExtendModal();
                showToast(data.message); // Показываем toast
                setTimeout(() => location.reload(), 2000); // Перезагрузка через 2 сек
            } else {
                showToast('Ошибка при продлении срока: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Произошла ошибка при продлении срока');
        });
    };

    // Функции для модального окна удаления
    let homeworkToDelete = null;

    function openDeleteModal(homeworkId, homeworkName) {
        homeworkToDelete = homeworkId;
        document.getElementById('deleteHomeworkName').textContent = homeworkName;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        homeworkToDelete = null;
    }

    function confirmDelete() {
        if (!homeworkToDelete) {
            alert('Ошибка: ID задания не найден');
            return;
        }

        if (confirm('Вы уверены, что хотите удалить это задание? Это действие нельзя отменить!')) {
            fetch(`/homework/${homeworkToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    alert(data.message);
                    location.reload(); // Перезагружаем страницу для обновления данных
                } else {
                    alert('Ошибка при удалении задания: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при удалении задания');
            });
        }
    }

    // Обновляем обработчик клика вне модального окна
    window.onclick = function(event) {
        const extendModal = document.getElementById('extendModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target == extendModal) {
            closeExtendModal();
        }
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }

    // Функция для очистки фильтров
    function clearFilters() {
        @if(isset($isAdmin) && $isAdmin)
            window.location.href = '{{ route("teacher.homework") }}?teacher_id={{ $teacher->users_id }}';
        @else
        window.location.href = '{{ route("teacher.homework") }}';
        @endif
    }
    </script>
@endsection 