@extends('admin.layouts.head')
@section('head')
@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="admin-dashboard">
            <div class="dashboard-header">
                <h2> {{ $teacher->fio }}</h2>
                <a href="{{ route('teacher') }}" class="action-btn">Назад к списку преподавателей</a>
            </div>
            
            <!-- Информация о преподавателе -->
            <div class="profile-section" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;">
                <div class="teacher-avatar">
                    <img src="{{ asset('images/man.jpg') }}" alt="{{ $teacher->fio }}">
                </div>
                <div class="teacher-details">
                    <h3>{{ $teacher->fio }}</h3>
                    <p><strong>Должность:</strong> {{ $teacher->job_title }}</p>
                    <p><strong>Email:</strong> {{ $teacher->email }}</p>
                    <p><strong>Логин:</strong> {{ $teacher->user->name ?? '' }}</p>
                    <p><strong>Предметы:</strong> 
                        @if(is_array($teacher->subjects))
                            {{ implode(', ', $teacher->subjects) }}
                        @else
                            {{ $teacher->subjects }}
                        @endif
                    </p>
                    <p><strong>Образование:</strong> 
                        @if(is_array($teacher->education))
                            {{ implode(', ', $teacher->education) }}
                        @else
                            {{ $teacher->education }}
                        @endif
                    </p>
                    @if(!empty($teacher->achievements))
                        <p><strong>Достижения:</strong> 
                            @if(is_array($teacher->achievements))
                                {{ implode(', ', $teacher->achievements) }}
                            @else
                                {{ $teacher->achievements }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            <!-- Статистика преподавателя -->
            <div class="teacher-stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($statistics['average_performance'] ?? 0, 1) }}</div>
                    <div class="stat-label">Средняя успеваемость</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($statistics['average_attendance'] ?? 0, 1) }}%</div>
                    <div class="stat-label">Средняя посещаемость</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($statistics['average_homework'] ?? ($statistics['average_exam_score'] ?? 0), 1) }}</div>
                    <div class="stat-label">Средний балл экзаменов</div>
                </div>
            </div>

            <!-- Блок звёзд рейтинга -->
            <div class="teacher-rating-block" >
                <div class="review-rating" >
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= round($average_rating) ? 'filled' : 'empty' }}">★</span>
                        @endfor
                    </div>
                    <span class="rating-value">{{ number_format($average_rating, 1) }}/5</span>
                </div>
            </div>

            <!-- Отзывы о преподавателе -->
            
            
            <div class="reviews-section">
                <h3>Отзывы о преподавателе</h3>
                @if($teacherReviews->isEmpty())
                    <div class="reviews-container">
                        <div class="reviews">
                            <p>Отзывов пока нет</p>
                        </div>
                    </div>
                @else
                    <div class="reviews-list">
                        @foreach($teacherReviews as $review)
                            <div class="review-card approved">
                                <div class="review-header">
                                    <div class="review-sender">
                                        <img src="{{ asset('images/man.jpg') }}" alt="Avatar">
                                        <div class="sender-info">
                                            <h4>{{ $review->sender_name }}</h4>
                                            <span class="sender-type">{{ $review->sender_type == 'teacher' ? 'Преподаватель' : 'Студент' }}</span>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="star {{ $i <= $review->rating ? 'filled' : 'empty' }}">★</span>
                                            @endfor
                                        </div>
                                        <span class="rating-value">{{ $review->rating }}/5</span>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <div class="review-text">
                                        {{ $review->review_text }}
                                    </div>
                                </div>
                                <div class="review-meta">
                                    <span class="review-date">{{ $review->created_at->format('d.m.Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<style>
@import './colors.css';

.admin-dashboard {
    padding: 20px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.profile-section {
    display: flex;
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 2px 8px var(--card-shadow);
    padding: 24px;
    margin-bottom: 30px;
}

.teacher-avatar {
    margin-right: 24px;
}

.teacher-rating-block {
    text-align: center;
}

.teacher-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f0f0f0;
}

.teacher-details h3 {
    margin: 0 0 16px 0;
    color: #333;
    font-size: 24px;
}

.teacher-details p {
    margin: 8px 0;
    color: #666;
    line-height: 1.5;
}

.teacher-details strong {
    color: #333;
}

.teacher-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    color: #131936;
}

.stat-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-value {
    font-size: 32px;
    font-weight: bold;

    margin-bottom: 8px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.reviews-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.reviews-section h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 20px;
}

.reviews-container {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
}

.reviews {
    color: #666;
    font-style: italic;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.review-item {
    background: #f8fafc;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #131936;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.review-sender {
    display: flex;
    align-items: center;
    gap: 10px;
}

.review-sender img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
}

.sender-info h4 {
    color: #333;
    font-size: 14px;
    margin: 0 0 3px 0;
}

.sender-type {
    color: #666;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 500;
}

.review-rating {
    text-align: center;
    align-items: center;
    gap: 5px;
    display:inline-block;
}

.rating-stars {
    display: flex;
    gap: 1px;
}

/* Стилизация звёзд рейтинга (как в admin/reviews)
.star {
    color: #f39c12;
    font-size: 22px;
    margin-right: 1px;
}
.star.filled {
    color: #f39c12;
}
.star.empty {
    color: #d1d5db;
} */



.rating-value {
    color: #666;
    font-size: 11px;
    font-weight: 500;
    text-align: center;
}

.rating-stars {
    display: flex;
    
    gap: 2px;
}

.rating-stars .star {
    font-size: 22px;
    color: var(--accent-color);
    text-shadow: 0 1px 2px var(--border-dark), 0 0 1px #fff;
    transition: color 0.2s, text-shadow 0.2s;
}
.rating-stars .star.filled,
.rating-stars .star.selected {
    color: var(--status-pending);
    text-shadow: 0 2px 6px var(--secondary-color), 0 0 2px #fff;
}


.review-content {
    margin-bottom: 10px;
}

.review-text {
    color: #333;
    font-size: 13px;
    line-height: 1.5;
}

.review-meta {
    display: flex;
    justify-content: flex-end;
}

.review-date {
    color: #999;
    font-size: 11px;
}

.action-btn {
    background: var(--btn-primary);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.action-btn:hover {
    background: var(--btn-primary-hover);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .profile-section {
        flex-direction: column;
        text-align: center;
    }
    
    .teacher-avatar {
        margin-right: 0;
        margin-bottom: 20px;
    }
    
    .teacher-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 