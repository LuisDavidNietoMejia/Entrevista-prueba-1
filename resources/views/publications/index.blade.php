@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">Publicaciones Recientes
                    <div class="btn btn-primary">
                        <a style="color:white;" href="{{ route('publications.create') }}">Crear Nuevo Comentario</a>
                    </div>
                    @include('layouts.mensajes')
                </div>
                <div class="panel-body">
                    <div class='table-responsive'>
                        <table class='table table-striped table-bordered table-hover table-condensed'>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Ver Publicacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($publications as $publication)
                                <tr>
                                    <td>{{ $publication->id }}</td>
                                    <td>{{ $publication->title}}</td>
                                    <td>{{ $publication->content}}</td>
                                    {{-- <td><a href="{{ route('publications.show',$publication->id) }}"><i
                                        class="far fa-eye fa-2x text-center"></i></a></td> --}}
                                    <td><a href="{{ route('publications.show',$publication->id) }}">Ver</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
