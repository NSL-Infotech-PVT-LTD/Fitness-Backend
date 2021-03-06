<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <!--login start-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">	
        <link rel="icon" type="image/png" href="{{asset('login-styles/images/icons/favicon.ico')}}"/>
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="{{asset('login-styles/text/css" href="fonts/iconic/css/material-design-iconic-font.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/animate/animate.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/css-hamburgers/hamburgers.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/animsition/css/animsition.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/select2/select2.min.css')}}">	
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/vendor/daterangepicker/daterangepicker.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/css/util.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('login-styles/css/main.css')}}">
        
        <!--login end-->
</head>
<body>
    <div id="app">
            <div class="container">
<!--                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel').' Admin Login' }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>-->

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @if(!Auth::check())
<!--                            <li><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                            <li><a class="nav-link" href="{{ url('/register') }}">Register</a></li>-->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('/logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
