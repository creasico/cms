<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('project.title') }}</title>

    {{-- DOC: include application favicons --}}
    @include('_partials.favicons')

    {{-- DOC: include page head, this section contains all stylesheets --}}
    @include('_partials._head')
</head>
<body id="app-layout">
    {{-- DOC: include the main navbar --}}
    @include('_partials.navbar')

    {{-- DOC: retrieve the page content --}}
    @yield('page-content')

    {{-- DOC: include the page foot, this section contains all javascripts --}}
    @include('_partials._foot')
</body>
</html>
