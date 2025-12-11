@extends('layouts.public')

@section('title', 'Autoridades')

@section('content')
    <div class="page-header">
        <h1>Autoridades</h1>
        <p>Conoce a las autoridades de la Unidad de Posgrado</p>
    </div>

    @if(count($autoridades) > 0)
        <div class="grid">
            @foreach($autoridades as $autoridad)
                <div class="card">
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div
                            style="width: 120px; height: 120px; background: linear-gradient(135deg, #680D10 0%, #B6A350 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: bold;">
                            {{ strtoupper(substr($autoridad['nombre'], 0, 1)) }}
                        </div>
                    </div>

                    <h3 style="color: #680D10; text-align: center; margin-bottom: 0.5rem;">{{ $autoridad['cargo'] }}</h3>
                    <p style="text-align: center; font-size: 1.1rem; font-weight: 500; margin-bottom: 1.5rem;">
                        {{ $autoridad['nombre'] }}</p>

                    <div style="background: #f9f9f9; padding: 1rem; border-radius: 8px;">
                        <p><strong>Email:</strong> <a href="mailto:{{ $autoridad['email'] }}"
                                style="color: #680D10;">{{ $autoridad['email'] }}</a></p>
                        @if(isset($autoridad['telefono']) && $autoridad['telefono'])
                            <p style="margin-top: 0.5rem;"><strong>Teléfono:</strong> {{ $autoridad['telefono'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <p>No hay información de autoridades disponible.</p>
        </div>
    @endif
@endsection
