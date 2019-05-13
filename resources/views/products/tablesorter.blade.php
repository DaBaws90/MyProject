<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    

    <!-- choose a theme file -->
<link rel="stylesheet" href="{{ asset('tableorder-master/css/theme.bootstrap_4.css') }}">
<!-- load jQuery and tablesorter scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 

<script type="text/javascript" src="{{ asset('tablesorter-master/js/jquery.tablesorter.js') }}"></script>
</head>
<body>
    <div class="container">
    <table id="indextable1" class="table table-striped table-dark table-hover customAlign tablesorter">
            <thead>
                <tr scope="row">
                    <th id="column1" scope="col"><a href="javascript:SortTable(0,'T');">Código</a></th>
                    <th id="column2" scope="col"><a href="javascript:SortTable(1,'T');">Nombre</a></th>
                    <th scope="col"><a href="javascript:SortTable(2,'T');">Referencia</a></th>
                    <th scope="col"><a href="javascript:SortTable(3,'N');">Precio PCBox</a></th>
                    <th id="column3" scope="col"><a href="javascript:SortTable(4,'N');">Precio PCComponentes</a></th>
                    <th scope="col"><a href="javascript:SortTable(5,'N');">Diferencia</a></th>
                    <th scope="col"><a href="javascript:SortTable(6,'N');">Porcentaje</a></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="child" scope="row">
                        <td>{{ $product->codigo }}</td>
                        <td><a target="_blank" class="customLinks" href="{{ $product->enlace }}">{{ strtoupper($product->nombre) }}</a></td>
                        <td><a target="_blank" class="customLinks" href="{{ $product->enlacePccomp }}">{{ $product->referencia_fabricante}}</a></td>
                        <td>{{ $product->precio != null ? $product->precio : _("Consultar") }}</td>
                        <td>{{ $product->precioPccomp }}</td>
                        
                        <td>{{ $product->difference != null ? $product->difference : _(" --- ")}}</td>
                        
                        <td>{{ $product->percentage != null ? "$product->percentage %"  :  _(" --- ")}}</td>
                    </tr>
                @empty
                    <tr class="parent" scope="row">
                        <td class="text-center" colspan="7">{{ _("No se produjeron resultados de búsqueda con los filtros seleccionados") }}</td>
                    </tr>
                @endforelse
                </tbody>
                <tbody class="avoid-sort">
                <tr class="parent" scope="row">
                    <td></td>
                    <td></td>
                    <td>{{ _("----------") }}</td>
                    <td>{{ _("----------") }}</td>
                    <td>{{ _("----------") }}</td>
                    <td>{{ _("----------") }}</td>
                    <td>{{ _("----------") }}</td>
                </tr>
                <tr class="parent" scope="row">
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
<script>
    $(function() {
  $("#indextable1").tablesorter({ 
    //   sortList: [[0,0], [1,0]],
      cssInfoBlock : "avoid-sort",  
    });
});
</script>
</body>
</html>