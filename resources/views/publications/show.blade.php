@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12" style="margin-bottom:20px;">

            <div class="card">
                <div class="card-header">
                    <h3>{{ $publication->title }}</h3>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <p>{{ $publication->content}}</p>
                        <footer class="blockquote-footer">Escrito por el famoso autor <cite title="Source Title">
                                {{$publication->user->name }}</cite></footer>
                    </blockquote>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Comentarios acerca de esta publicacion</h5>
                    <ul class="list-group list-group-flush">
                        @foreach ($comments as $item)
                        <li class="list-group-item">
                                <blockquote class="blockquote mb-0">
                                    <p>{{ $item->content}}</p>
                                    <footer class="blockquote-footer">Comentando por <cite title="Source Title">
                                            {{$item->user->name }}</cite></footer>
                                </blockquote>
                            </li>
                        @endforeach            
                    </ul>
                </div>
            </div>
          
            @if($countUser == 0)
            <div class="card">
                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('comments.store') }}">
                            {{ csrf_field() }}
    
                            <input id="publication" type="hidden" class="form-control" name="publication"
                                value="{{ $publication->id }}" required autofocus>
    
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
                          
            @else
            
            <div class="card">
                    <div class="card-header">
                        <h4 class="text-center text-danger">Ya no puedes comentar mas!! <a href="{{ route('publications.index') }}">Regresar</a></h4>                        
                    </div>                    
                </div>
            
            @endif    
            

      
        </div>
    </div>
</div>

@endsection