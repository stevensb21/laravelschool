<div class="review-form">
    <h3>Оставить отзыв</h3>
    <form method="POST" action="{{ route('reviews.store') }}">
        @csrf
        <input type="hidden" name="recipient_type" value="{{ $recipientType }}">
        <input type="hidden" name="recipient_id" value="{{ $recipientId }}">
        
        <div class="form-group">
            <label for="review_text">Ваш отзыв:</label>
            <textarea name="review_text" id="review_text" required placeholder="Поделитесь своими впечатлениями..."></textarea>
        </div>
        
        <div class="form-group">
            <label>Оценка:</label>
            <div class="rating-input">
                <input type="radio" name="rating" value="5" id="rating5" required>
                <input type="radio" name="rating" value="4" id="rating4">
                <input type="radio" name="rating" value="3" id="rating3">
                <input type="radio" name="rating" value="2" id="rating2">
                <input type="radio" name="rating" value="1" id="rating1">
                
                <label for="rating5">★</label>
                <label for="rating4">★</label>
                <label for="rating3">★</label>
                <label for="rating2">★</label>
                <label for="rating1">★</label>
            </div>
        </div>
        
        <button type="submit" class="submit-review-btn">Отправить отзыв</button>
    </form>
    
    <div class="review-notice">
        <p><small>Отзыв будет опубликован после проверки администратором</small></p>
    </div>
</div>

<style>
.review-form {
    background-color: var(--card-bg);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px var(--card-shadow);
    margin-bottom: 20px;
}

.review-form h3 {
    color: var(--text-primary);
    font-size: 18px;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
}

.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--input-border);
    border-radius: 5px;
    font-size: 14px;
    background-color: var(--input-bg);
    color: var(--text-primary);
    resize: vertical;
    min-height: 100px;
}

.form-group textarea:focus {
    outline: none;
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px rgba(110, 1, 4, 0.1);
}

.rating-input {
    display: flex;
    gap: 10px;
    align-items: center;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 24px;
    color: var(--border-light);
    transition: color 0.3s;
}

.rating-input input[type="radio"]:checked ~ label,
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: var(--warning-color);
}

.submit-review-btn {
    background-color: var(--btn-primary);
    color: var(--text-light);
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s;
}

.submit-review-btn:hover {
    background-color: var(--btn-primary-hover);
}

.review-notice {
    margin-top: 15px;
    padding: 10px;
    background-color: var(--bg-secondary);
    border-radius: 5px;
    border-left: 3px solid var(--status-pending);
}

.review-notice p {
    color: var(--text-secondary);
    font-size: 12px;
    margin: 0;
}
</style> 