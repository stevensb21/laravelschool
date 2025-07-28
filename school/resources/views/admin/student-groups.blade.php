@extends('admin.layouts.head')
@section('head')
<style>
    .groups-container {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px var(--card-shadow);
        margin-bottom: 24px;
        max-width: 1200px;
        margin: 0 auto;
        overflow: hidden; /* Предотвращаем переполнение */
    }
    
    .header-section {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border-color);
    }
    
    .header-section h2 {
        color: var(--text-primary);
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary-color), var(--info-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .student-info {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .student-info-item {
        background: var(--bg-secondary);
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .student-info-item strong {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .current-groups {
        margin-bottom: 40px;
    }
    
    .current-groups h3 {
        color: var(--text-primary);
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .current-groups h3::before {
        content: "📚";
        font-size: 24px;
    }
    
    .groups-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .group-item {
        background: var(--bg-secondary);
        border-radius: 16px;
        padding: 24px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-sizing: border-box; /* Важно для правильного расчета размеров */
    }
    
    .group-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--border-color);
        transition: all 0.3s ease;
    }
    
    .group-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }
    
    .group-item:hover::before {
        background: var(--primary-color);
    }
    
    .group-item.primary {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05), rgba(var(--primary-color-rgb), 0.02));
    }
    
    .group-item.primary::before {
        background: var(--primary-color);
    }
    
    .group-item.primary .group-status {
        background: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }
    
    .group-info h4 {
        color: var(--text-primary);
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .group-info h4::before {
        content: "👥";
        font-size: 18px;
    }
    
    .group-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }
    
    .group-subjects {
        background: var(--card-bg);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid var(--border-color);
    }
    
    .group-subjects strong {
        color: var(--text-primary);
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
    }
    
    .group-subjects span {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.4;
    }
    
    .group-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .btn-small {
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-small:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-danger {
        background: var(--error-color);
        color: white;
    }
    
    .btn-success {
        background: var(--success-color);
        color: white;
    }
    
    .add-group-form {
        background: linear-gradient(135deg, var(--bg-secondary), var(--card-bg));
        border-radius: 16px;
        padding: 28px;
        margin-top: 32px;
        border: 2px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    
    .add-group-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--info-color));
    }
    
    .add-group-form h3 {
        color: var(--text-primary);
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .add-group-form h3::before {
        content: "➕";
        font-size: 20px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 20px;
        align-items: end;
        margin-bottom: 20px;
    }
    
    /* Мобильные стили */
    @media (max-width: 768px) {
        .groups-container {
            padding: 20px;
            margin: 10px;
            border-radius: 12px;
        }
        
        .header-section h2 {
            font-size: 24px;
        }
        
        .student-info {
            gap: 16px;
            justify-content: center;
        }
        
        .student-info-item {
            padding: 10px 16px;
            font-size: 14px;
        }
        
        .groups-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .group-item {
            padding: 20px;
            border-radius: 12px;
            margin: 0;
            width: 100%;
            box-sizing: border-box;
        }
        
        .group-info h4 {
            font-size: 18px;
        }
        
        .group-actions {
            flex-direction: column;
            gap: 8px;
        }
        
        .btn-small {
            width: 100%;
            justify-content: center;
            padding: 12px 16px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .add-group-form {
            padding: 20px;
            margin-top: 20px;
        }
    }
    
    /* Очень маленькие экраны */
    @media (max-width: 480px) {
        .groups-container {
            padding: 16px;
            margin: 8px;
        }
        
        .group-item {
            padding: 16px;
        }
        
        .group-info h4 {
            font-size: 16px;
        }
        
        .group-status {
            font-size: 11px;
            padding: 5px 10px;
        }
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-control {
        padding: 12px 16px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        background: var(--input-bg);
        color: var(--text-primary);
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: var(--card-bg);
        border-radius: 10px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }
    
    .checkbox-group:hover {
        border-color: var(--primary-color);
    }
    
    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color);
    }
    
    .checkbox-group label {
        color: var(--text-primary);
        font-weight: 500;
        font-size: 14px;
        margin: 0;
        cursor: pointer;
    }
    
    .back-link {
        text-align: center;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--border-color);
    }
    
    .btn-secondary {
        background: var(--btn-secondary);
        color: var(--text-light);
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
        font-size: 16px;
        background: var(--bg-secondary);
        border-radius: 12px;
        border: 2px dashed var(--border-color);
    }
    
    .empty-state::before {
        content: "📚";
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        border: 1px solid;
        font-weight: 500;
    }
    
    .alert-success {
        background: rgba(var(--success-color-rgb), 0.1);
        border-color: var(--success-color);
        color: var(--success-color);
    }
    
    .alert-error {
        background: rgba(var(--error-color-rgb), 0.1);
        border-color: var(--error-color);
        color: var(--error-color);
    }
</style>
@endsection

@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="groups-container">
            <div class="header-section">
                <h2>Управление группами студента</h2>
                <div class="student-info">
                    <div class="student-info-item">
                        <strong>Студент:</strong> {{ $student->fio }}
                    </div>
                    <div class="student-info-item">
                        <strong>Email:</strong> {{ $student->email }}
                    </div>
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="current-groups">
                <h3>Текущие группы</h3>
                @if($student->groups->count() > 0)
                    <div class="groups-grid">
                        @foreach($student->groups as $group)
                            <div class="group-item {{ $group->pivot->is_primary ? 'primary' : '' }}">
                                <div class="group-info">
                                    <h4>{{ $group->name }}</h4>
                                    <div class="group-status">
                                        @if($group->pivot->is_primary)
                                            ⭐ Основная группа
                                        @else
                                            📋 Дополнительная группа
                                        @endif
                                    </div>
                                    <div class="group-subjects">
                                        <strong>Предметы:</strong>
                                        <span>{{ implode(', ', $group->getCourseNames()) ?: 'Предметы не назначены' }}</span>
                                    </div>
                                </div>
                                
                                <div class="group-actions">
                                    @if(!$group->pivot->is_primary)
                                        <form method="POST" action="{{ route('admin.student.set-primary-group', $student->id) }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-primary btn-small">
                                                ⭐ Сделать основной
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.student.remove-from-group', $student->id) }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Удалить студента из группы {{ $group->name }}?')">
                                            🗑️ Удалить из группы
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        Студент не состоит ни в одной группе
                    </div>
                @endif
            </div>
            
            <div class="add-group-form">
                <h3>Добавить в группу</h3>
                <form method="POST" action="{{ route('admin.student.add-to-group', $student->id) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="group_id">Выберите группу:</label>
                            <select name="group_id" id="group_id" required class="form-control">
                                <option value="">Выберите группу</option>
                                @foreach($allGroups as $group)
                                    @if(!$student->groups->contains($group->id))
                                        <option value="{{ $group->id }}">{{ $group->name }} ({{ implode(', ', $group->getCourseNames()) }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_primary" id="is_primary" value="1">
                            <label for="is_primary">Сделать основной группой</label>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-small">
                            ➕ Добавить в группу
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="back-link">
                <a href="{{ route('student') }}" class="btn btn-secondary">
                    ← Назад к списку студентов
                </a>
            </div>
        </div>
    </main>
</div> 