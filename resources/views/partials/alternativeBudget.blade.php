<form id="budgetForm" action="{{ route('alternativeBudget') }}" method="post">
    @csrf
    <!-- Modal -->
    <div class="modal fade" id="budgetModal" tabindex="-1" role="dialog" aria-labelledby="budgetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title text-center" id="budgetModalLabel">Presupuesto alternativo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <!-- Radio comparison -->
                        <div class="col-md-6">
                            <p>Mostrar artículos cuyo precio:</p>

                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input class="customRadioAlign float-left" type="radio" name="comparison" id="comparativa-menor" value="lesser">
                                            <label class="float-left" for="comparativa-menor"> PC Box <= Web</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input class="customRadioAlign float-left" type="radio" name="comparison" id="comparativa-mayor" value="greater">
                                            <label class="float-left" for="comparativa-mayor"> PC Box > Web</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input class="customRadioAlign float-left" type="radio" name="comparison" id="comparativa-all" value="all" checked>
                                            <label class="float-left" for="comparativa-all"> Todos</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Percentage -->
                        <div class="col-md-6">
                            <p>Mostrar articulos con diferencia de margen maximo de:</p>
                            <div class="row">
                                <div class="col-md-10 offset-md-1">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="porcentaje">Porcentaje:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="wideInput" min="0" max="100" step="0.1" lang="es" id="porcentaje" name="percentage" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- <div class="row mt-3">
                        <div class="col-md-10 offset-md-1 text-center">
                            <p>Mostrar articulos que contengan el/los término(s):</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <input class="wideInput" type="text" name="keyword" placeholder="Palabra(s) clave...">
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!-- Buttons -->
                <div class="modal-footer">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 pr-2">
                                <button type="button" class="btn btn-outline-secondary float-right" data-dismiss="modal"><i class="far fa-times-circle"></i> Cancel</button>
                            </div>
                            <div class="col-md-3 pl-0 pr-0">
                                <button type="submit" class="btn btn-outline-primary btn-block"><i class="fas fa-search"></i> Generate</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>