@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/student/dashboard.css'])
@endsection

@include('admin.layouts.adminNav')

<div class="container">
<main class="content">
    <div class="profile-section">
        <h2>Информация о студенте</h2>
        <div class="profile-info">
            <div class="profile-header">
                <img src="{{ asset('images/man.png') }}" alt="Аватар" class="avatar">
                <div class="profile-details">
                    <h3>{{ $student->fio }}</h3>
                    <p>Группа: {{ $student->group_name }}</p>
                    <p>День рождения: {{ $student->datebirthday }}</p>
                    <p>День поступления: {{ $student->datewelcome }}</p>
                    <p>Номер телефона: {{ $student->numberphone }}</p>
                    <p>ФИО родителя: {{ $student->femaleparent }}</p>
                    <p>Номер телефона родителя: {{ $student->numberparent }}</p>
                </div>
            </div>
            <div class="profile-stats">
                <div class="stat-card">
                    <h4>Средний балл</h4>
                    <p>{{ $student->average_performance }}</p>
                </div>
                <div class="stat-card">
                    <h4>Средняя за экзамен</h4>
                    <p>{{ $student->average_exam_score }}</p>
                </div>
                <div class="stat-card">
                    <h4>Посещаемость</h4>
                    <p>{{ $student->average_attendance }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="achievements-section">
        <h2>Достижения</h2>
        <div class="achievements-grid">
            <div class="achievement-card">
                <img src="{{ asset('images/excellent.png') }}" alt="Достижение">
                <h4>Отличник</h4>
                <p>Получено за отличную успеваемость</p>
            </div>
            <div class="achievement-card">
                <img src="{{ asset('images/olympiad.png') }}" alt="Достижение">
                <h4>Активный участник</h4>
                <p>Участие в олимпиадах</p>
            </div>
        </div>
    </div>

    @if(isset($student))
        <div class="reviews-section">
            <h2>Отзывы преподавателей</h2>
            <div class="reviews-list">
                <div class="review-card">
                    @if(isset($reviews) && $reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="reviews">
                                <div class="review-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <strong>{{ $review->sender_name }}</strong>
                                    <div class="review-rating">
                                        <div class="rating-stars" style="display: flex; gap: 2px;">
                                            @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $review->rating ? 'filled' : 'empty' }}">★</span>
                                            @endfor
                                        </div>
                                        <span class="rating-value">{{ $review->rating }}/5</span>
                                    </div>
                                </div>
                                <p>{{ $review->review_text }}</p>
                                <small style="color: #666;">{{ $review->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                        @endforeach
                    @else
                        <div class="reviews">
                            <p>Отзывов пока нет</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</main> 
</div>