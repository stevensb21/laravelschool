@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/reviews.css'])
@vite(['resources/js/app.js'])

@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="reviews-container">
            <div class="reviews-header">
                <h2>Модерация отзывов</h2>
                <div class="reviews-stats">
                    <span class="stat-item pending">{{ $reviews->where('status', 'pending')->count() }} ожидают</span>
                    <span class="stat-item approved">{{ $reviews->where('status', 'approved')->count() }} одобрены</span>
                    <span class="stat-item rejected">{{ $reviews->where('status', 'rejected')->count() }} отклонены</span>
                </div>
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

            <div class="reviews-filters">
                <select id="statusFilter" onchange="filterReviews()">
                    <option value="">Все статусы</option>
                    <option value="pending">Ожидают модерации</option>
                    <option value="approved">Одобренные</option>
                    <option value="rejected">Отклоненные</option>
                </select>
                <select id="typeFilter" onchange="filterReviews()">
                    <option value="">Все типы</option>
                    <option value="teacher">Отзывы преподавателям</option>
                    <option value="student">Отзывы студентам</option>
                </select>
            </div>

            <div class="reviews-grid">
                @if($reviews->isEmpty())
                    <div class="no-reviews">
                        <p>Нет отзывов для модерации</p>
                    </div>
                @else
                    @foreach($reviews as $review)
                        <div class="review-card {{ $review->status }}" data-status="{{ $review->status }}" data-type="{{ $review->sender_type }}">
                            <div class="review-header">
                                <div class="review-sender">
                                    <img src="{{ asset('images/man.png') }}" alt="Avatar">
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

                            @if($review->status == 'pending')
                                <div class="review-actions">
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="approve-btn">Одобрить</button>
                                    </form>
                                    <button class="reject-btn" onclick="openRejectModal({{ $review->id }})">Отклонить</button>
                                </div>
                            @endif

                            <div class="review-meta">
                                <span class="review-date">{{ $review->created_at->format('d.m.Y H:i') }}</span>
                                @if($review->moderated_by)
                                    <span class="moderation-info">
                                        Модерировал: {{ $review->moderator->name ?? 'Администратор' }}
                                        @if($review->moderated_at)
                                            ({{ $review->moderated_at->format('d.m.Y H:i') }})
                                        @endif
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" class="delete-review-form" data-review-id="{{ $review->id }}" style="display:inline; margin-left:10px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-btn" onclick="showDeleteReviewModal({{ $review->id }})">Удалить</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </main>
</div>

<!-- Модальное окно для отклонения отзыва -->
<div id="rejectModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeRejectModal()">&times;</span>
        <h3>Отклонить отзыв</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="moderation_comment">Причина отклонения:</label>
                <textarea name="moderation_comment" id="moderation_comment" required placeholder="Укажите причину отклонения отзыва..." rows="4" cols="40" class="model-reject-textarea"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="model-reject-btn">Отклонить</button>
                <button type="button" class="model-cancel-btn" onclick="closeRejectModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="deleteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="hideDeleteReviewModal()">&times;</span>
        <h3>Подтвердите удаление</h3>
        <p>Вы действительно хотите удалить этот отзыв?</p>
        <div class="form-actions">
            <button id="confirmDeleteBtn" class="delete-btn model-delete-btn">Удалить</button>
            <button type="button" class="cancel-btn" onclick="hideDeleteReviewModal()">Отмена</button>
        </div>
    </div>
</div>

<script>
function filterReviews() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const reviewCards = document.querySelectorAll('.review-card');

    reviewCards.forEach(card => {
        const status = card.dataset.status;
        const type = card.dataset.type;
        
        let showCard = true;
        
        if (statusFilter && status !== statusFilter) {
            showCard = false;
        }
        
        if (typeFilter && type !== typeFilter) {
            showCard = false;
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

function openRejectModal(reviewId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/reviews/${reviewId}/reject`;
    modal.style.display = 'block';
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'none';
    document.getElementById('moderation_comment').value = '';
}

let deleteReviewModalId = null;
function showDeleteReviewModal(reviewId) {
    deleteReviewModalId = reviewId;
    document.getElementById('deleteModal').classList.add('show');
}
function hideDeleteReviewModal() {
    deleteReviewModalId = null;
    document.getElementById('deleteModal').classList.remove('show');
}
document.getElementById('confirmDeleteBtn').onclick = function() {
    if (deleteReviewModalId) {
        const form = document.querySelector('.delete-review-form[data-review-id="' + deleteReviewModalId + '"]');
        if (form) form.submit();
        hideDeleteReviewModal();
    }
};
window.onclick = function(event) {
    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal && event.target == rejectModal) {
        closeRejectModal();
    }
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal && event.target == deleteModal) {
        hideDeleteReviewModal();
    }
}
</script>

@endsection 