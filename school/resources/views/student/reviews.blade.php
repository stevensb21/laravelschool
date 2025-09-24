@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/reviews.css'])
@vite(['resources/css/colors.css'])
@vite(['resources/css/student/dashboard.css'])
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
    @include('student.nav')
@endif

<div class="content">
    @if(isset($isAdmin) && $isAdmin)
        <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <h2 style="margin: 0; color: #333;">Отзывы студента: {{ $student->fio ?? auth()->user()->name }}</h2>
            @if(isset($student))
                <a href="{{ route('admin.student.profile', $student->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
            @endif
        </div>
    @endif

    <div class="reviews-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;">
        <div class="reviews-header">
            <h2>Отзывы</h2>
            <button class="action-btn" onclick="showReviewForm()" style="background: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Оставить отзыв
            </button>
        </div>

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <!-- Форма отправки отзыва -->
        <div id="reviewForm" class="review-form" style="display: none;">
            <h3>Оставить отзыв о преподавателе</h3>
            <form method="POST" action="{{ route('student.reviews.store') }}">
                @csrf
                <div class="form-group">
                    <label for="recipient_id">Выберите преподавателя:</label>
                    <select name="recipient_id" id="recipient_id" required>
                        <option value="">Выберите преподавателя...</option>
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->fio ?? $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="review_text">Текст отзыва:</label>
                    <textarea name="review_text" id="review_text" required placeholder="Напишите ваш отзыв о преподавателе..." rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Оценка:</label>
                    <div class="rating-input">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" required>
                            <label for="star{{ $i }}">★</label>
                        @endfor
                    </div>
                </div>
                <button type="submit" class="submit-review-btn">Отправить отзыв</button>
            </form>
        </div>

        <!-- Список отзывов -->
        <div class="reviews-grid">
            @if(isset($reviews) && $reviews->count() > 0)
                @foreach($reviews as $review)
                    <div class="review-card {{ $review->status }}">
                        <div class="review-header">
                            <div class="review-sender">
                                <img src="{{ asset('images/man.jpg') }}" alt="Avatar">
                                <div class="sender-info">
                                    <h4>{{ $review->sender_name }}</h4>
                                    <span class="sender-type">{{ $review->sender_type == 'teacher' ? 'Преподаватель' : 'Студент' }}</span>
                                </div>
                            </div>
                            <span class="review-status {{ $review->status }}">
                                @switch($review->status)
                                    @case('pending')
                                        Ожидает
                                        @break
                                    @case('approved')
                                        Одобрен
                                        @break
                                    @case('rejected')
                                        Отклонен
                                        @break
                                @endswitch
                            </span>
                        </div>

                        <div class="review-content">
                            <div class="review-text">
                                {{ $review->review_text }}
                            </div>
                            
                            <div class="review-rating">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $review->rating ? 'filled' : 'empty' }}">★</span>
                                    @endfor
                                </div>
                                <span class="rating-value">{{ $review->rating }}/5</span>
                            </div>

                            <div class="review-recipient">
                                <div class="recipient-label">
                                    Отзыв для {{ $review->recipient_type == 'teacher' ? 'преподавателя' : 'студента' }}:
                                </div>
                                <div class="recipient-name">
                                    {{ $review->recipient_name }}
                                </div>
                            </div>

                            @if($review->status == 'rejected' && $review->moderation_comment)
                                <div class="moderation-comment">
                                    <div class="moderation-comment-label">Причина отклонения:</div>
                                    <div class="moderation-comment-text">{{ $review->moderation_comment }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="review-meta">
                            <span class="review-date">{{ $review->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-reviews" style="text-align:center;padding:40px;color:var(--text-color);">
                    <p>Отзывов пока нет</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showReviewForm() {
    const form = document.getElementById('reviewForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script> 