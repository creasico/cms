@extends('_layouts._base')

@section('site-content')
    <div class="container">
        <header class="row section">
            <div class="col-md-12">
                @include('_partials.navbar')
            </div>
        </header>

        <section class="row section">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h1 class="display-3">Hello, world!</h1>
                    <p class="lead">Your Application's Landing Page</p>
                </div>
            </div>
        </section>

        <footer class="row section">
            <div class="col-md-12">
                @include('_partials.copyright')
            </div>
        </footer>
    </div>
@endsection

@section('site-head')
    {{-- DOC: include application favicons --}}
    @include('_partials.favicons')

    {{-- DOC: Load global site stylesheets --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <link href="{{ asset_url('styles/vendor.css') }}" rel="stylesheet">
    <link href="{{ asset_url('styles/app.css') }}" rel="stylesheet">

    {{-- DOC: Load page specific stylesheets --}}
    @yield('page-styles')

    {{-- DOC: Load overrider stylesheets --}}
    {{-- <link rel="stylesheet" href="{{ asset_url('path/to/style.css') }}"> --}}
@endsection

@section('site-foot')
    {{-- DOC: Load global site javascripts --}}
    <script src="{{ asset_url('scripts/vendor.js') }}"></script>
    <script src="{{ asset_url('scripts/app.js') }}"></script>

    {{-- DOC: Load page specific javascripts --}}
    @yield('page-scripts')

    {{-- DOC: Load overrider javascripts --}}
    {{-- <script src="{{ asset_url('path/to/script.js') }}"></script> --}}
@endsection
