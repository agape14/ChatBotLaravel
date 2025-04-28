<!-- resources/views/chatbot/dashboard.blade.php -->

@extends('layouts.app') <!-- Asegúrate de tener un layout base -->

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Mensajes del Chatbot - Emilima</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th> <!-- Contador -->
                <th>Número de Teléfono</th>
                <th>Nombre</th>
                <th>Última Interacción</th>
                <th>Total Mensajes</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalMensajes = 0;
                $contador = 1;
            @endphp

            @foreach ($usuarios as $usuario)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <a href="{{ route('chatbot.usuario.mensajes', $usuario->id) }}">
                        {{ $usuario->numero_telefono }}
                    </a>
                </td>
                <td>{{ $usuario->nombre ?? 'No registrado' }}</td>
                <td>{{ \Carbon\Carbon::parse($usuario->ultima_interaccion)->format('d/m/Y H:i') }}</td>
                <td>{{ $usuario->mensajes_count }}</td>
            </tr>
            @php
                $totalMensajes += $usuario->mensajes_count;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Mensajes:</th>
                <th>{{ $totalMensajes }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
