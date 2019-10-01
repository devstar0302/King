<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <title>{{ $title }}</title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{url('/')}}/img/favicon.ico" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ url('/') }}/css/fontawesome.min.css">
        <link rel="stylesheet" href="{{url('/')}}/css/frontend/reset.css">
        <link rel="stylesheet" href="{{url('/')}}/css/frontend/style.css">
        <link rel="stylesheet" href="{{url('/')}}/css/app.css" media="all">
        <link rel="stylesheet" href="{{url('/')}}/css/custom.css" media="all">

        @if (app()->getLocale() == 'he')
            <link href="{{url('/')}}/css/frontend/style_rtl.css" rel="stylesheet">
            <link href="{{url('/')}}/css/custom_rtl.css" rel="stylesheet" media="all">
        @endif

        @yield('styles')
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel header">
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">{{ __('Main')}}</a></li>
                        @if(isset($breadcrumbs))
                            @foreach($breadcrumbs as $route)
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ $route['url']}}">{{ $route['label'] }}</a></li>
                            @endforeach
                        @else
                            @if(Request::segment(1) != null)
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ isset($breadcrumbs_url)?$breadcrumbs_url:action('ParagraphController@index')}}">{{ucfirst(Request::segment(1))}}</a></li>
                            @endif
                            @if(Request::segment(2) != null)
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{action('ParagraphController@index')}}">{{ucfirst(Request::segment(2))}}</a></li>
                            @endif
                        @endif
                    </ol>
                </div>

                <a href="{{url('/')}}"><img src="{{url('/')}}/img/logo.png" class="logo-img"></a>

                @if(env('APP_ENV', 'local') == 'local')
                    <div class='lang-switch'>
                        <label class="toggle">
                            <input type="checkbox" name="toggle-status" @if (App::getLocale() == 'he') checked='checked' @endif }} onclick="$('#lang-form').submit()">
                            <i data-swchon-text="{{__('he')}}" data-swchoff-text="{{__('en')}}"></i>
                        </label>
                        <form id="lang-form" action="{{ route('lang.switch', ['lang' => (App::getLocale() == 'he' ? 'en' : 'he')]) }}" method="POST" style="display: none;"> @csrf </form>
                    </div>
                @endif

                <div class="logout-wrapper">
                    <a href="{{ route('logout') }}" title="{{ __('Log out') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>

            <nav class="navbar navbar-expand-md navbar-light navbar-laravel footer">
                <div class="footer-wrapper">
                    <a href="mailto:a@pampuni.com">{{__('Created by Pampuni')}}</a>
                </div>
            </nav>
        </div>

        @include('components.alert')

        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.4/dist/sweetalert2.all.min.js"></script>
        <script src="{{url('/')}}/js/custom.js"></script>

        @yield('scripts')
    </body>
</html>
