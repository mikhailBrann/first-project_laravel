# Каталог + административная панель к нему на Laravel

## техническое задание
>Создать таблицу products через миграции:
  Создать Eloquent-модель «Product», связанную с таблицей «products». 
  В модели реализовать Local Scope для получения только доступных продуктов (STATUS = “available”).
  1) [Миграция](/src/app/database/migrations/2023_11_05_184308_create_products_table.php)
  2) [Модель](/src/app/app/Models/Product.php)

>Сделать валидацию создания и редактирования:
>- NAME — обязательное поле, длиной не менее 10 символов;
>- ARTICLE — обязательное поле, только латинские символы и цифры, уникальное в таблице.
>- Создать роль администратора, который может редактировать артикул, остальным пользователям можно редактировать всё, кроме артикула.
 Роль пользователя можно узнать из настроек (config(‘products.role’)).

При создании моделей пользователя забыл добавить поле admin(bool) для пользователей административной панели,
поэтому создал дополнительную миграцию с этим полем:
- [Модель](/src/app/app/Models/AdminUser.php)
- [Миграция](/src/app/database/migrations/2023_11_24_103203_add_column_admin_to_admin_users_table.php)
>- Реализовать валидацию и проверку прав (контроллер, модель, отдельный сервис — на своё усмотрение).

Валидацию организовал через наследование от [FormRequest](/src/app/app/Http/Requests/Admin/ProductFormRequest.php)


>- При создании продукта реализовать отправку на заданный в конфигурации Email (config(‘products.email’)) уведомления (Notification) о том, что продукт создан.
   Уведомление должно отправляться через задачу (Job) в очереди (Queue).

Добавил генерацию Job внутри контроллера [ProductController](/src/app/app/Http/Controllers/Admin/ProductController.php)
через хелпер dispatch
```php
public function store(ProductFormRequest $request)
{
    $user = auth("admin")->user();
    if(!$user) {
        return redirect(route('admin.login'));
    }

    $product = Product::create($request->validated());

    //создаем задачу на отправку письма о создании товара
    $this->dispatch(new CreateProductNotificJob($product, $user));

    return redirect(route('admin.products.index'));
}
```
[Job](/src/app/app/Jobs/CreateProductNotificJob.php)

для запуска выполнения очереди внутрь контейнера first-step-laravel выполняем
```bash
php artisan queue:work
```

>- Интерфейс приложения реализовать соответственно [макету](https://www.figma.com/file/pVspJcvzwZUYynT2dogGG2/PIN-ERP-ТЗ-03.02.2022-(Copy)?type=design&node-id=0-1&mode=design&t=pzPc1ZJlca6HnPL3-0)
>- Завернуть все в docker ✔
>- Готовое приложение выложить на GitHub / Bitbucket


## Первый запуск приложения
сначала в файле .env в корне проекта задаем свои данные
```dotenv
DB_NAME=first
DB_USER=root
DB_PASSWORD=admin1287
PGADMIN_EMAIL=your-email@mail.ru
PGADMIN_PASS=your-admin-password
SMTP_EMAIL=your-smtp-email
SMTP_PASS=your-smtp-app-password
```

Для первого запуска проекта последовательно выполняем следующие команды:

1) собираем и поднимаем контенеры
```bash
docker-compose up -d --build
```

2) переходим внутрь контейнера first-step-laravel
```bash
docker exec -it first-step-laravel bash
```

3) внутри контейнера переходим в директорию app, подтягиваем зависимости, собираем front и применяем миграции

```bash
composer install
npm install
npm run dev
php artisan migrate --seed
```

## Ронтинг проекта
### Административная часть
- [Главная](http://localhost:8080/admin)
- [Каталог](http://localhost:8080/admin/products)
- [Создание товара](http://localhost:8080/admin/products/create)
- [Детальная товара](http://localhost:8080/admin/products/1)
- [Редактирование товара](http://localhost:8080/admin/products/1/edit)
- [Авторизация](http://localhost:8080/admin/login)
- [Регистрация](http://localhost:8080/admin/registration)


### Публичная часть
- [Главная](http://localhost:8080/)
- [Каталог](http://localhost:8080/products)
- [Авторизация](http://localhost:8080/login)
- [Регистрация](http://localhost:8080/registration)
- [Забыли пароль](http://localhost:8080/forgot)

### PS:
При создании проекта документировал свои действия в [WORK_STEPS.md](WORK_STEPS.md)