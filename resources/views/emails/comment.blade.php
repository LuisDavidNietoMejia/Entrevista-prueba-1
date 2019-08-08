<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Nuevo Comentario</title>
</head>
<body>
<p>Hola! Han comentando sobre tu publicacion....{{ $publication->title}}</p>
    <p>El usuario:</p>
    <ul>
        <li>{{ $userComment->name }}</li>
        <li>Comentario: {{ $comment }}</li>
    </ul>   
</body>
</html>