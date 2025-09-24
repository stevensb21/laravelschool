<div class="reviews-section">
    <div class="reviews-header">
        <h3>Отзывы</h3>
        @if($reviews->count() > 0)
            <span class="reviews-count">{{ $reviews->count() }} отзывов</span>
        @endif
    </div>

    @if($reviews->isEmpty())
        <div class="no-reviews">
            <p>Пока нет отзывов</p>
        </div>
    @else
        <div class="reviews-list">
            @foreach($reviews as $review)
                <div class="review-item">
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

<style>
.reviews-section {
    background-color: var(--card-bg);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px var(--card-shadow);
    margin-top: 20px;
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.reviews-header h3 {
    color: var(--text-primary);
    font-size: 18px;
    margin: 0;
}

.reviews-count {
    color: var(--text-secondary);
    font-size: 14px;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.review-item {
    background-color: var(--bg-secondary);
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid var(--status-active);
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
    color: var(--text-primary);
    font-size: 14px;
    margin: 0 0 3px 0;
}

.sender-type {
    color: var(--text-secondary);
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 500;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-stars {
    display: flex;
    gap: 1px;
}

.star {
    color: var(--warning-color);
    font-size: 14px;
}

.star.filled {
    color: var(--warning-color);
}

.star.empty {
    color: var(--border-light);
}

.rating-value {
    color: var(--text-secondary);
    font-size: 11px;
    font-weight: 500;
}

.review-content {
    margin-bottom: 10px;
}

.review-text {
    color: var(--text-primary);
    font-size: 13px;
    line-height: 1.5;
}

.review-meta {
    display: flex;
    justify-content: flex-end;
}

.review-date {
    color: var(--text-muted);
    font-size: 11px;
}

.no-reviews {
    text-align: center;
    padding: 30px 20px;
    color: var(--text-muted);
}

.no-reviews p {
    font-size: 14px;
    margin: 0;
}
</style> 