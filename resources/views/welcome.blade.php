<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PC Box - Inicio</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            span:first-child{
                display: none;
                /* position: fixed; */
                /* float: left; */
            }
            span + span{
                display: none;
                /* float: right;  */
            }

        </style>
        
        <!-- BootStrap 4.3 CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>

    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        <!-- Register route is already controlled by isAdmin middleware -->
                        <!-- @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif -->
                    @endauth
                </div>
            @endif
            <div class="container">
                <div class="content">
                    <div class="title m-b-md">
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <!-- Añadir condicional en caso de no estar conectado, modificar estructura columnada -->
                                <div class="row">
                                    @auth
                                    <div class="col-5">
                                        <span class="text-muted text-center float-left">Bienvenido</span>
                                    </div>

                                    <div class="col-7">
                                        <span class="text-muted text-center float-right">{{ Auth ::user()->name }}</span>
                                    </div>
                                    @else
                                    <div class="col-5">
                                        <span class="text-muted text-center float-left">Bienvenido</span>
                                    </div>

                                    <div class="col-7">
                                        <span class="text-muted text-center float-right">{{ __(' invitado') }}</span>
                                    </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="links">
                        <a href="https://www.pcbox.com/" target="_blank">Nuestra Web</a>
                        @auth
                            @if(Auth::user()->role == 'admin')
                                <a href="{{ route('register') }}">Registrar usuario</a>
                                <a href="{{ route('users.index') }}">Gestionar usuarios</a>
                            @endif
                            <a href="{{ route('home') }}">Ir al comparador</a>
                            <a href="{{ route('profile') }}">Ir al historial</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Scripts -->
        <script>
            $(document).ready(function() {
                $('span:first').fadeIn(1000, () => {
                    $('div > div > span').fadeIn(1000);
                });
            });
        </script>


        @if(session('message') || session('success') || session('errors'))
            @include('partials.modal')

            <script>
                $(document).ready(() => {
                    var btn = $('#openModal').click();
                    btn.hide();
                })
            </script>
        @endif
    </body>
</html>
