@extends('layouts.app')

@section('title', 'PC Box - Resultados de búsqueda')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div id="contentFadeIn" style="display:none;" class="col-md-12 mt-5">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h2>{{ __('Comparador de :title', ['title' => $title]) }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="#" id="arrowDown" class="float-left" style="display:none;" title="Show totals"><i class="fas fa-chevron-down"></i><span class="hideableText">{{ __(' Ir a totales') }}</span></a>
                            <input style="position:sticky;" id="myInput" class="float-right mb-3" type="text" placeholder="Search...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="indextable1" class="table table-striped table-dark table-hover customAlign">
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
                            <tbody class="searchTable">
                                @forelse($products as $product)
                                    <tr scope="row">
                                        <td>{{ $product->codigo }}</td>
                                        <td><a target="_blank" class="customLinks" href="{{ $product->enlace }}">{{ strtoupper($product->nombre) }}</a></td>
                                        <td><a target="_blank" class="customLinks" href="{{ $product->enlacePccomp }}">{{ $product->referencia_fabricante}}</a></td>
                                        <td>{{ $product->precio != null ? $product->precio : _("Consultar") }}</td>
                                        <td>{{ $product->precioPccomp }}</td>
                                        <td>{{ $product->difference != null ? round($product->difference, 2, PHP_ROUND_HALF_UP) : _(" --- ")}}</td>
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
                                    <td>{{ $totalDifference != null ? round($totalDifference, 2, PHP_ROUND_HALF_UP) : _(" ---- ")}}</td>
                                    <td>{{ $totalPercentage != null ? "$totalPercentage %" : _(" ---- ") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-5 mt-2">
                    <div class="col-md-3">
                        <a href="#" id="arrowUp" class="float-left mt-2" style="display:none;" title="Go top"><i class="fas fa-chevron-up"></i>{{ __(' Ir arriba') }}</a>
                    </div>
                    <div class="col-md-6 offset-md-3 text-center mb-2">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-block btn-outline-dark" data-toggle="modal" data-target="#budgetModal">
                            {{ __('Generar alternativa') }}
                        </button>
                        @include('partials.alternativeBudget')
                    </div>
                    <div class="row noMargin">
                        <div class="col-md-6 offset-md-3 text-center">
                            <a class="btn btn-outline-primary btn-block" href="{{ route('home') }}">Atrás</a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <div id="dialog"></div>
</div>


@endsection

@push('scripts')

<!-- TableSorter Scripts -->
<script type="text/javascript" src="{{ asset('tablesorter-master/js/jquery.tablesorter.js') }}"></script>

<script type="text/javascript">
$(function() {
    $("#indextable1").tablesorter({ 
        // sortList: [4,0],
        cssInfoBlock : "avoid-sort",  
    });
});
</script>

<!-- Scroll Progress Bar Script -->
<script src="{{ asset('js/prognroll.js') }}"></script>

<script>
    $(document).ready(function() {
        $(function() {
            $("body").prognroll();
        });
    });
</script>

<!-- Custom Scripts to handle some events on scrolling -->
<script src="{{ asset('js/goTopBottomScrollingEvents.js') }}"></script>

<!-- Alternative Budget Modal -->
<script>
$(document).ready(() => {

    var btnModalTrigger = $('button[data-target="#budgetModal"]');
    var budgetForm = $('#budgetForm');
    // var tableCells = $('#indextable1 tbody tr td');
    // var tableTotals = $('.avoid-sort tr td');
    var dialog = $('#dialog');

    // Form submit handler
    budgetForm.on('submit', (e) => {
        e.preventDefault();
        
        let formData = budgetForm.serializeArray();

        formData.push({
            name: 'products',
            // value: {!! str_replace("'", "\'", json_encode($products)) !!},
            // value: {!! json_encode($products) !!},
            value: JSON.stringify({!! json_encode($products) !!}),
        })

        let dialogParams = {
            title: '',
            mssg: '',
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: budgetForm.attr('action'),
            type: 'POST',
            data: formData,
            success: function( data, textStatus, jqXHR ) {

                console.log(jqXHR);

                try {
                    dialogParams = setParams( jqXHR );
                } 
                catch (error) {
                    dialogParams = setParams( error )
                }

                dialog.html(dialogParams.mssg);
                
                dialog.dialog({
                    width: 'auto',
                    modal: true,
                    show : { effect: "slide", duration: 1000 },
                    hide: { effect: "drop", duration: 800 },
                    // Do action at dialog's creation
                    open: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    // Do action at dialog's open transition finished 
                    focus: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    buttons: [
                        {  "text": "Cancel",
                            "click": function(){ 
                                $(this).dialog("close");
                                console.log("Cancelled")
                            } 
                        },
                        {  "text": "Ok",
                            "click": function(e) { 
                                $(this).dialog("close"); 
                                console.log("Accepted => Redirecting...")
                                // Calls AUX function for handling another Ajax call
                                btnModalTrigger.click();
                                onSuccess(data.success);
                            } 
                        }
                    ],
                    title: dialogParams.title,
                })
            },
            error: function( jqXHR, textStatus, errorThrown ) {

                console.log(jqXHR);

                try {
                    dialogParams = setParams( jqXHR, errorThrown );
                } 
                catch (error) {
                    dialogParams = setParams( error, errorThrown )
                }

                dialog.html(dialogParams.mssg);

                dialog.dialog({
                    width: 'auto',
                    modal: true,
                    show : { effect: "slide", duration: 1000 },
                    hide: { effect: "drop", duration: 800 },
                    // Do action at dialog's creation
                    open: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    // Do action at dialog's open transition finished 
                    focus: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    buttons: [
                        {  "text": "Ok",
                            "click" : function() { 
                                $(this).dialog("close"); 
                            } 
                        }
                    ],
                    title: dialogParams.title,
                })
            }
        })

    });

    // AUX function (Ajax call / request handler)
    function onSuccess(data) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/products/choices",
            type: 'POST',
            data: { 
                'oldProducts': JSON.stringify(data.oldProducts),
                'alternatives': JSON.stringify(data.alternatives),
                'totals': data.totals
            },
            success: function(response) {
                console.log("onSuccess JS method - success response")
                console.info(response);
                $('.container-fluid').html(response.view);
                $("#indextable1").tablesorter({ 
                    // sortList: [4,0],
                    cssInfoBlock : "avoid-sort",  
                });
                searchBar();
            },
            error: function(response) {
                console.log("onSuccess JS method - error response")
                console.info(response);
            }
        });
    }

    // AUX function
    function setParams( jqXHR, errorThrown = null ) {

        let dialogParams = {
            title: '',
            mssg: '',
        }

        switch(errorsType(jqXHR)) {

            case 'success':
                // console.log("SUCCESS CASE")
                dialogParams.title = 'Success';
                dialogParams.mssg = jqXHR.responseJSON.success.mssg + " (Status code: " + jqXHR.status + ")";
                break;

            case 'percentage':
                // console.log("PERCENTAGE CASE")
                dialogParams.title = 'Validation error';
                dialogParams.mssg = jqXHR.responseJSON.errors.percentage + " (Status code: " + jqXHR.status + ")";
                break;

            case 'products':
                dialogParams.title = 'Validation error';
                dialogParams.mssg = jqXHR.responseJSON.errors.products + " (Status code: " + jqXHR.status + ")";
                break;

            case 'keyword':
                dialogParams.title = 'Validation error';
                dialogParams.mssg = jqXHR.responseJSON.errors.keyword + " (Status code: " + jqXHR.status + ")";
                break;

            // Standard error (besides error validations)
            case 'error':
                // console.log("ERROR CASE")
                dialogParams.title = "Something went wrong (" + errorThrown + ")";
                dialogParams.mssg = jqXHR.responseJSON.errors.mssg + " (Status code: " + jqXHR.status + ")";
                break;

            // Try / catch error or unknow error handler
            default:
                // console.log("DEFAULT CASE")
                dialogParams.title = "Something went wrong (Unknown failure)";
                dialogParams.mssg = "An unknow error occurred. Please, try again later. (Status code: Unknown)";
                break;

        }

        return dialogParams;
    }

    // AUX function
    function errorsType( jqXHR ) {

        if (jqXHR.responseJSON ) {

            if ( jqXHR.responseJSON.errors ) {
                return jqXHR.responseJSON.errors.percentage ? 'percentage' : jqXHR.responseJSON.errors.products ? 'products' : jqXHR.responseJSON.errors.keyword ? 'keyword' : 'error';
            }
            else {
                // If no errors exists, it must be a success
                return 'success';
            }
        }
        // If no responseJSON exists, it must be try / catch error, so default case needs to be triggered
        else {
            return '';
        }
        
    }

});
</script>

<!-- Table's searchbar script -->
<script>
$(document).ready(function(){
    // We only call the function declared OUTSIDE because this way we can (and we'll do) reutilize it
    searchBar();
});
</script>

<script>
// Declared outside anonymus function to reutilize the fucntion
function searchBar(){
    // Event on keyup for the search input
    $('#myInput').on('keyup', function(){
        // Parse to lower case the input text
        var text = $(this).val().toLowerCase();
        // Filter the table tbody's row loking for the text pattern
        $('#indextable1 tbody tr').filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(text) > -1);
        });
    }); 
} 
</script>

@endpush