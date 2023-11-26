
## Пошагова инструкция:

1) Установка и настройка Laravel
   после сборки и запуска контейнеров в docker заходим в контейнер first-step-laravel и запускаем там команду создания проекта,
   если он еще не создан

```bash
composer create-project laravel/laravel app
```
незабываем настроить подключение к БД у laravel в файле окружения src/app/.env

собиаем сборку через npm
```bash 
npm run dev
```

добавляем подключение стилей и скриптов /css/app.css и /js/app.js в src/app/resources/views/welcome.blade.php
и в файл src/app/webpack.mix.js

так-же нужно создать симлинк для папки storage(туда подгружаются все файлы из форм по умолчанию)
```bash 
php artisan storage:link
```

2) Работа с БД

создаем модель Product и миграцию для таблицы products и фабрику для тестовых данных
```bash 
php artisan make:model Product --migration --factory
```

для миграции src/app/database/migrations/****_**_**_*****_create_products_table.php создаем поля
```php 
public function up()
 {
     Schema::create('products', function (Blueprint $table) {
         $table->id();
         $table->char('article', 255)->unique();
         $table->char('name', 255);
         $table->enum('status', ['available', 'unavailable'])->default('unavailable');
         $table->jsonb('data')->default('{}');
         $table->timestamps();
         $table->softDeletes();
     });
 }
```

вносим изменения в модель Product src/app/app/Models/Product.php для связи с таблицей

```php 
class Product extends Model
{
    use HasFactory;
    //связываем модель с таблицей в БД
    protected $table = 'products';
    //указываем поля, которые будут заполняться при работе с моделью
    protected $fillable = [
        'article',
        'name',
        'status',
        'data'
    ];
}
```

добавляем поля фабрике src/app/database/factories/ProductFactory.php для создания тестовых данных в БД

```php 
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article' => $this->faker->unique()->text(5),
            'name'  => $this->faker->unique()->text(15),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
            'data' => json_encode([
                'key_1' => $this->faker->text(150),
                'key_2' => $this->faker->randomNumber()
            ]),
        ];
    }
}
```

в src/app/database/seeders/DatabaseSeeder.php создаем 20 случайных продуктов для теста

```php 
public function run()
{
   Product::factory(20)->create();
}
```

Далее делаем все тоже самое для AdminUser

```php 
#src/app/app/Models/AdminUser.php

class AdminUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}

#src/app/database/migrations/2023_11_05_185120_create_admin_users_table.php

public function up()
 {
     Schema::create('admin_users', function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('email')->unique();
         $table->string('password');
         $table->timestamps();
     });
 }

#src/app/database/seeders/DatabaseSeeder.php - тут сразу передадим тестовые данные для админа
public function run()
{
  Product::factory(20)->create();
  AdminUser::factory(1)->create([
      "name" => "admin",
      "email" => env('PGADMIN_EMAIL'),
      "password" => bcrypt(env('PGADMIN_PASSWORD'))
  ]);
}
```

далее запускаем миграции с сидами
```bash 
php artisan migrate --seed
```

При создании таблицы для AdminUser я забыл добавить поле с ролью администратора (admin),
поэтому придется создать дополнительную миграцию c этим полем

```bash 
php artisan make:migration add_column_admin_to_admin_users_table
```

Заполняем новую миграцию src/app/database/migrations/****_**_**_******_add_column_admin_to_admin_users_table.php
```php
class AddColumnAdminToAdminUsersTable extends Migration
{

    public function up()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->boolean('admin')->default(0)->after('name');
        });
    }


    public function down()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('admin');
        });
    }
}
```

```bash 
php artisan migrate
```


3) Роутинг

настраиваем весь роутинг на src/app/routes/web.php, для этого вносим правки в файлы:
- src/app/app/Http/Kernel.php
- src/app/app/Providers/RouteServiceProvider.php

```bash
# посмотреть полный список роутов
php artisan route:list
```

создаем контролер для главной страницы
```bash
php artisan make:controller IndexController
```
после этого выводим логику контроллера для главной в src/app/app/Http/Controllers/IndexController.php,
а роут для главной в src/app/routes/web.php переписываем

```php
Route::get('/', [
    \App\Http\Controllers\IndexController::class,
    'index',
])->name('main');
```

для удобства создаем шаблоны для вывода товара на главную:
- src/app/resources/views/welcome.blade.php
- src/app/resources/views/layout

передаем данные из БД в через контроллер src/app/app/Http/Controllers/IndexController.php в src/app/resources/views/welcome.blade.php
и выводим в шаблон
```php
class IndexController extends Controller
{
    public function index()
    {
        $products = Product::orderBy("name", "ASC")->get();

        return view('welcome', [
            "products" => $products,
        ]);
    }
}
```

4) Авторизация

настройки гардов и провайдеров аторизации находятся в файле src/app/config/auth.php

создаем контролер, который будет отвечать за авторизацию
```bash
php artisan make:controller AuthController
```

создаем в контроллере метод для вывода формы авторизации

```php
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
}
```


далее создаем мидлвары с роутами в файле src/app/routes/web.php

```php
//группа роутов с авторизацией
Route::middleware("auth")->group(function() {
    Route::get('/logout', [
        \App\Http\Controllers\AuthController::class,
        'logout'
    ])->name('logout');
});

//группа роутов без авторизации
Route::middleware("guest")->group(function() {
    Route::get('/login', [
        \App\Http\Controllers\AuthController::class,
        'showLoginForm'
    ])->name('login');
    Route::post('/login_process', [
        \App\Http\Controllers\AuthController::class,
        'login'
    ])->name('login_process');

    Route::get('/registration', [
        \App\Http\Controllers\AuthController::class,
        'showRegisterForm'
    ])->name('registration');
    Route::post('/registration_process', [
        \App\Http\Controllers\AuthController::class,
        'register'
    ])->name('registration_process');
});
```

создаем шаблон для авторизации и регистрации
- src/app/resources/views/auth/login.blade.php
- src/app/resources/views/auth/registration.blade.php

добавляем в главный layout src/app/resources/views/layout/main.blade.php функционал перехода для регистрации и авторизации

```php
<div class="site-header__auth auth">
  {{-- разделяем логику для авторизованных и не авторизованных пользователей --}}
  @guest("web")
      @sectionMissing('auth_page')
          <a href="{{route('login')}}" class="auth__link auth-btn">Авторизоваться</a>
      @endif
      @sectionMissing('registr_page')
          <a href="{{route('registration')}}" class="auth__link registr-btn">Регистрация</a>
      @endif
  @endguest

  @auth("web")
      <a href="{{route('logout')}}" class="auth__link auth-btn">Выйти</a>
  @endauth
</div>
```

5) Формы и уведомления

создаем класс для отправки email
```bash
php artisan make:mail ContactForm
```

создаем класс для отправки форм
```bash
php artisan make:request ContactFormRequest
```

добавляем валидацию для формы в  src/app/app/Http/Requests/ContactFormRequest.php
```php
class ContactFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "email" => ["required", "email"],
            "text" => ["required", "min:5"]
        ];
    }
}
```

добавляем методы для класса  src/app/app/Mail/ContactForm.php
```php
class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    protected $formData = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact_form')->with($this->formData);
    }
}
```


добавляем методы для работы с формой в контроллер src/app/app/Http/Controllers/IndexController.php
```php
//метод отвечает за вывод формы
 public function getContactForm()
 {
     return view('forms.contact_form');
 }

 //метод отвечает за обработку формы
 public function contactForm(ContactFormRequest $request)
 {
     Mail::to(env('PGADMIN_EMAIL'))->send(new ContactForm($request->validated()));

     return redirect(route("contact_form"));
 }
```

создаем роуты для формы src/app/routes/web.php
```php
Route::get('/contact_form', [
    \App\Http\Controllers\IndexController::class,
    'getContactForm'
])->name('contact_form');

Route::post('/contact_form_process', [
    \App\Http\Controllers\IndexController::class,
    'contactForm'
])->name('contact_form_process');
```

создаем вьюхи для формы и шаблона письма
- src/app/resources/views/forms/contact_form.blade.php
- src/app/resources/views/emails/contact_form.blade.php

6) Админка

создаем новым роут src/app/routes/admin.php для админки по примеру web,
подключаем мидлвары в src/app/app/Http/Kernel.php

```php
protected $middlewareGroups = [
     'web' => [
         \App\Http\Middleware\EncryptCookies::class,
         \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
         \Illuminate\Session\Middleware\StartSession::class,
         // \Illuminate\Session\Middleware\AuthenticateSession::class,
         \Illuminate\View\Middleware\ShareErrorsFromSession::class,
         \App\Http\Middleware\VerifyCsrfToken::class,
         \Illuminate\Routing\Middleware\SubstituteBindings::class,
     ],
     'admin' => [
         \App\Http\Middleware\EncryptCookies::class,
         \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
         \Illuminate\Session\Middleware\StartSession::class,
         // \Illuminate\Session\Middleware\AuthenticateSession::class,
         \Illuminate\View\Middleware\ShareErrorsFromSession::class,
         \App\Http\Middleware\VerifyCsrfToken::class,
         \Illuminate\Routing\Middleware\SubstituteBindings::class,
     ],

 ];
```

подключаем роут в src/app/app/Providers/RouteServiceProvider.php

```php
public function boot()
 {
     $this->configureRateLimiting();

     $this->routes(function () {
         Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
             
         Route::middleware('admin')
             ->prefix('admin')
             ->name('admin.')
             ->namespace($this->namespace)
             ->group(base_path('routes/admin.php'));
     });
 }
```

создаем контроллер для админ панели (параметр --resource закладывает вкласе методы, удобные для админ панели)

```bash
php artisan make:controller Admin/ProductController --resource
```

Добавляем ресурс в роут src/app/routes/admin.php

```php
Route::resource('products', \App\Http\Controllers\Admin\PostController::class);
```

создаем контроллер для авторизации админа

```bash
php artisan make:controller Admin/AuthController
```

```php
class AuthController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

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
}
```

далее настраиваем авторизацию в файле src/app/config/auth.php

```php
'guards' => [
     'web' => [
         'driver' => 'session',
         'provider' => 'users',
     ],
     'admin' => [
         'driver' => 'session',
         'provider' => 'admin_users',
     ],
 ],

/*------*/
 'providers' => [
     'users' => [
         'driver' => 'eloquent',
         'model' => App\Models\User::class,
     ],
     'admin_users' => [
         'driver' => 'eloquent',
         'model' => App\Models\AdminUser::class,
     ],
 ],
```

создаем шаблоны для админки src/app/resources/views/admin

меняем  наследование в модели админа src/app/app/Models/AdminUser.php

```php
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
```

создаем класс для запросов на работу с таблицей products через админку

```bash
php artisan make:request Admin/ProductFormRequest
```

прописываем проверку авторизации и правила полей для валидации
```php
class ProductFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("admin")->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ruleArr = [
            "name" => ["required", "min:10", "max:255"],
            "status" => ["required"],
            "data" => []
        ];

        $articleFlag = false;

        //валидируем артикул только при редактировании и если он изменился
        if (isset($this->id)) {
            $productInDB = Product::find($this->id);
            $articleFlag =  mb_strtolower(trim($this->article)) != mb_strtolower(trim($productInDB->article));
        } else {
            $articleFlag = true;
        }

        if($articleFlag) {
            $ruleArr["article"] = [
                "required",
                "unique:products",
                "regex:/^[a-zA-Z0-9]+$/",
                "max:255"
            ];
        }

        return $ruleArr;
    }

    /**
     * lang to error message
     *
     * @return array
     */
    public function messages()
    {
        return [
            'article.regex' => 'Артикул должен состоять только из латинских букв и цифр',
            'article.unique' => 'Артикул с таким значением уже есть у другого товара',
            'article.required' => 'Артикул не может быть пустым',
            'article.max' => 'Артикул не может превышать 255 символов',
            'name.required' => 'Название товара не может быть пустым',
            'name.min' => 'Название товара не может быть короче 10 символов',
            'name.max' => 'Название товара не может превышать 255 символов',
        ];
    }
}
```

7) Очереди

как и в 5 пункте создаем класс для отправки email

```bash
php artisan make:mail CreateProductNotific
```
создаем шаблон письма для отправки после
создания товара resources/views/emails/admin/create_product_notif.blade.php


заполняем методы для класса Mail/CreateProductNotific.php и передаем в качестве
аргумента конструктора модели Product и AdminUser

```php
class CreateProductNotific extends Mailable
{
    use Queueable, SerializesModels;

    protected $product;
    protected $user;

    /**
     * Create a new message instance.
     * @param  App\Models\Product  $product
     * @param App\Models\AdminUser $user
     * @return void
     */
    public function __construct(Product $product, AdminUser $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.admin.create_product_notif')->with([
            'productName' => $this->product->name,
            'productArticle' => $this->product->article,
            'productCreateDate' => $this->product->created_at,
            'userName' => $this->user->name,
            'userEmail' => $this->user->email,
        ]);
    }
}
```

меняем в файле src/app/.env настройку типа очереди на database

```bash
php artisan queue:table
```

теперь создаем миграцию с табицей table, так как все задачи будут храниться в БД

```bash
QUEUE_CONNECTION=database
```
и добавляем ее в БД

```bash
php artisan migrate
```

создаем саму задачу, которая будет выполняться в очереди

```bash
php artisan make:job CreateProductNotificJob
```

добавляем методы к классу Jobs/CreateProductNotificJob.php

```php
class CreateProductNotificJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to(env('PGADMIN_EMAIL'))->send(new CreateProductNotific($this->product, $this->user));
    }
}
```

для проверки запускаем воркер очереди

```bash
php artisan queue:work
```