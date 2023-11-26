@extends('admin.partials.adminPanel')

@section('title', 'Регистрация в админ панели')
@section('no_header_page', 'Y')
@section('admin-content')
    <div class="admin__form">
        <form method="POST" action="{{ route("admin.registration_process") }}" class="admin__form-login form-login form">
            @csrf
            <input name="name" type="text"
                   class="form__input @error('name') border-red-500 @enderror"
                   placeholder="Имя" />

            @error('name')
            <p class="text-red-500">{{ $message }}</p>
            @enderror

            <input name="email" type="text"
               class="form__input @error('email') border-red-500 @enderror"
               placeholder="Email" />

            @error('email')
            <p class="text-red-500">{{ $message }}</p>
            @enderror

            <input name="password" type="password"
               class="form__input @error('password') border-red-500 @enderror"
               placeholder="Пароль" />

            @error('password')
            <p class="text-red-500">{{ $message }}</p>
            @enderror

            <input name="password_confirmation" type="password"
               class="form__input @error('password_confirmation') border-red-500 @enderror"
               placeholder="Подтверждение пароля" />

            @error('password_confirmation')
            <p class="text-red-500">{{ $message }}</p>
            @enderror



            <button type="submit" class="form__submit btn btn-blue">Зарегистрироваться</button>
            <div class="has-account__wrap">
                <a href="{{ route("admin.login") }}" class="btn btn-blue">Есть аккаунт?</a>
            </div>
        </form>
    </div>
@endsection
