<div class="appeal-details">
    <h3>{{ $appeal->title }}</h3>
    
    <div class="appeal-info">
        <div class="info-row">
            <strong>Отправитель:</strong> 
            @if($appeal->sender)
                @if($appeal->sender->student)
                    {{ $appeal->sender->student->fio }}
                @elseif($appeal->sender->teacher)
                    {{ $appeal->sender->teacher->fio }}
                @else
                    {{ $appeal->sender->name }}
                @endif
            @else
                Неизвестно
            @endif
        </div>
        <div class="info-row">
            <strong>Получатель:</strong> 
            @if($appeal->recipient)
                @if($appeal->recipient->student)
                    {{ $appeal->recipient->student->fio }}
                @elseif($appeal->recipient->teacher)
                    {{ $appeal->recipient->teacher->fio }}
                @else
                    {{ $appeal->recipient->name }}
                @endif
            @else
                Неизвестно
            @endif
        </div>
        <div class="info-row">
            <strong>Тип:</strong> 
            <span class="type-badge type-{{ strtolower($appeal->type) }}">
                {{ $appeal->type }}
            </span>
        </div>
        <div class="info-row">
            <strong>Статус:</strong> 
            <span class="status-badge status-{{ strtolower($appeal->status) }}">
                {{ $appeal->status }}
            </span>
        </div>
        <div class="info-row">
            <strong>Дата создания:</strong> {{ $appeal->created_at->format('d.m.Y H:i') }}
        </div>
        @if($appeal->updated_at != $appeal->created_at)
            <div class="info-row">
                <strong>Дата обновления:</strong> {{ $appeal->updated_at->format('d.m.Y H:i') }}
            </div>
        @endif
    </div>
    
    <div class="appeal-content">
        <h4>Описание:</h4>
        <div class="description">
            {{ $appeal->description }}
        </div>
    </div>
    
    @if($appeal->feedback)
        <div class="appeal-feedback">
            <h4>Ответ:</h4>
            <div class="feedback">
                {{ $appeal->feedback }}
            </div>
            @if($appeal->like_feedback)
                <div class="feedback-rating">
                    <strong>Оценка ответа:</strong> 
                    <span class="rating">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $appeal->like_feedback ? 'filled' : '' }}">★</span>
                        @endfor
                        ({{ $appeal->like_feedback }}/5)
                    </span>
                </div>
            @endif
        </div>
    @endif
</div> 