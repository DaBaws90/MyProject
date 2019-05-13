@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 mt-5 mb-4">
            <div class="text-center">
                <h2>{{ __(':header', ['header' => $header]) }}</h2>
            </div>
        </div>
    </div>

    

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    {{ __(':cardHeader', ['cardHeader' => $cardHeader]) }}
                </div>
                
                <div class="card-body col-md-8 offset-md-2">
                    <form action="{!! $route !!}" method="POST">
                        @csrf
                        @if(session('pathInput'))
                            {{ $patchInput }}
                        @endif
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label">Nombre</label>
                            <div class="col-md-10">
                                <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" value="{{ old('name', $user->name) }}">
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label">Email</label>
                            <div class="col-md-10">
                                <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" value="{{ old('email', $user->email) }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 text-center mt-3">
                                <a class="backURL float-left mt-2" href="#"><i class="fas fa-chevron-left"></i>{{ __(' Atrás') }}</a>
                                <button type="submit" class="btn btn-outline-primary mr-5 onConfirm save">{{ __('Guardar cambios') }} <i class="far fa-save"></i></button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

    @if( session('message'))

        @include('partials.modal')

        <script>
            $(document).ready(() => {
                var btn = $('#openModal').click();
                btn.hide();
            })
        </script>

    @endif

@endpush