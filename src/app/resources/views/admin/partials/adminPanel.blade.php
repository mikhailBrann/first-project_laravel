@extends('layout.admin')
@section('admin-panel')
    <div class="admin-panel">
        <div class="admin-panel__wrap">
            <div class="admin-panel__left">
                <a href="{{route('admin.main')}}" class="admin-logo__link">
                    <span class="admin-logo__img">
                        <img src="{{asset('image/logo-min.svg')}}" alt="logo">
                    </span>
                    <span class="admin-logo__text">Enterprice<br>Resource<br>Planning</span>
                </a>
                @sectionMissing('no_header_page')
                <nav class="admin-panel__category">
                    <ul class="admin-panel__category-list">
                        <li class="admin-panel__category-item">
                            <a href="{{route('admin.products.index')}}" class="admin-panel__category-link">Продукция</a>
                        </li>
                    </ul>
                </nav>
                @endif
            </div>
            <div class="admin-panel__right">
                @sectionMissing('no_header_page')
                <header class="admin-panel__header">
                    <nav class="admin-panel__header-nav">
                        <ul class="admin-panel__header-nav-list">
                            <li class="admin-panel__header-nav-item">
                                <a href="{{route('admin.products.index')}}" class="admin-panel__header-nav-link">
                                    Продукция
                                </a>
                            </li>
                        </ul>
                    </nav>
                    @hasSection('admin_user')
                    <div class="admin-panel__header-user">
                        <span class="admin-panel__header-user-name">{{ $user->name }}</span>
                        <a href="{{route('admin.logout')}}" class="admin-panel__header-btn user-logout btn btn-blue">Выйти</a>
                    </div>
                    @endif
                </header>
                @endif
                <section class="admin-panel__content"> @yield('admin-content')</section>
            </div>
        </div>
    </div>
@endsection
