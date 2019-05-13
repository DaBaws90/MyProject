@extends('layouts.app')

@section('title', 'PC Box - Deshabilitar usuarios')

@section('content')

<div class="container">
    <div class="row">
        <div id="contentFadeIn" class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-center mt-5 mb-4">
                    <h2>{{ __('Deshabilitar usuarios') }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <div class="alert alert-warning alert-dismissible fade show text-center">
                            {{ __('Atención. Los usuarios seleccionados alternarán su estado de desactivado a activado o viceversa cuando pulse de botón situado en el margen inferior') }}
                        </div>

                        <table id="users-table" class="table table-striped table-hover alignItemsTable">
                            <thead>
                                <tr scope="row">
                                    <th scope="col"></th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Registrado el</th>
                                    <th scope="col">Activo</th>
                                    <!-- <th scope="col">Acciones</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <form id="disableForm" action="{{ route('disable') }}" method="POST">
                                            @csrf
                                            <input type="checkbox" name="ids[]" value="{{ $user->id }}">
                                        </form>
                                    </td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->active ? 'Sí' : 'No' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">{{ __('No hay usuarios registrados en el sistema en estos momentos') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <button class="btn btn-block btn-outline-primary onConfirm disable" type="submit" form="disableForm">Deshabilitar / habillitar</button>
                        <button class="btn btn-block btn-outline-primary onConfirm verify" type="submit" form="disableForm">Verificar emails</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@if( session('errors') || session('message'))

@include('partials.modal')
<script>
    $(document).ready(() => {
        var btn = $('#openModal').click();
        btn.hide();
    })
</script>

@endif
@endpush