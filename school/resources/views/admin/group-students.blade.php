@extends('admin.layouts.head')
@section('head')
<style>
    .students-container {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px var(--card-shadow);
        margin-bottom: 24px;
        overflow: hidden; /* Предотвращаем переполнение */
    }
    
    .student-item {
        background: var(--bg-secondary);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid var(--border-color);
        box-sizing: border-box; /* Важно для правильного расчета размеров */
    }
    
    .student-item.primary {
        border-color: var(--primary-color);
        background: rgba(var(--primary-color-rgb), 0.05);
    }
    
    .student-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    
    .btn-small {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .add-student-form {
        background: var(--bg-secondary);
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid var(--border-color);
    }
    
    .form-row {
        display: flex;
        gap: 16px;
        align-items: end;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    
    .form-group {
        flex: 1;
        min-width: 200px;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .stats {
        display: flex;
        gap: 24px;
        margin-bottom: 20px;
        padding: 16px;
        background: var(--bg-secondary);
        border-radius: 8px;
        border: 1px solid var(--border-color);
        flex-wrap: wrap;
    }
    
    .stat-item {
        text-align: center;
        flex: 1;
        min-width: 120px;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .stat-label {
        font-size: 14px;
        color: var(--text-secondary);
    }
    
    /* Мобильные стили */
    @media (max-width: 768px) {
        .students-container {
            padding: 16px;
            margin: 10px;
            border-radius: 8px;
        }
        
        .student-item {
            padding: 12px;
            margin-bottom: 8px;
        }
        
        .student-actions {
            flex-direction: column;
            gap: 6px;
        }
        
        .btn-small {
            width: 100%;
            justify-content: center;
            padding: 10px 12px;
        }
        
        .form-row {
            flex-direction: column;
            gap: 12px;
        }
        
        .form-group {
            min-width: auto;
            width: 100%;
        }
        
        .stats {
            flex-direction: column;
            gap: 16px;
            padding: 12px;
        }
        
        .stat-item {
            min-width: auto;
        }
        
        .add-student-form {
            padding: 16px;
            margin-top: 16px;
        }
    }
    
    /* Очень маленькие экраны */
    @media (max-width: 480px) {
        .students-container {
            padding: 12px;
            margin: 8px;
        }
        
        .student-item {
            padding: 10px;
        }
        
        .stat-number {
            font-size: 20px;
        }
        
        .stat-label {
            font-size: 12px;
        }
    }
</style>
@endsection

@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="students-container">
            <div class="header-section">
                <h2>Студенты группы</h2>
                <p><strong>Группа:</strong> {{ $group->name }}</p>
                <p><strong>Предметы:</strong> {{ implode(', ', $group->getCourseNames()) }}</p>
            </div>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $group->getStudentsCount() }}</div>
                    <div class="stat-label">Всего студентов</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $group->getPrimaryStudentsCount() }}</div>
                    <div class="stat-label">Основных студентов</div>
                </div>
            </div>
            
            <div class="current-students">
                <h3>Студенты в группе</h3>
                @if($group->allStudents->count() > 0)
                    @foreach($group->allStudents as $student)
                        <div class="student-item {{ $student->pivot->is_primary ? 'primary' : '' }}">
                            <div class="student-info">
                                <h4>{{ $student->fio }}</h4>
                                <p><strong>Email:</strong> {{ $student->email }}</p>
                                <p><strong>Статус:</strong> 
                                    @if($student->pivot->is_primary)
                                        <span style="color: var(--primary-color);">Основная группа</span>
                                    @else
                                        <span>Дополнительная группа</span>
                                    @endif
                                </p>
                                <p><strong>Телефон:</strong> {{ $student->numberphone }}</p>
                            </div>
                            
                            <div class="student-actions">
                                @if(!$student->pivot->is_primary)
                                    <form method="POST" action="{{ route('admin.student.set-primary-group', $student->id) }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                                        <button type="submit" class="btn btn-primary btn-small">Сделать основной</button>
                                    </form>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.group.remove-student', $group->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Удалить студента {{ $student->fio }} из группы {{ $group->name }}?')">Удалить из группы</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>В группе нет студентов.</p>
                @endif
            </div>
            
            <div class="add-student-form">
                <h3>Добавить студента</h3>
                <form method="POST" action="{{ route('admin.group.add-student', $group->id) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="student_id">Выберите студента:</label>
                            <select name="student_id" id="student_id" required class="form-control">
                                <option value="">Выберите студента</option>
                                @foreach(\App\Models\Student::with('user')->get() as $student)
                                    @if($student->user && !$group->allStudents->contains($student->id))
                                        <option value="{{ $student->id }}">{{ $student->fio }} ({{ $student->email }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_primary" id="is_primary" value="1">
                            <label for="is_primary">Сделать основной группой</label>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Добавить студента</button>
                    </div>
                </form>
            </div>
            
            <div class="back-link">
                <a href="{{ route('management') }}" class="btn btn-secondary">← Назад к управлению</a>
            </div>
        </div>
    </main>
</div> 