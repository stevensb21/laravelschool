@extends('admin.layouts.head')
@section('head')

<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/appeals.css'])

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
.appeals-container {
    max-width: none !important;
    overflow: visible !important;
    width: auto !important;
}

/* Убираем ограничения у appeals-table */
.appeals-table {
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
.appeals-container .table-scroll-container .appeals-table table th,
.appeals-container .table-scroll-container .appeals-table table td {
    text-align: left !important;
}
</style>
@include('admin.layouts.adminNav')

<!-- Toast уведомление -->
<div id="toast"></div>

<div class="container">
    <main class="content">
        <div class="appeals-container">
            <div class="appeals-header">
                <h2>Управление обращениями</h2>
                <button class="add-appeal-btn" onclick="openCreateModal()">Создать обращение</button>
            </div>

            <div class="appeals-filters">
                <form method="GET" action="{{ route('appeals') }}" id="filterForm">
                    <input type="text" name="search" placeholder="Поиск обращений..." value="{{ request('search') }}">
                    <select name="type">
                        <option value="">Все типы</option>
                        <option value="Вопрос" {{ request('type') == 'Вопрос' ? 'selected' : '' }}>Вопрос</option>
                        <option value="Жалоба" {{ request('type') == 'Жалоба' ? 'selected' : '' }}>Жалоба</option>
                        <option value="Предложение" {{ request('type') == 'Предложение' ? 'selected' : '' }}>Предложение</option>
                        <option value="Другое" {{ request('type') == 'Другое' ? 'selected' : '' }}>Другое</option>
                    </select>
                    <select name="status">
                        <option value="">Все статусы</option>
                        <option value="Активно" {{ request('status') == 'Активно' ? 'selected' : '' }}>Активно</option>
                        <option value="Завершено" {{ request('status') == 'Завершено' ? 'selected' : '' }}>Завершено</option>
                    </select>
                    <button type="submit" class="filter-btn">Применить фильтры</button>
                    <button type="button" class="clear-btn" onclick="clearFilters()">Очистить</button>
                </form>
            </div>

            <div class="table-scroll-container">
                <div class="appeals-table">
                    <table>
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Заголовок</th>
                                <th>Отправитель</th>
                                <th>Получатель</th>
                                <th>Тип</th>
                                <th>Статус</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appeals as $index => $appeal)
                                <tr class="{{ $appeal->status === 'Завершено' ? 'completed' : 'active' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="appeal-title" onclick="openViewModal({{ $appeal->id }})">
                                            {{ $appeal->title }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($appeal->sender)
                                            @if($appeal->sender->student)
                                                {{ $appeal->sender->student->fio }}
                                            @elseif($appeal->sender->teacher)
                                                {{ $appeal->sender->teacher->fio }}
                                            @else
                                                {{ $appeal->sender->name }}
                                            @endif
                                        @else
                                            Неизвестно
                                        @endif
                                    </td>
                                    <td>
                                        @if($appeal->recipient)
                                            @if($appeal->recipient->student)
                                                {{ $appeal->recipient->student->fio }}
                                            @elseif($appeal->recipient->teacher)
                                                {{ $appeal->recipient->teacher->fio }}
                                            @else
                                                {{ $appeal->recipient->name }}
                                            @endif
                                        @else
                                            Неизвестно
                                        @endif
                                    </td>
                                    <td>
                                        <span class="type-badge type-{{ strtolower($appeal->type) }}">
                                            {{ $appeal->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($appeal->status) }}">
                                            {{ $appeal->status }}
                                        </span>
                                    </td>
                                    <td>{{ $appeal->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <div class="appeal-actions">
                                            <button class="view-btn" onclick="openViewModal({{ $appeal->id }})">Просмотр</button>
                                            @if($appeal->status === 'Активно' && $appeal->recipient_id === auth()->id())
                                                <button class="reply-btn" onclick="openReplyModal({{ $appeal->id }})">Ответить</button>
                                            @endif
                                            @if($appeal->feedback && is_null($appeal->like_feedback) && $appeal->sender_id === auth()->id())
                                                <button class="reply-btn" onclick="openRateModal({{ $appeal->id }})">Оценить ответ</button>
                                            @endif
                                            @if($appeal->sender_id === auth()->id() || auth()->user()->role === 'admin')
                                                <button class="delete-btn" onclick="deleteAppeal({{ $appeal->id }})">Удалить</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Модальное окно создания обращения -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCreateModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Создать обращение</h3>
        <form id="createForm">
            <div class="form-group">
                <label for="title">Заголовок:</label>
                <input type="text" id="title" name="title" required maxlength="255">
            </div>
            <div class="form-group">
                <label for="recipient">Получатель:</label>
                <select id="recipient" name="recipient_id" required>
                    <option value="">Выберите получателя</option>
                    @foreach($users as $user)
                        @if($user->id !== auth()->id())
                            <option value="{{ $user->id }}">
                                @if($user->student)
                                    {{ $user->student->fio }} (Студент)
                                @elseif($user->teacher)
                                    {{ $user->teacher->fio }} (Преподаватель)
                                @else
                                    {{ $user->name }} ({{ $user->role }})
                                @endif
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="type">Тип обращения:</label>
                <select id="type" name="type" required>
                    <option value="">Выберите тип</option>
                    <option value="Вопрос">Вопрос</option>
                    <option value="Жалоба">Жалоба</option>
                    <option value="Предложение">Предложение</option>
                    <option value="Другое">Другое</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea id="description" name="description" rows="4" required maxlength="1000" placeholder="Опишите ваше обращение..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Создать</button>
                <button type="button" class="cancel-btn" onclick="closeCreateModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно просмотра обращения -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeViewModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <div id="viewContent">
            <!-- Содержимое будет загружено динамически -->
        </div>
    </div>
</div>

<!-- Модальное окно ответа -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReplyModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Ответить на обращение</h3>
        <form id="replyForm">
            <input type="hidden" id="replyAppealId" name="appeal_id">
            <div class="form-group">
                <label for="feedback">Ответ:</label>
                <textarea id="feedback" name="feedback" rows="4" required maxlength="1000" placeholder="Введите ваш ответ..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Отправить ответ</button>
                <button type="button" class="cancel-btn" onclick="closeReplyModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно оценки ответа -->
<div id="rateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRateModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Оценить ответ</h3>
        <form id="rateForm">
            <input type="hidden" id="rateAppealId" name="appeal_id">
            <div class="form-group">
                <label for="like_feedback">Оценка ответа (1-5):</label>
                <select id="like_feedback" name="like_feedback" required>
                    <option value="">Выберите оценку</option>
                    <option value="1">1 - Плохо</option>
                    <option value="2">2 - Удовлетворительно</option>
                    <option value="3">3 - Хорошо</option>
                    <option value="4">4 - Очень хорошо</option>
                    <option value="5">5 - Отлично</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Оценить</button>
                <button type="button" class="cancel-btn" onclick="closeRateModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
// Функции для модальных окон
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}

function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
    document.getElementById('createForm').reset();
}

function openViewModal(appealId) {
    fetch(`/appeals/${appealId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewContent').innerHTML = data.html;
                document.getElementById('viewModal').style.display = 'flex';
            } else {
                alert('Ошибка при загрузке обращения');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при загрузке обращения');
        });
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function openReplyModal(appealId) {
    document.getElementById('replyAppealId').value = appealId;
    document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
    document.getElementById('replyForm').reset();
}

function openRateModal(appealId) {
    document.getElementById('rateAppealId').value = appealId;
    document.getElementById('rateModal').style.display = 'flex';
}

function closeRateModal() {
    document.getElementById('rateModal').style.display = 'none';
    document.getElementById('rateForm').reset();
}

function clearFilters() {
    window.location.href = '{{ route("appeals") }}';
}

// Toast уведомления
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast-show toast-${type}`;
    clearTimeout(window.toastTimeout);
    window.toastTimeout = setTimeout(() => {
        toast.className = '';
    }, 3000);
}

// Обработка форм
document.getElementById('createForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("appeals.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateModal();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Ошибка при создании обращения: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Произошла ошибка при создании обращения', 'error');
    });
};

document.getElementById('replyForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const appealId = formData.get('appeal_id');
    formData.append('_method', 'PUT');
    
    fetch(`/appeals/${appealId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReplyModal();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Ошибка при отправке ответа: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Произошла ошибка при отправке ответа', 'error');
    });
};

document.getElementById('rateForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const appealId = formData.get('appeal_id');
    formData.append('_method', 'PUT');
    fetch(`/appeals/${appealId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRateModal();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Ошибка при оценке: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Произошла ошибка при оценке', 'error');
    });
};

function deleteAppeal(appealId) {
    if (confirm('Вы уверены, что хотите удалить это обращение?')) {
        fetch(`/appeals/${appealId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Ошибка при удалении обращения: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Произошла ошибка при удалении обращения', 'error');
        });
    }
}

// Закрытие модальных окон при клике вне их
window.onclick = function(event) {
    const modals = ['createModal', 'viewModal', 'replyModal', 'rateModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
}

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

@endsection 