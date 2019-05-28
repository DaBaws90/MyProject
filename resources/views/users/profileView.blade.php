@extends('layouts.app')

@section('title', 'PC Box - Perfil del usuario')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="text-center mt-5">
                <h2>{{ __('Bienvenido a su perfil, :user', ['user' => $user->name]) }}</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="text-center mt-5">
                <p>Aquí podrá revisar su historial de presupuestos o editar algunos detalles personales como su teléfono, dirección, etc.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row mt-5">
                <!-- Card 1 of 3 -->
                <div class="col-12 col-md-4 col-lg-4 mb-3 mb-md-3">
                    <div class="custom card">
                        <div class="custom card-top">
                            <img src="{{ asset('imgs/historial.jpg') }}" alt="Uploads history image" class="custom card-img-top">
                        </div>
                        <div class="custom card-body text-center">
                            <a href="#history"><h2 class="card-title">Historial de presupuestos</h2></a>
                            <p class="card-text text-muted">Eche un vistazo a los presupuestos subidos con anterioridad, renómbrelos o descargue de nuevo los pdfs. Tambíen podrá eliminarlos si así lo desea.</p>
                            <!-- <a href="#" class="btn btn-outline-danger btn-sm">Continúe leyendo</a> -->
                        </div>
                    </div>
                </div>
                <!-- Card 2 of 3 -->
                <div class="col-12 col-md-4 col-lg-4 mb-3 mb-md-3">
                    <div class="custom card">
                        <div class="custom card-top">
                            <img class="custom card-img-top" src="{{ asset('imgs/edit-profile.jpg') }}" alt="Edit profile image">
                        </div>
                        <div class="custom card-body text-center">
                            <a href="{{ route('editProfileView') }}"><h2 class="card-title">Edite sus datos</h2></a>
                            <p class="card-text text-muted">Edite su información personal y los detalles de contacto, tales como el email o su nombre.</p>
                        </div>
                    </div>
                </div>
                <!-- Card 3 of 3 -->
                <div class="col-12 col-md-4 col-lg-4 mb-3 mb-md-3">
                    <div class="custom card">
                        <div class="custom card-top">
                            <img class="custom card-img-top" src="{{ asset('imgs/chart.jpeg') }}" alt="Chart image">
                        </div>
                        <div class="custom card-body text-center">
                            <a href="{{ route('home') }}" class="card-title"><h2 class="card-title">Comparador</h2><a>
                            <p class="card-text text-muted">Acceda al comparador de presupuestos para realizar una comparación con diversos filtros personalizados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center mt-5" id="history">
                    <h3>Historial de presupuestos</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center mt-4">
                    <p>Consulte todos sus presupuestos comparados hasta la fecha y edite o elimínelos si así lo desea. También podrá descargarlos o visualizarlos en el navegador. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped centerAlign mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre original</th>
                                <th scope="col">Alias del fichero</th>
                                <th scope="col">Subido el</th>
                                <th scope="col">Descargar</th>
                                <th scope="col">Comparar</th>
                                <th scope="col">Borrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->uploads as $index => $file)
                            <tr class="mt-5">
                                <td class="mt-5" scope="row">{{ $index + 1 }}</td>
                                <td> <a target="_blank" href="{{ route('download', ['id' => $file->id, 'browser' => true]) }}">{{ $file->filename }}  <i class="fas fa-external-link-alt"></i></a></td>
                                <td>
                                    <form class="editAliasForm" style="display:none;" action="{{ route('uploads.update', $file->id) }}" method="POST">
                                        @csrf
                                        <input name="_method" type="hidden" value="PATCH"/>
                                        <input name="id" type="hidden" value="{{ $file->id }}"/>
                                        <div class="form-group row">
                                            <div class="col-md-11">
                                                <input class="form-control" type="text" name="alias" placeholder="Last value: {{ $file->alias ? $file->alias : __('Sin alias') }}">
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-link btn-light customBtn onConfirm save" type="submit"> <i class="far fa-save"></i></button>  
                                            </div>
                                        </div>
                                    </form>
                                    <p class="hideable">
                                        <span>{{ $file->alias ? $file->alias : '(Sin alias)' }} </span>
                                        <button id="showEditForm" class="btn btn-link btn-light btn-sm customBtn showEditForm"> <i class="fas fa-pencil-alt"></i></button> 
                                    </p> 
                                </td>
                                <td>{{ $file->created_at }}</td>
                                <td><a class="btn btn-link btn-light btn-sm customBtn" href="{{ route('download', ['id' => $file->id]) }}"><i class="fas fa-download"></i></a></td>
                                <td>
                                    <form action="{{ route('upload') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $file->id }}">
                                        <button class="btn btn-link btn-light btn-sm customBtn" type="submit">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('uploads.destroy', $file->id) }}" method="POST">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="btn btn-link btn-light btn-sm customBtn onConfirm delete" type="submit">
                                            <i style="color:red;" class="far fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="6">{{ __('No se encontraron presupuestos en el historial en estos momentos. Puede que los haya eliminado todos.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div id="dialog">

    </div>

</div>

@endsection

@push('scripts')

@if(session('errors') || session('success') || session('message'))
    <!-- Includes modal set up for both updated or errors messages -->
    @include('partials.modal')
    <!-- Script to be loaded if there were errors -->
    <script type="text/javascript">
        $(document).ready(function() {
            var btn = $('#openModal').click();
            btn.hide();
        });
    </script>
@endif


<script src="{{ asset('js/aliasUpdateAjaxSubmit.js') }}"></script>

@endpush