<header>
    <div class="site-header__top">
        <div class="content site-header__top-wrap">
            <nav class="site-header__nav">
                <ul class="site-header__nav-list">
                    <li><a href="{{route('main')}}">Главная</a></li>
                    <li><a href="{{route('products.list')}}">Каталог</a></li>
                </ul>
            </nav>
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
        </div>
    </div>
</header>
