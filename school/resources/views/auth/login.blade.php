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
    background-position: center center !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    flex-direction: column !important;
    min-height: 100vh !important;
    width: 100% !important;
}

/* Мобильная версия - используем вертикальное изображение */
@media (max-width: 1200px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png') }}') !important;
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
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

/* Дополнительные стили для очень маленьких экранов */
@media (max-width: 480px) {
    body {
        background-position: center center !important;
        background-size: cover !important;
        background-image: url('{{ asset('images/authback720х1080.png') }}') !important;
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

/* Стили для планшетов */
@media (min-width: 481px) and (max-width: 768px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png') }}') !important;
        background-size: cover !important;
        background-position: center center !important;
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
// Принудительная перезагрузка фонового изображения на мобильных устройствах
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth <= 1200) {
        const body = document.body;
        
        // Сначала пробуем загрузить мобильное изображение
        const mobileImage = new Image();
        mobileImage.onload = function() {
            body.style.backgroundImage = 'url({{ asset('images/authback720х1080.png') }})';
        };
        mobileImage.onerror = function() {
            // Если мобильное изображение не загрузилось, используем основное
            body.style.backgroundImage = 'url({{ asset('images/first_page.png') }})';
        };
        
        // Устанавливаем таймаут для загрузки
        const timeout = setTimeout(function() {
            body.style.backgroundImage = 'url({{ asset('images/first_page.png') }})';
        }, 3000); // 3 секунды таймаут
        
        mobileImage.onload = function() {
            clearTimeout(timeout);
            body.style.backgroundImage = 'url({{ asset('images/authback720х1080.png') }})';
        };
        
        mobileImage.src = '{{ asset('images/authback720х1080.png') }}';
    }
});
</script>
@endsection
