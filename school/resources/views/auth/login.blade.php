@extends('layouts.app')

@section('content')
<style>
* {
    margin: 0 !important;
    padding: 0 !important;
    box-sizing: border-box !important;
}

html, body {
    overflow: hidden !important;
    height: 100vh !important;
    width: 100vw !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    -webkit-overflow-scrolling: touch !important;
    -webkit-tap-highlight-color: transparent !important;
}

body {
    background-image: url('{{ asset('images/first_page.png') }}') !important;
    background-size: cover !important;
    background-position: center top !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    flex-direction: column !important;
    min-height: 100vh !important;
    width: 100% !important;
}

/* Fallback для устройств без поддержки background-attachment: fixed */
@supports not (background-attachment: fixed) {
    body {
        background-attachment: scroll !important;
    }
}

/* Мобильная версия - исправляем все проблемы */
@media (max-width: 1200px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png?v=3') }}') !important;
        background-size: cover !important;
        background-position: center top !important;
        background-repeat: no-repeat !important;
        background-attachment: scroll !important;
        background-color: #f5f5f5 !important;
        min-height: 100vh !important;
        width: 100% !important;
        -webkit-background-size: cover !important;
        -moz-background-size: cover !important;
        -o-background-size: cover !important;
    }
    
    .site-title {
        color: #ffffff !important;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8) !important;
        font-size: 2rem !important;
        margin-bottom: 20px !important;
    }
    
    .container {
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        margin: 20px !important;
        padding: 30px !important;
        border-radius: 15px !important;
    }
}

/* Только для мелких экранов (< 480px) */
@media (max-width: 480px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png?v=3') }}') !important;
        background-position: center top !important;
        background-size: cover !important;
        background-attachment: scroll !important;
    }
    
    .site-title {
        font-size: 1.5rem !important;
        margin-bottom: 15px !important;
    }
    
    .container {
        margin: 10px !important;
        padding: 20px !important;
    }
}

/* Только для планшетов (481px — 768px) */
@media (min-width: 481px) and (max-width: 768px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png?v=3') }}') !important;
        background-size: cover !important;
        background-position: center top !important;
        background-attachment: scroll !important;
    }
}
</style>
<h1 class="site-title">okapi-board</h1> <!-- Название сайта -->
    <div class="container">
        <div class="person-icon">
            <div class="shoulders"></div>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="name" placeholder="Логин" required value="{{ old('username') }}">
                @error('username')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <input type="password" name="password" placeholder="Пароль" required>
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Запомнить меня') }}
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" id="loginButton">Войти</button>
            </div>
        </form>
    </div>

<script>
// Упрощенная загрузка фонового изображения на мобильных устройствах
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth <= 1200) {
        const body = document.body;
        const mobileImageUrl = '{{ asset('images/authback720х1080.png?v=3') }}';
        const fallbackImageUrl = '{{ asset('images/first_page.png') }}';
        
        // Функция для установки фонового изображения
        function setBackgroundImage(url) {
            body.style.backgroundImage = 'url(' + url + ')';
        }
        
        // Создаем изображение для проверки загрузки
        const mobileImage = new Image();
        
        // Обработчик успешной загрузки
        mobileImage.onload = function() {
            console.log('Мобильное изображение успешно загружено');
            setBackgroundImage(mobileImageUrl);
        };
        
        // Обработчик ошибки загрузки
        mobileImage.onerror = function() {
            console.log('Ошибка загрузки мобильного изображения, используем fallback');
            setBackgroundImage(fallbackImageUrl);
        };
        
        // Начинаем загрузку
        mobileImage.src = mobileImageUrl;
    }
});
</script>
@endsection
