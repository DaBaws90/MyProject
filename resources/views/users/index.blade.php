@extends('layouts.app')

@section('title', 'PC Box - Usuarios')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div id="contentFadeIn" class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-center mt-5 mb-5">
                    <h2>{{ __('Listado de usuarios') }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="table-responsive">
    @endsection

        @section('datatables')
                        <!-- <div class="float-left mb-1"> -->
                            <a class="btn btn-outline-primary btn-block mb-3" href="{{ route('register') }}"><i class="fa fa-plus"></i> Añadir usuario</a>
                        <!-- </div> -->
                        <form id="disableForm" action="{{ route('disable') }}" method="post">
                            @csrf
                            <table id="users-table" class="table table-striped table-hover table-bordered" style="width:100%">
                                <thead>
                                    <tr scope="row">
                                        <th scope="col"></th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Registrado el</th>
                                        <th scope="col">Activo</th>
                                        <th scope="col">Verificar</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
        @endsection

    @section('content2')
                    </div>
                    <button class="btn btn-block btn-outline-primary onConfirm disable mt-2 mb-2" type="submit" form="disableForm"><i class="fas fa-sync-alt"></i> {{ __(' Deshabilitar / habillitar') }}</button>
                    <a class="btn btn-block btn btn-outline-primary mb-5" href="{{ route('home') }}"><i class="fas fa-chevron-left"></i>{{ __(' Ir al inicio') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@if( session('errors') || session('success') || session('message'))
    @include('partials.modal')

    <script>
        $(document).ready(() => {
            var btn = $('#openModal').click();
            btn.hide();
        })
    </script>
@endif

<script>
var table = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    columnDefs: [
        { "width": "15%", "targets": 5 },
        { "className": "dt-center", "targets": "_all" }
    ],
    ajax: '{!! route('users.index.datatables') !!}',
    columns: [
        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'created_at', name: 'created_at' },
        { data: 'active', name: 'active',
            "render": function(data, type, row) {
                return (data == true ) ? 'Sí' : 'No';
            }
        },
        { data: 'email_verified_at', name: 'email_verified_at', 
            "render": function(data, type, row) {
                let id = $.parseJSON(JSON.stringify(row.id));
                /* 
                    Mala praxis el inlcuir el event handler inline, pero al añadirlo desde JS, solo podía añadirlo a los elementos que estuviesen cargados en la página actuald e Datatables,
                    al menos con lo que sé de JS en este puntoi, por lo que he tenido que hacerlo de este modo aunque no sea lo correcto.
                */
                let startingTag = `<a onclick='return confirm("Va a verificar el email del siguiente usuario. ¿Desea continuar?")' 
                    href="{{ route('verify', ['id' => 'idVal']) }}" class="btn btn-link verify">`.replace('idVal', id);

                let closingTag = '</a>';

                return (data === null) ? startingTag + '<i class="fas fa-user-slash"></i>' + closingTag : '<i class="fas fa-user-check"></i>';
            }    
        },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    order: [[ 1, 'asc' ]],
    // infoCallback: function( settings, start, end, max, total, pre ) {
    //     if (total == 0) return "No matches found";
    //     return 'Showing '+total+' to '+end+' of '+total+' entries (filtered from '+max+' total entries)';
    // }
});

// $('.verify').on('click', function (e) { 
//     // e.preventDefault();
//     alert("JEJE");
//     // if(confirm("¿Estás seguro de eliminar el registro?")) {
//         $.ajaxSetup({
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             }
//         });
//         // var url = $(this).data('remote');
//         console.info(url);
//         // confirm then
//         $.ajax({
//             url: route('users.disable', true),
//             type: 'POST',
//             dataType: 'json',
//             data: { method: 'POST', submit: true }
//         })
//         .always(function (data) {
//             $('#users-table').DataTable().draw(false);
//         });
//     // }
// });
</script>

@endpush