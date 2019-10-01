<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- {{--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">--}} -->

<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HOME') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{url('/')}}/img/favicon.ico" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    {{--<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">--}}

    <!-- Styles -->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">

    <link href="{{url('/')}}/css/app.css" rel="stylesheet">
    <link href="{{url('/')}}/css/custom.css" rel="stylesheet">

    @if (app()->getLocale() == 'he')
        <link href="{{url('/')}}/css/custom_rtl.css" rel="stylesheet" media="all">
    @endif
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
            @if(count($errors))
                <div class="alert alert-danger" id="error_message">
                    <ul style="list-style: none;"
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                </div>
            @endif
            <div class="no-print">
                @include('flash::message')
            </div>
            @yield('content')
        </main>

        <nav class="navbar navbar-expand-md navbar-light navbar-laravel footer">
            <div class="footer-wrapper">
                <a href="mailto:a@pampuni.com">{{__('Created by Pampuni')}}</a>
            </div>
        </nav>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.4/dist/sweetalert2.all.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<link href="{{url('/')}}/js/custom.js" rel="stylesheet">

@yield('scripts')

<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>

</html>
