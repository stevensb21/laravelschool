@vite(['resources/js/app.js', 'resources/css/method-modal.css'])


<div id="addMethodModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addMethodModal')">&times;</span>
        <h2>Добавить метод</h2>
        
        @if (session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        
        @if (session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        
        <form method="POST" action="{{ route('method.store') }}" class="add-method-form" id="addMethodForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="course" value="{{ request('course', 'C++') }}">
            
            <div class="form-group">
                <label for="title">Название метода:</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_homework">Названия домашних заданий (каждое с новой строки):</label>
                <textarea id="title_homework" name="title_homework" rows="3" placeholder="Homework 1&#10;Homework 2">{{ old('title_homework') }}</textarea>
                @error('title_homework')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="homework">Ссылки на домашние задания (каждое с новой строки):</label>
                <textarea id="homework" name="homework" rows="3" placeholder="methodfile/homework/file1.pdf&#10;methodfile/homework/file2.pdf">{{ old('homework') }}</textarea>
                <div class="file-upload-section">
                    <label for="homework_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="homework_files" name="homework_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('homework')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_lesson">Названия уроков (каждое с новой строки):</label>
                <textarea id="title_lesson" name="title_lesson" rows="3" placeholder="Lesson 1&#10;Lesson 2">{{ old('title_lesson') }}</textarea>
                @error('title_lesson')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="lesson">Ссылки на уроки (каждое с новой строки):</label>
                <textarea id="lesson" name="lesson" rows="3" placeholder="methodfile/lesson/file1.pdf&#10;methodfile/lesson/file2.pdf">{{ old('lesson') }}</textarea>
                <div class="file-upload-section">
                    <label for="lesson_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="lesson_files" name="lesson_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                   
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('lesson')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_exercise">Названия практических заданий (каждое с новой строки):</label>
                <textarea id="title_exercise" name="title_exercise" rows="3" placeholder="Exercise 1&#10;Exercise 2">{{ old('title_exercise') }}</textarea>
                @error('title_exercise')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="exercise">Ссылки на практические задания (каждое с новой строки):</label>
                <textarea id="exercise" name="exercise" rows="3" placeholder="methodfile/exercise/file1.pdf&#10;methodfile/exercise/file2.pdf">{{ old('exercise') }}</textarea>
                <div class="file-upload-section">
                    <label for="exercise_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="exercise_files" name="exercise_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                 
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('exercise')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_book">Названия книг (каждое с новой строки):</label>
                <textarea id="title_book" name="title_book" rows="3" placeholder="Book 1&#10;Book 2">{{ old('title_book') }}</textarea>
                @error('title_book')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="book">Ссылки на книги (каждое с новой строки):</label>
                <textarea id="book" name="book" rows="3" placeholder="methodfile/book/file1.pdf&#10;methodfile/book/file2.pdf">{{ old('book') }}</textarea>
                <div class="file-upload-section">
                    <label for="book_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="book_files" name="book_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('book')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_video">Названия видео (каждое с новой строки):</label>
                <textarea id="title_video" name="title_video" rows="3" placeholder="Video 1&#10;Video 2">{{ old('title_video') }}</textarea>
                @error('title_video')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="video">Ссылки на видео (каждое с новой строки):</label>
                <textarea id="video" name="video" rows="3" placeholder="https://youtube.com/watch?v=...&#10;https://rutube.ru/...">{{ old('video') }}</textarea>
                @error('video')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_presentation">Названия презентаций (каждое с новой строки):</label>
                <textarea id="title_presentation" name="title_presentation" rows="3" placeholder="Presentation 1&#10;Presentation 2">{{ old('title_presentation') }}</textarea>
                @error('title_presentation')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="presentation">Ссылки на презентации (каждое с новой строки):</label>
                <textarea id="presentation" name="presentation" rows="3" placeholder="methodfile/presentation/file1.pdf&#10;methodfile/presentation/file2.pdf">{{ old('presentation') }}</textarea>
                <div class="file-upload-section">
                    <label for="presentation_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="presentation_files" name="presentation_files[]" multiple accept=".pdf,.ppt,.pptx,.zip,.rar" class="file-input">
                   
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('presentation')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_test">Названия тестов (каждое с новой строки):</label>
                <textarea id="title_test" name="title_test" rows="3" placeholder="Test 1&#10;Test 2">{{ old('title_test') }}</textarea>
                @error('title_test')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="test">Ссылки на тесты (каждое с новой строки):</label>
                <textarea id="test" name="test" rows="3" placeholder="methodfile/test/file1.pdf&#10;methodfile/test/file2.pdf">{{ old('test') }}</textarea>
                <div class="file-upload-section">
                    <label for="test_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="test_files" name="test_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                 
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('test')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="title_article">Названия статей (каждое с новой строки):</label>
                <textarea id="title_article" name="title_article" rows="3" placeholder="Статья 1&#10;Статья 2">{{ old('title_article') }}</textarea>
                @error('title_article')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="article">Ссылки на статьи (каждое с новой строки):</label>
                <textarea id="article" name="article" rows="3" placeholder="methodfile/article/file1.pdf&#10;https://example.com/article">{{ old('article') }}</textarea>
                <div class="file-upload-section">
                    <label for="article_files" class="file-upload-label">Или загрузите файлы:</label>
                    <input type="file" id="article_files" name="article_files[]" multiple accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="file-input">
                 
                    <div class="file-hint">💡 Для выбора нескольких файлов: удерживайте Ctrl и кликайте на файлы, или перетащите несколько файлов</div>
                </div>
                @error('article')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-actions">
                <button type="submit" name="add_method" class="submit-btn">Добавить</button>
                <button type="button" onclick="closeModal('addMethodModal')" class="cancel-btn">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    handleAddMethodForm();
});

function handleAddMethodForm() {
    const form = document.getElementById('addMethodForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Здесь можно добавить валидацию формы
            console.log('Форма метода отправлена');
        });
    }
}
</script>