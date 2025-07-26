<div id="editGroupModal" class="modal">
    <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
        <span class="close" onclick="closeModal('editGroupModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
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
                <label for="edit_group_teacher">Преподаватель:</label>
                <select id="edit_group_teacher" name="teacher_id" required>
                    <option value="">Выберите преподавателя...</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ (isset($group) && $group->teacher_id == $teacher->id) ? 'selected' : '' }}>{{ $teacher->fio }}</option>
                    @endforeach
                </select>
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
    const groupName = document.getElementById('edit_group_id') ? document.getElementById('edit_group_id').value : null;
    if (!groupName) return;
    fetch(`/management/get-group-data?group_name=${encodeURIComponent(groupName)}`)
        .then(response => response.json())
        .then(data => {
            const nameInput = document.getElementById('edit_group_name');
            if (nameInput) {
                nameInput.value = data.name;
            } else {
                console.warn('Не найден элемент edit_group_name');
            }
            // Преподаватель
            const teacherSelect = document.getElementById('edit_group_teacher');
            if (teacherSelect) {
                for (let i = 0; i < teacherSelect.options.length; i++) {
                    teacherSelect.options[i].selected = (teacherSelect.options[i].value === String(data.teacher_id));
                }
            } else {
                console.warn('Не найден элемент edit_group_teacher');
            }
            // Курсы
            const select = document.getElementById('edit_group_courses');
            if (select) {
            for (let i = 0; i < select.options.length; i++) {
                    select.options[i].selected = data.courses.map(String).includes(select.options[i].value);
            }
            } else {
                console.warn('Не найден элемент edit_group_courses');
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

<style>
  .modal-content {
      scrollbar-width: thin;
      scrollbar-color: #EDEDED #FBFBFB;
  }
  .modal-content::-webkit-scrollbar {
      width: 8px;
  }
  .modal-content::-webkit-scrollbar-thumb {
      background: #EDEDED;
      border-radius: 4px;
  }
  .modal-content::-webkit-scrollbar-thumb:hover {
      background: #383E33;
  }
  .modal-content::-webkit-scrollbar-track {
      background: #FBFBFB;
  }
  </style>