@extends('layouts.app')

@section('content')
<style>
body {
    background-image: url('{{ asset('images/first_page.png') }}') !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
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
