<div id="editGroupModal" class="modal">
    <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
        <span class="close" onclick="closeModal('editGroupModal')">&times;</span>
        <h3>Редактировать группу</h3>
        <form action="{{ route('management.updateGroup') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_group_id">Выберите группу для редактирования:</label>
                <select id="edit_group_id" name="group_id" required onchange="loadGroupData()">
                    <option value="">Выберите группу...</option>
                    @foreach($groupsList as $groupName)
                        <option value="{{ $groupName }}">{{ $groupName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_group_name">Название группы:</label>
                <input type="text" id="edit_group_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit_group_size">Размер группы:</label>
                <input type="number" id="edit_group_size" name="size" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_group_courses">Курсы группы:</label>
                <div id="edit_group_courses_container">
                    <select id="edit_group_courses" name="courses" multiple>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <small>Выберите один или несколько курсов (доступен поиск)</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editGroupModal')">Отмена</button>
            </div>
        </form>
    </div>
</div>
<!-- Choices.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script>
window.loadGroupData = function() {
    const groupName = document.getElementById('edit_group_id').value;
    if (!groupName) return;
    fetch(`/management/get-group-data?group_name=${encodeURIComponent(groupName)}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_group_name').value = data.name;
            document.getElementById('edit_group_size').value = data.size;
            let selectedIds = Array.isArray(data.courses_json) ? data.courses_json.map(String) : [];
            const container = document.getElementById('edit_group_courses_container');
            const oldSelect = document.getElementById('edit_group_courses');
            if (oldSelect) {
                if (oldSelect.choicesInstance) {
                    oldSelect.choicesInstance.destroy();
                }
                container.removeChild(oldSelect);
            }
            // Создаем новый select
            const select = document.createElement('select');
            select.id = 'edit_group_courses';
            select.name = 'courses[]';
            select.multiple = true;
            select.disabled = false;
            (window.allCourses || []).forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.text = course.name;
                select.appendChild(option);
            });
            container.appendChild(select);
            // Проверка наличия select
            if (!select) {
                console.error('Select не создан!');
                return;
            }
            // Инициализация Choices.js
            let choices;
            try {
                choices = new Choices(select, {
                    removeItemButton: true,
                    searchResultLimit: 10,
                    searchPlaceholderValue: 'Поиск курса...'
                });
            } catch (e) {
                console.error('Ошибка инициализации Choices.js:', e);
                return;
            }
            // Проверка наличия Choices
            if (!choices) {
                console.error('Choices.js не инициализирован!');
                return;
            }
            // Устанавливаем выбранные значения через Choices.js
            try {
                choices.setChoiceByValue(selectedIds);
            } catch (e) {
                console.error('Ошибка setChoiceByValue:', e);
            }
            select.choicesInstance = choices;
            // Явно убираем disabled и класс is-disabled у Choices
            select.removeAttribute('disabled');
            const choicesEl = select.closest('.choices') || select.parentElement.querySelector('.choices');
            if (choicesEl) {
                choicesEl.classList.remove('is-disabled');
            }
            // Логируем состояние
            console.log('select.disabled:', select.disabled);
            console.log('choicesEl:', choicesEl);
            if (choicesEl) console.log('choicesEl.classList:', choicesEl.classList);
            // Логируем все опции select
            let opts = [];
            for (let i = 0; i < select.options.length; i++) {
                opts.push(`value=${select.options[i].value}, selected=${select.options[i].selected}, text=${select.options[i].text}`);
            }
            // Логируем состояние Choices
            if (choices) {
                console.log('Choices.getValue:', choices.getValue());
            }
        });
}
if (window.Choices) {
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('edit_group_courses');
        if (select && !select.choicesInstance) {
            select.choicesInstance = new Choices(select, {
                removeItemButton: true,
                searchResultLimit: 10,
                searchPlaceholderValue: 'Поиск курса...'
            });
        }
    });
}
</script> 