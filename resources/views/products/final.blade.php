<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PC Box - Resultados de búsqueda</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom CSS File -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <!-- choose a theme file -->
    <link rel="stylesheet" href="{{ asset('tableorder-master/css/theme.bootstrap_4.css') }}">
    <!-- load jQuery and tablesorter scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
    <script type="text/javascript" src="{{ asset('tablesorter-master/js/jquery.tablesorter.js') }}"></script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
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
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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

        <div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="text-center">
                <h2>Comparador de familias</h2>
            </div>
            <div class="row">
                <div class="col-md-12 mt-5">
                    <div class="table-responsive">
                        <table id="indextable1" class="table table-striped table-dark table-hover customAlign tablesorter">
                            <thead>
                                <tr scope="row">
                                    <th scope="col">Código</a></th>
                                    <th scope="col">Nombre</a></th>
                                    <th scope="col">Referencia</a></th>
                                    <th scope="col">Precio PCBox</a></th>
                                    <th scope="col">Precio PCComponentes</a></th>
                                    <th scope="col">Diferencia</a></th>
                                    <th scope="col">Porcentaje</a></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr scope="row">
                                        <td>{{ $product->codigo }}</td>
                                        <td><a target="_blank" class="customLinks" href="{{ $product->enlace }}">{{ strtoupper($product->nombre) }}</a></td>
                                        <td><a target="_blank" class="customLinks" href="{{ $product->enlacePccomp }}">{{ $product->referencia_fabricante}}</a></td>
                                        <td>{{ $product->precio != null ? $product->precio : _("Consultar") }}</td>
                                        <td>{{ $product->precioPccomp }}</td>
                                        
                                        <td>{{ $product->difference != null ? $product->difference : _(" --- ")}}</td>
                                        
                                        <td>{{ $product->percentage != null ? "$product->percentage %"  :  _(" --- ")}}</td>
                                    </tr>
                                @empty
                                    <tr scope="row">
                                        <td class="text-center" colspan="7">{{ _("No se produjeron resultados de búsqueda con los filtros seleccionados") }}</td>
                                    </tr>
                                @endforelse
                </tbody>
                <tbody class="avoid-sort">
                                <tr scope="row">
                                    <td></td>
                                    <td></td>
                                    <td>{{ _("----------") }}</td>
                                    <td>{{ _("----------") }}</td>
                                    <td>{{ _("----------") }}</td>
                                    <td>{{ _("----------") }}</td>
                                    <td>{{ _("----------") }}</td>
                                </tr>
                                <tr scope="row">
                                    <td></td>
                                    <td></td>
                                    <td>{{ _("Totales") }}</td>
                                    <td>{{ $totalPCB != 0 ? $totalPCB : _(" Sin importe ") }}</td>
                                    <td>{{ $totalPCC != 0 ? $totalPCC : _(" Sin importe ") }}</td>
                                    <td>{{ $totalDifference != null ? $totalDifference : _(" ---- ")}}</td>
                                    <td>{{ $totalPercentage != null ? "$totalPercentage %" : _(" ---- ") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 offset-md-3 mb-5 mt-2">
                <a class="btn btn-outline-primary btn-block" href="{{ route('home') }}">Atrás</a>
            </div>
            
        </div>
    </div>
    
</div>
        
    </div>
    <!-- jQuery -->
    <!-- <script src="//code.jquery.com/jquery.js"></script> -->
    <!-- DataTables -->
    <!-- <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> -->

    <!-- App scripts -->
    <script>
    $(function() {
  $("#indextable1").tablesorter({ 
      sortList: [[0,0], [1,0]],
      cssInfoBlock : "avoid-sort",  
    });
});
</script>
</body>
</html>
