

<div class="container-fluid">
    <div class="row">
        <div id="contentFadeIn" class="col-md-12 mt-5">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h2>{{ __('Generador de presupuesto alternativo') }}</h2>
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
                    <form id="alternativeChoicesForm" action="{{ route('alternatives') }}" method="post">
                        @csrf
                        <div class="table-responsive">
                            <table id="indextable1" class="table table-striped table-dark table-hover customAlign">
                                <thead>
                                    <tr scope="row">
                                        <!-- <th scope="col">#</th> -->
                                        <th scope="col">Código</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Precio PCBox</th>
                                        <th scope="col">Precio PCComp</th>
                                        <th scope="col">Alternativas</th>
                                    </tr>
                                </thead>
                                <tbody class="searchTable">
                                    @forelse($oldProducts as $index => $product)
                                        
                                        <tr scope="row">
                                            <!-- <td>{{ $index + 1}}</td> -->
                                            <td>{{ $product['codigo'] }}</td>
                                            <td><a target="_blank" href="{{ $product['enlace'] }}">{{ strtoupper($product['nombre']) }}</a></td>
                                            <td>{{ $product['precio'] }}</td>
                                            <td>{{ $product['precioPccomp'] }}</td>
                                            <td>
                                                <select name="choices[]" class="choices">
                                                    @if($alternatives[$index] != null)
                                                        @foreach($alternatives[$index] as $a)
                                                            <option value="{{ $a['codigo'] }}">{{ $a['codigo'] }} - {{ strtoupper($a['nombre']) }} - {{ $a['precio'] }} €</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{ null }}">{{ __('No se encontraron alternativas para este producto') }}</option>
                                                    @endif
                                                </select>
                                            </td>
                                        </tr>
                                        
                                    @empty
                                        <tr scope="row">
                                            <td class="text-center" colspan="5">{{ _("No se produjeron resultados de búsqueda con los filtros seleccionados") }}</td>
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
                                    </tr>
                                    <tr scope="row">
                                        <td></td>
                                        <td class="text-center">{{ _("Totales") }}</td>
                                        <td>{{ $total }}</td>
                                        <td>{{ $totalPCC }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-5 mt-2">
                    <div class="col-md-3">
                        <a href="#" id="arrowUp" class="float-left mt-2" style="display:none;" title="Go top"><i class="fas fa-chevron-up"></i>{{ __(' Ir arriba') }}</a>
                    </div>
                    <div class="row noMargin">
                        <div class="col-md-6 offset-md-3 text-center mb-2">
                            <button type="submit" form="alternativeChoicesForm" class="btn btn-outline-dark btn-block">Procesar elecciones</button>
                        </div>
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

