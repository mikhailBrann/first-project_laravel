<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        //если условия валидации не выполняяться, то в @error('название поля') в шаблоне прилетит ошибка
        $inputData = $request->validate([
            "email" => ["required", "email", "string"],
            "password" => ["required"]
        ]);

        //авторизуем юзера через функцию attempt
        if(auth("web")->attempt($inputData)) {
            //при успешной авторизации редиректим на главную
            return redirect(route('main'));
        } else {
            //иначе возращаем на страницу авторизации с ошибкой
            return redirect(route('login'))->withErrors([
                "email" => "Пользователь не найден или данные введены некорректно"
            ]);
        }
    }

    public function showRegisterForm()
    {
        return view('auth.registration');
    }

    public function register(Request $request)
    {
        $inputData = $request->validate([
            "name" => ["required", "string"],
            //валидируем поле email по таблице users c проверкой на уникальность
            "email" => ["required", "email", "string", "unique:users,email"],
            //при добавлении валидации confirmed laravel будет искать в запросе поле password_confirmation и сравнивать с ним
            "password" => ["required", "confirmed"]
        ]);

        //если валидация прошла, то создаем юзера из проваледированных полей, через функцию bcrypt хешируем пароль
        $user = User::create([
            "name" => $inputData["name"],
            "email" => $inputData["email"],
            "password" => bcrypt($inputData["password"])
        ]);

        //если запись с юзером успешно добавлена в БД, авторизуем его через функцию auth
        if($user) {
            auth("web")->login($user);
        }

        //редиректим на главную
        return redirect(route('main'));
    }

    public function logout()
    {
        //разлогиниваем текущего пользователя через функцию auth
        auth("web")->logout();

        //редиректим на главную
        return redirect(route('main'));
    }

    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    public function forgot(Request $request)
    {
        //проверяем есть ли пользователь с таким email в таблице users
        $inputData = $request->validate([
            "email" => ["required", "email", "string", "exists:users"],
        ]);

        $user = User::where(["email" => $inputData["email"]])->first();
        $password = uniqid();

        $user->password = bcrypt($password);
        $user->save();

        Mail::to($user)->send(new ForgotPassword($password));

        return redirect(route('main'));
    }
}
