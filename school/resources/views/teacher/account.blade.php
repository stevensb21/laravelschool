@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/teacher/dashboard.css'])
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
@include('teacher.nav')
@endif

<div class="container">
<div class="content">
    @if(isset($isAdmin) && $isAdmin)
        <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <h2 style="margin: 0; color: #333;">Просмотр личного кабинета преподавателя: {{ $teacher->fio }}</h2>
            <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
        </div>
    @endif
    
    <div class="profileReviews">
        <div class="profile">
            <div class="photo">
                <img src="{{ asset('images/man.jpg') }}" alt="Аватар">
            </div>
            <div class="info">
                <p style="font-size: 15pt;">{{ $teacher->fio }}</p>
                <p style="font-size: 12pt;">Должность: {{ $teacher->job_title }}</p>
                <p style="font-size: 12pt;">Электронная почта: {{ $teacher->email }}</p>
            </div>
        </div>
        
        <div class="reviews-container">
            <h3>Отзывы о преподавателе:</h3>
            @if(isset($reviews) && $reviews->count() > 0)
                @foreach($reviews as $review)
                    <div class="reviews">
                        <div class="review-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ $review->sender_name }}</strong>
                            <div class="rating-stars" style="display: flex; gap: 2px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" style="color: {{ $i <= $review->rating ? 'var(--status-pending)' : 'var(--border-light)' }};">★</span>
                                @endfor
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
    
    <div class="static">
        <div class="statistics">
            <div class="education">
                <h3 style="font-weight: normal">Образование:</h3>
                <p>@foreach ($teacher->education as $item)
                    <p>{{ $item }}</p>
                @endforeach</p>
            </div>
            <div class="achievements">
                <h3 style="font-weight: normal">Личные достижения:</h3>
                <p>@foreach ($teacher->achievements as $item)
                    <p>{{ $item }}</p>
                @endforeach</p>
            </div>
            <div class="items">
                <h3 style="font-weight: normal">Предметы:<h3>
                <ul style="font-weight: normal; font-size: 13pt;">
                    @foreach ($teacher->subjects as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="average">
                <div class="averperf">
                    <p style="font-size: 14pt;">Средняя успеваемость за работу на уроке:</p>
                    <p class="numbers" style="font-size: 30pt;">{{ $statistics['average_performance'] ?? 0 }}</p>
                </div>
                <div class="averatt">
                    <p style="font-size: 14pt;">Средняя посещаемость уроков:</p>
                    <p class="numbers" style="font-size: 30pt;">{{ $statistics['average_attendance'] ?? 0 }}%</p>
                </div>
                <div class="averexam">
                    <p style="font-size: 14pt;">Средняя за домашнюю работу:</p>
                    <p class="numbers" style="font-size: 30pt;">{{ $statistics['average_homework'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

