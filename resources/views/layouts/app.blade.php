<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Defer attribute makes the external scripts load wait until page is fully parsed, so it results in non working scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <!-- jQuery UI -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- DataTables Scripts and CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    
    <!-- Custom CSS File -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <!-- Custom App Scripts -->
    <!-- 
        Retrieve all submit buttons. Then, for every one of them, binds a function with an 'onclick' event, displaying a message based on which action performs on submit, 
        e.g. DELETE, SAVE, ETC. and returns a confirm prompt with the proper message, e.g. The record will be modified. Are you sure? 
    -->
    <script src="{{ asset('js/onConfirmBtns.js') }}"></script>

    @stack('headStyles')

    @stack('headScripts')

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <!-- {{ __('Home') }} -->
                     <i class="fas fa-home"></i>
                    {{ config('app.name', 'PC Box') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <!-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <!-- Borrar tras pruebas -->
                                    <a class="dropdown-item" href="{{ route('profile') }}">{{ __('Perfil de usuario') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @if(Auth::user()->role != 'admin')
                                    <a class="dropdown-item" href="{{ route('profile') }}">{{ __('Perfil de usuario') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @else
                                        @if(Route::current()->getName() != 'register')
                                            <a class="dropdown-item" href="{{ route('register') }}">{{ __('Registrar usuario') }}</a>
                                            <div class="dropdown-divider"></div>
                                        @endif
                                    <a class="dropdown-item" href="{{ route('users.index') }}">{{ __('Gestionar usuarios') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- <main class="py-4"> -->
            @yield('content')

            @yield('datatables')

            @yield('content2')
        <!-- </main> -->
        
    </div>
<!-- 
    ----- DataTables Scripts -----
        -- jQuery --
         <script src="//code.jquery.com/jquery.js"></script>
        -- DataTables --
         <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

         --jQuery --
         <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        -- DataTables --
         <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> 
-->

    <!-- Custom App scripts -->

<!-- 
    Every back button/link references the REAL previous URL, not the last one from it received the request. 
    That means, in form validations errors, the back button will get us into the URL we did the initial request, and not the last URL we did the submit.
    This way, back button functionality is working as expected without stablish a fixed URL on it
-->
<script>
    $(document).ready(() => {
        $('.backURL').on('click', (e) => {
            parent.history.back();
            e.preventDefault();
        });
    })
</script>

<script>
    
</script>
    @stack('scripts')
</body>
</html>
