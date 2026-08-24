@extends('layouts.public')

@section('title', 'Plana Docente')

@section('content')
    <div class="page-header">
        <h1>Plana Docente</h1>
        <p>Conoce a nuestros profesores e investigadores</p>
    </div>

    @if(count($profesores) > 0)
        <div class="grid">
            @foreach($profesores as $profesor)
                <div class="card">
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div
                            style="width: 100px; height: 100px; background: linear-gradient(135deg, #103050 0%, #B6A350 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold;">
                            {{ strtoupper(substr($profesor['nombre'], 0, 1)) }}
                        </div>
                        <h3 style="margin-bottom: 0.25rem;">{{ $profesor['nombre'] }}</h3>
                        <p style="color: #B6A350; font-weight: 500;">{{ $profesor['grado'] }}</p>
                    </div>

                    <p style="color: #666; margin-bottom: 1rem; text-align: justify;">
                        {{ Str::limit($profesor['biografia'], 200) }}
                    </p>

                    <div style="margin-bottom: 1rem;">
                        <p><strong>Email:</strong> <a href="mailto:{{ $profesor['email'] }}"
                                style="color: #103050;">{{ $profesor['email'] }}</a></p>

                        @if(isset($profesor['lineas_investigacion']) && count($profesor['lineas_investigacion']) > 0)
                            <p style="margin-top: 0.5rem;"><strong>Líneas de investigación:</strong></p>
                            <div style="margin-top: 0.5rem;">
                                @foreach($profesor['lineas_investigacion'] as $linea)
                                    <span class="badge"
                                        style="background: #e3f2fd; color: #1976d2; margin-top: 0.25rem;">{{ $linea }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                        @if(isset($profesor['orcid']) && $profesor['orcid'])
                            <a href="{{ $profesor['orcid'] }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; color: #103050;">ORCID ↁE/a>
                        @endif
                        @if(isset($profesor['cti_vitae']) && $profesor['cti_vitae'])
                            <a href="{{ $profesor['cti_vitae'] }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; color: #103050;">CTI Vitae
                                ↁE/a>
                        @endif
                    </div>

                    <a href="{{ route('institucional.profesor', $profesor['id']) }}" class="btn"
                        style="margin-top: 1rem; width: 100%; text-align: center;">Ver perfil completo</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <p>No hay información de profesores disponible.</p>
        </div>
    @endif
@endsection
