@extends('layouts.app')

@section('content')
<!-- Скрытое изображение для iOS Safari -->
<div id="iosBackgroundImage" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0; pointer-events: none;">
    <img src="{{ asset('images/authback720х1080.png?v=5') }}" alt="background" style="width: 100%; height: 100%; object-fit: cover; object-position: center top;">
</div>

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

/* Специальные стили для iOS Safari */
@supports (-webkit-touch-callout: none) {
    body {
        background-attachment: scroll !important;
        -webkit-background-size: cover !important;
        background-size: cover !important;
    }
}

/* Мобильная версия - исправляем все проблемы */
@media (max-width: 1200px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png?v=5') }}') !important;
        background-size: cover !important;
        background-position: center top !important;
        background-repeat: no-repeat !important;
        background-attachment: scroll !important; /* Убираем fixed для мобильных */
        background-color: #f5f5f5 !important;
        min-height: 100vh !important;
        width: 100% !important;
        -webkit-background-size: cover !important;
        -moz-background-size: cover !important;
        -o-background-size: cover !important;
        -webkit-transform: translateZ(0) !important; /* Принудительный аппаратный рендеринг */
        transform: translateZ(0) !important;
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
        background-image: url('{{ asset('images/authback720х1080.png?v=5') }}') !important;
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
        background-image: url('{{ asset('images/authback720х1080.png?v=5') }}') !important;
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
// Радикальное решение для iOS Safari - используем img элемент
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth <= 1200) {
        const body = document.body;
        const iosBackgroundDiv = document.getElementById('iosBackgroundImage');
        const mobileImageUrl = '{{ asset('images/authback720х1080.png?v=5') }}';
        const fallbackImageUrl = '{{ asset('images/first_page.png') }}';
        
        // Проверяем, является ли устройство iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                     (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        
        console.log('Устройство iOS:', isIOS);
        console.log('Ширина экрана:', window.innerWidth);
        
        if (isIOS) {
            console.log('Применяем iOS-специфичное решение');
            
            // Для iOS используем img элемент вместо background-image
            const img = iosBackgroundDiv.querySelector('img');
            
            img.onload = function() {
                console.log('Изображение загружено для iOS, показываем его');
                iosBackgroundDiv.style.opacity = '1';
                // Убираем background-image с body
                body.style.backgroundImage = 'none';
            };
            
            img.onerror = function() {
                console.log('Ошибка загрузки изображения для iOS, используем fallback');
                body.style.backgroundImage = 'url(' + fallbackImageUrl + ')';
            };
            
            // Принудительно загружаем изображение
            img.src = mobileImageUrl;
            
        } else {
            // Для других устройств используем стандартный подход
            console.log('Применяем стандартное решение для не-iOS устройств');
            
            const mobileImage = new Image();
            mobileImage.crossOrigin = 'anonymous';
            
            mobileImage.onload = function() {
                console.log('Мобильное изображение успешно загружено');
                body.style.backgroundImage = 'url(' + mobileImageUrl + ')';
            };
            
            mobileImage.onerror = function() {
                console.log('Ошибка загрузки мобильного изображения, используем fallback');
                body.style.backgroundImage = 'url(' + fallbackImageUrl + ')';
            };
            
            mobileImage.src = mobileImageUrl;
        }
    }
});

// Дополнительная проверка при изменении ориентации экрана
window.addEventListener('orientationchange', function() {
    console.log('Изменение ориентации экрана');
    setTimeout(function() {
        if (window.innerWidth <= 1200) {
            console.log('Перезагружаем страницу при смене ориентации');
            location.reload();
        }
    }, 500);
});

// Проверка при загрузке страницы
window.addEventListener('load', function() {
    console.log('Страница полностью загружена');
    if (window.innerWidth <= 1200) {
        const iosBackgroundDiv = document.getElementById('iosBackgroundImage');
        if (iosBackgroundDiv) {
            const img = iosBackgroundDiv.querySelector('img');
            if (img.complete && img.naturalHeight !== 0) {
                console.log('Изображение уже загружено, показываем его');
                iosBackgroundDiv.style.opacity = '1';
            }
        }
    }
});
</script>
@endsection
