<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('project.title') }}</title>

    {{-- DOC: include page head, this section contains all stylesheets --}}
    @yield('site-head')
</head>
<body id="app-layout">
    {{-- DOC: retrieve the page content --}}
    @yield('site-content')

    <script id="site-script">
    window.baseUrl = '{{ url('') }}';
    window.siteUrl = function (path) {
        return baseUrl + '/' + path.replace(/^\/|\/$/g, '');
    };
    </script>

    @if (env('APP_ENV') != 'production')
        <script src="//cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
    @endif

    {{-- DOC: include the page foot, this section contains all javascripts --}}
    @yield('site-foot')
</body>
</html>
