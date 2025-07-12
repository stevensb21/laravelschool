<div id="editCourseModal" class="modal">
    <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
        <span class="close" onclick="closeModal('editCourseModal')">&times;</span>
        <h3>Редактировать курс</h3>
        <form action="{{ route('management.updateCourse') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_course_id">Выберите курс для редактирования:</label>
                <select id="edit_course_id" name="course_id" required onchange="loadCourseData()">
                    <option value="">Выберите курс...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_course_name">Название курса:</label>
                <input type="text" id="edit_course_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit_access_groups">Доступные группы:</label>
                <select id="edit_access_groups" name="access_groups[]" multiple>
                    @foreach($groupsList as $groupName)
                        @if(!empty($groupName))
                            <option value="{{ $groupName }}">{{ $groupName }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_access_teachers">Доступные преподаватели:</label>
                <select id="edit_access_teachers" name="access_teachers[]" multiple>
                    @foreach($teachers as $teacher)
                        @if(!empty($teacher->id))
                            <option value="teacher_{{ $teacher->id }}">{{ $teacher->fio }} (Преподаватель)</option>
                        @endif
                    @endforeach
                    @foreach($admins as $admin)
                        @if(!empty($admin->id))
                            <option value="admin_{{ $admin->id }}">{{ $admin->name }} (Администратор)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editCourseModal')">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Красивый скролл для модального окна -->
<style>
.modal-content {
    scrollbar-width: thin;
    scrollbar-color: #2563eb #f8fafc;
}
.modal-content::-webkit-scrollbar {
    width: 8px;
}
.modal-content::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 4px;
}
.modal-content::-webkit-scrollbar-track {
    background: #f8fafc;
}
</style> 