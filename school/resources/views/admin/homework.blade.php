@extends('admin.layouts.head')
@section('head')

<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/homework.css'])
@include('admin.layouts.adminNav')


<div class="container">
        
        <main class="content">
            <div class="homework-container">
                <div class="homework-header">
                    <h2>Управление домашними заданиями</h2>

                </div>
                <div class="homework-filters">
                    <form method="GET" action="{{ route('homework') }}" id="filterForm">
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
                        </div>
                        <div class="select-group">
                            <button type="submit" class="filter-btn">Применить фильтры</button>
                            <button type="button" class="clear-btn" onclick="clearFilters()">Очистить</button>
                        </div>
                    </form>
                </div>
                <div class="homework-list">
                    @foreach ($homeworks as $homework)
                        <div class="homework-item">
                            <div class="homework-header">
                                <h3>{{ $homework->course->name }}</h3>
                                <span class="status active">{{ $homework->status }}</span>
                            </div>
                            <div class="homework-details">
                                <p><strong>Предмет:</strong> {{ $homework->course->name ?? 'Не указан' }}</p>
                                <p><strong>Преподаватель:</strong> {{ $homework->teacher->fio ?? 'Не указан' }}</p>
                                <p><strong>Группа:</strong> {{ $homework->group->name ?? 'Не указана' }}</p>
                                <p><strong>Срок сдачи:</strong> {{ $homework->deadline }}</p>
                                <p><strong>Описание:</strong> {{ $homework->description }}</p>
                                <p><strong>Прогресс:</strong> {{ $homework->homeWorkStudents->whereNotNull('grade')->count() }}/{{ $homework->group->students->count() }} студентов выполнили</p>
                            </div>
                            <div class="homework-actions">
                               
                                    <!-- <button class="view-results">Просмотр результатов</button>
                                    <button class="export-results">Экспорт</button> --> 
                             
                                    <a href="{{ route('homework.submissions', $homework->id) }}" class="view-submissions">Просмотр работ</a>
                                    <a class="view-submissions" href="{{ $homework->method->homework[0] ?? '#' }}" target="_blank">Просмотр задания</a>
                                    <button class="extend-deadline" onclick="openExtendModal({{ $homework->id }}, '{{ $homework->deadline }}')">Продлить срок</button>
                                    <button class="close-homework" onclick="openDeleteModal({{ $homework->id }}, '{{ $homework->course->name ?? 'Не указан' }}')">Закрыть задание</button>
                               
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

    <!-- Модальное окно для продления срока -->
    <div id="extendModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeExtendModal()">&times;</span>
            <h3>Продлить срок сдачи</h3>
            <form id="extendForm">
                <input type="hidden" id="homeworkId" name="homework_id">
                <div class="form-group">
                    <label for="currentDeadline">Текущий срок сдачи:</label>
                    <input type="text" id="currentDeadline" readonly>
                </div>
                <div class="form-group">
                    <label for="newDeadline">Новый срок сдачи:</label>
                    <input type="date" id="newDeadline" name="new_deadline" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-btn">Продлить</button>
                    <button type="button" class="cancel-btn" onclick="closeExtendModal()">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для удаления задания -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h3>Подтверждение удаления</h3>
            <div class="delete-warning">
                <p><strong>Внимание!</strong> Вы собираетесь удалить домашнее задание:</p>
                <p id="deleteHomeworkName" style="font-weight: bold; color: #E53E3E;"></p>
                <p>Это действие также удалит:</p>
                <ul>
                    <li>Все работы студентов по этому заданию</li>
                    <li>Все оценки и комментарии</li>
                    <li>Все загруженные файлы</li>
                </ul>
                <p><strong>Это действие нельзя отменить!</strong></p>
            </div>
            <div class="form-actions">
                <button type="button" class="delete-btn" onclick="confirmDelete()">Удалить задание</button>
                <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Отмена</button>
            </div>
        </div>
    </div>

    <script>
    function openExtendModal(homeworkId, currentDeadline) {
        document.getElementById('homeworkId').value = homeworkId;
        document.getElementById('currentDeadline').value = currentDeadline;
        
        // Устанавливаем минимальную дату как завтра
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        document.getElementById('newDeadline').min = tomorrowStr;
        
        document.getElementById('extendModal').style.display = 'block';
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
                alert(data.message);
                location.reload(); // Перезагружаем страницу для обновления данных
            } else {
                alert('Ошибка при продлении срока: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при продлении срока');
        });
    };

    // Функции для модального окна удаления
    let homeworkToDelete = null;

    function openDeleteModal(homeworkId, homeworkName) {
        homeworkToDelete = homeworkId;
        document.getElementById('deleteHomeworkName').textContent = homeworkName;
        document.getElementById('deleteModal').style.display = 'block';
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
        window.location.href = '{{ route("homework") }}';
    }
    </script>
@endsection