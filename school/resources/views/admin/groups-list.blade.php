@extends('admin.layouts.head')
@section('head')
<style>
    .groups-container {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px var(--card-shadow);
        margin-bottom: 24px;
        overflow: hidden; /* Предотвращаем переполнение */
    }
    
    .group-item {
        background: var(--bg-secondary);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box; /* Важно для правильного расчета размеров */
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .group-info {
        flex: 1;
        min-width: 200px;
    }
    
    .group-stats {
        display: flex;
        gap: 16px;
        margin-top: 8px;
        font-size: 14px;
        color: var(--text-secondary);
        flex-wrap: wrap;
    }
    
    .group-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }
    
    .btn-small {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-info {
        background: var(--info-color);
        color: white;
    }
    
    .back-link {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }
    
    /* Мобильные стили */
    @media (max-width: 768px) {
        .groups-container {
            padding: 16px;
            margin: 10px;
            border-radius: 8px;
        }
        
        .group-item {
            flex-direction: column;
            align-items: stretch;
            padding: 12px;
            gap: 8px;
        }
        
        .group-info {
            min-width: auto;
        }
        
        .group-stats {
            flex-direction: column;
            gap: 4px;
        }
        
        .group-actions {
            width: 100%;
            justify-content: center;
        }
        
        .btn-small {
            width: 100%;
            text-align: center;
            padding: 10px 12px;
        }
    }
    
    /* Очень маленькие экраны */
    @media (max-width: 480px) {
        .groups-container {
            padding: 12px;
            margin: 8px;
        }
        
        .group-item {
            padding: 10px;
        }
        
        .group-stats {
            font-size: 12px;
        }
    }
</style>
@endsection

@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="groups-container">
            <div class="header-section">
                <h2>Список групп</h2>
                <p>Выберите группу для просмотра студентов</p>
            </div>
            
            <div class="groups-list">
                @if($groups->count() > 0)
                    @foreach($groups as $group)
                        <div class="group-item">
                            <div class="group-info">
                                <h4>{{ $group->name }}</h4>
                                <div class="group-stats">
                                    <span>Всего студентов: {{ $group->all_students_count }}</span>
                                    <span>Основных студентов: {{ $group->primary_students_count }}</span>
                                </div>
                            </div>
                            
                            <div class="group-actions">
                                <a href="{{ route('admin.group.students', $group->id) }}" class="btn-small btn-primary">Просмотр студентов</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>Группы не найдены.</p>
                @endif
            </div>
            
            <div class="back-link">
                <a href="{{ route('management') }}" style="color:var(--link-color); text-decoration:underline; transition:color 0.2s;" onmouseover="this.style.color='var(--link-hover)'" onmouseout="this.style.color='var(--link-color)'">← Назад к управлению</a>
            </div>
        </div>
    </main>
</div> 