@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12" style="margin-bottom:20px;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">hi</h5>
                    @include('layouts.listerrores')
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <p class="card-text">
                                {{ $publication->content}}
                            </p>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <cite>Escrito por: {{ $publication->user->name }}</cite>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Agrega tu comentario</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">hi</li>
                    </ul>
                </div>
            </div>

            {{-- @if($userComment )

            @endif --}}

            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ route('comments.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
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
                                    Comentar
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
