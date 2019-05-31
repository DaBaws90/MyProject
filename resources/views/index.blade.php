@extends('layouts.app')

@section('title', 'PC Box - Comparador')

@section('content')

<div class="container-fluid">
    <div class="row mb-5">
        <div class="col-md-12 text-center noPadding">
            <h2 class="blueBackgroundTitle mb-3">Buscador de referencias</h2>
            <div class="row">
                <div class="col-md-12">
                    <p>Introduce las referencias a buscar separadas por comas o seleccione un archivo PDF</p>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <form action="{{ route('products.results.refSearch') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label" for="searchbox">Referencias:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="searchbox" name="references" value="{{ old('references') }}">
                                    </div>
                                    <div class="col-md-1">
                                         <button title="Add a PDF file" data-toggle="modal" data-target="#myModal" type="button" class="btn btn-link"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>

                                <div class="col-md-4 offset-md-4">
                                    <button type="submit" class="mt-4 mb-3 btn btn-outline-primary btn-block"><i class="fas fa-search"></i> Buscar</button>
                                </div>
                            </form>
                            @if ($errors->has('file') || $errors->has('limit'))
                                <div id="modalError" class="text-center alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>{{ $errors->first('file') ? $errors->first('file') : $errors->first('limit') }}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <form id="pdfForm" action="{{ route('upload', ['newFile' => true]) }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title text-center" id="modalLabel">Add a PDF</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body form-group text-center">
                                        <label for="inputFile">Seleccione un fichero PDF para extraer las referencias de manera automática</label>
                                        <input class="form-control-file text-center" id="inputFile" name="file" type="file" accept=".pdf" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-primary btn-block">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="form" class="pt-5" action="{{ route('products.results.catSearch') }}" method="POST">
        @csrf
        <div class="row text-center">
            <div class="col-md-12 noPadding">
                <h2 class="blueBackgroundTitle mb-3">Buscador por familia</h2>
                <div class="row">
                   <div class="col-md-3">
                        <p>Selecciona familia (para todas dejar tal cual):</p>    
                        <select style="min-width:65%" name="category">
                            <option value="0" selected>Seleccione...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id_familia }}">{{ $category->familia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <p>Mostrar articulos cuyo precio:</p>

                        <div class="row">
                            <div class="col-md-12">
                                <input class="customRadio" type="radio" id="comparativa-menor" name="comparison" value="lesser">
                                <label for="comparativa-menor">PC Box <= Web</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input class="customRadio" type="radio" id="comparativa-mayor" name="comparison" value="greater">
                                <label for="comparativa-mayor">PC Box > Web</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input class="customRadio" type="radio" id="comparativa-todos" name="comparison" value="all" checked>
                                <label for="comparativa-todos">Todos</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-md-4">
                        <p>Mostrar articulos con diferencia de margen maximo de:</p>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="porcentaje">Porcentaje:</label>
                                    <div class="col-md-9">
                                        <input type="number" class="wideInput" min="0.1" max="100" step="0.1" lang="es" id="porcentaje" name="percentage">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <p>Mostrar articulos que contengan el termino:</p>
                        <div class="row">
                            <div id="myDiv" class="col-md-10 offset-md-1">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input id="kWord" class="wideInput mb-1" placeholder="Término(s)" type="text" name="keyword">
                                        <small id="smallInfo" style="display:none;" aria-describedby="kWord">Please, use the first search input instead</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="addBtn" type="button" class="btn btn-link"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <input style="display:none;" id="kWord1" class="wideInput" placeholder="O el término" type="text" name="keyword1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="top5 row">
                    <div class="col-md-4 offset-md-4">
                        <button type="submit" class="mb-3 btn btn-outline-primary btn-block"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection

@push('scripts')

<script>
$(document).ready(function(){

    var btn = $('#addBtn');
    var input = $("#kWord");
    var input2 = $("#kWord1");
    var small = $("#smallInfo");

    // Clears inputs at page's refresh
    input.val('');
    input2.val('');

    input.on('blur', () => {
        if (input.val() == '' || input.val().length == 0) {
            
            if(input2.val() != "" || input2.val().length > 0){
                input2.attr('disabled', true);
                small.show();
            }
        }
        else {
            input2.removeAttr('disabled');
            small.hide();
        }
    })

    input2.on('blur', () => {
        if (input.val() == '' && input2.val() != '') {
            input2.attr('disabled', true);
            small.show();
        }
    })

    btn.on('click', () => {
        input2.show();
        btn.hide();
    })

});
</script>

@endpush