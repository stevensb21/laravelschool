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
}

body {
    background-image: url('{{ asset('images/first_page.png') }}') !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    flex-direction: column !important;
}

/* Мобильная версия - используем вертикальное изображение */
@media (max-width: 1200px) {
    body {
        background-image: url('{{ asset('images/authback720х1080.png') }}') !important;
        background-size: cover !important;
        background-position: center top !important;
        background-repeat: no-repeat !important;
        background-color: #f5f5f5 !important;
    }
    
    .site-title {
        color: #ffffff !important;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8) !important;
    }
    
    .container {
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
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
@endsection
