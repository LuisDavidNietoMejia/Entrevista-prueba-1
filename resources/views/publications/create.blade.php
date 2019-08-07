@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header text-center">Crear Nueva Publicacion
                    <div class="btn btn-primary">
                        <a style="color:white;" href="{{ route('publications.index') }}">Atras</a>
                    </div>
                    @include('layouts.listerrores')
                </div>
                <div class="panel-body">

                    <form class="form-horizontal" method="POST" action="{{ route('publications.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-4  control-label"><i style="color:red;"
                                    class="fa fa-asterisk" aria-hidden="true"></i> title</label>
                            <div class="col-md-12 col-xs-12">
                                <input id="title" type="text" class="form-control" name="title"
                                    value="{{ old('title') }}" required autofocus>
                                @if ($errors->has('title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                            <label for="content" class="col-md-4 control-label"><i style="color:red;"
                                    class="fa fa-asterisk" aria-hidden="true"></i> content</label>
                            <div class="col-md-12 col-xs-12">
                                <input id="content" type="text" class="form-control" name="content"
                                    value="{{ old('content') }}" required autofocus>
                                @if ($errors->has('content'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('content') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <div class="col-md-12 col-xs-12">
                                <button id="mostrar" type="submit" class="btn btn-primary">
                                    Registrar Publicacion
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
