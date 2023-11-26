<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    @include('partials.main_header')
    @hasSection('show_page_title')
    <div class="content">
        <h1 class="page__title">@yield('title')</h1>
    </div>
    @endif
    @yield('content')
    <script src="/js/app.js"></script>
</body>
</html>
