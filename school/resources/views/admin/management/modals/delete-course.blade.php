<div id="deleteCourseModal" class="modal" style="top: 50px;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteCourseModal')">&times;</span>
        <h3>Удалить курс</h3>
        <form action="{{ route('management.deleteCourse') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="course_id">Выберите курс для удаления:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">Выберите курс...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="warning">
                <p><strong>Внимание!</strong> Удаление курса приведет к удалению всех связанных данных.</p>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Удалить</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteCourseModal')">Отмена</button>
            </div>
        </form>
    </div>
</div> 