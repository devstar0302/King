<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>{{ $title }}</title>

    <link rel="stylesheet" href="/lac/nik/frontend/css/reset.css">

    <link rel="stylesheet" href="/lac/nik/frontend/css/style.css">

    @yield('styles')

</head>

<body>



@yield('content')



<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>



@yield('scripts')



</body>

</html>