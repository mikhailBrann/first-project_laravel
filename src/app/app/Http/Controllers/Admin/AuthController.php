<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        $user = auth("admin")->user();

        if(isset($user)) {
            return view('admin.welcome', [
                "user" => $user,
            ]);
        } else {
            return redirect(route('admin.login'));
        }
    }

    public function loginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * method to login user in admin panel.
     * @param Illuminate\Http\Request $request
     */
    public function login(Request $request)
    {
        //если условия валидации не выполняяться, то в @error('название поля') в шаблоне прилетит ошибка
        $inputData = $request->validate([
            "email" => ["required", "email", "string"],
            "password" => ["required"]
        ]);

        //авторизуем юзера через функцию attempt
        if(auth("admin")->attempt($inputData)) {
            //при успешной авторизации редиректим на главную
            return redirect(route('admin.products.index'));
        } else {
            //иначе возращаем на страницу авторизации с ошибкой
            return redirect(route('admin.login'))->withErrors([
                "email" => "Пользователь не найден или данные введены некорректно"
            ]);
        }
    }

    public function logout()
    {
        //разлогиниваем текущего пользователя через функцию auth
        auth("admin")->logout();

        //редиректим на главную
        return redirect(route('main'));
    }

    /**
     * method show register form
     */
    public function showRegisterForm()
    {
        return view('admin.auth.registration');
    }

    /**
     * register user in admin panel.
     * @param Illuminate\Http\Request $request
     */
    public function register(Request $request)
    {
        $inputData = $request->validate([
            "name" => ["required", "string"],
            //валидируем поле email по таблице admin_users c проверкой на уникальность
            "email" => ["required", "email", "string", "unique:admin_users,email"],
            //при добавлении валидации confirmed laravel будет искать в запросе поле password_confirmation и сравнивать с ним
            "password" => ["required", "confirmed"]
        ]);

        //если валидация прошла, то создаем юзера из проваледированных полей, через функцию bcrypt хешируем пароль
        $user = AdminUser::create([
            "name" => $inputData["name"],
            "email" => $inputData["email"],
            "password" => bcrypt($inputData["password"])
        ]);

        //если запись с юзером успешно добавлена в БД, авторизуем его через функцию auth
        if($user) {
            auth("admin")->login($user);
        }

        //редиректим на главную
        return redirect(route('admin.products.index'));
    }
}
