<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createGroupModal')">&times;</span>
        <h3>Создать новую группу</h3>
        <form action="{{ route('management.createGroup') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="group_name">Название группы:</label>
                <input type="text" id="group_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="group_size">Размер группы:</label>
                <input type="number" id="group_size" name="size" min="0" value="0" required>
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