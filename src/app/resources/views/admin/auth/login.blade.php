@extends('admin.partials.adminPanel')

@section('title', 'Авторизация')
@section('no_header_page', 'Y')
@section('admin-content')
   <div class="admin__form">
        <form method="POST" action="{{ route("admin.login_process") }}" class="admin__form-login form-login form">
            @csrf
            <input name="email" type="text" class="form__input @error('email') border-red-500 @enderror" placeholder="Email" />

            @error('email')
            <p class="text-red-500">{{ $message }}</p>
            @enderror

            <input name="password" type="password" class="form__input @error('password') border-red-500 @enderror" placeholder="Пароль" />

            @error('password')
            <p class="text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit" class="form__submit btn btn-blue">Войти</button>
            <a href="{{ route("admin.registration") }}"  class="btn btn-blue">Зарегистрироваться</a>
        </form>
   </div>
@endsection
