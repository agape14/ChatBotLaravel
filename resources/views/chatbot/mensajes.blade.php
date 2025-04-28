@extends('layouts.app')

@section('content')
    <h1>Mensajes de {{ $usuario->numero_telefono }}</h1>

    <a href="{{ route('chatbot.dashboard') }}" class="btn btn-primary mb-3">⬅️ Volver al Dashboard</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Mensaje</th>
                <th>Tipo</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mensajes as $message)
                <tr>
                    <td>{{ $message->contenido }}</td>
                    <td>{{ $message->tipo_mensaje }}</td>
                    <td>{{ $message->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
