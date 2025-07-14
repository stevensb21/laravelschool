<div id="createCourseModal" class="modal" style="top: 50px;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createCourseModal')">&times;</span>
        <h3>Создать новый курс</h3>
        <form action="{{ route('management.createCourse') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="course_name">Название курса:</label>
                <input type="text" id="course_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="access_groups">Доступные группы:</label>
                <select id="access_groups" name="access_groups[]" multiple>
                    @foreach($groupsList as $groupName)
                        @if(!empty($groupName))
                            <option value="{{ $groupName }}">{{ $groupName }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="access_teachers">Доступные преподаватели:</label>
                <select id="access_teachers" name="access_teachers[]" multiple>
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
                <button type="submit" class="btn btn-primary">Создать</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createCourseModal')">Отмена</button>
            </div>
        </form>
    </div>
</div> 