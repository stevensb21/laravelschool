<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createGroupModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Создать новую группу</h3>
        <form action="{{ route('management.createGroup') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="group_name">Название группы:</label>
                <input type="text" id="group_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="group_teacher">Преподаватель:</label>
                <select id="group_teacher" name="teacher_id" required>
                    <option value="">Выберите преподавателя...</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->fio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="group_courses">Курсы группы:</label>
                <select id="group_courses" name="courses[]" multiple>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
                <small>Удерживайте Ctrl (Cmd на Mac) для выбора нескольких курсов</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Создать</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createGroupModal')">Отмена</button>
            </div>
        </form>
    </div>
</div> 