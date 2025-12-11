@extends('layouts.public')

@section('title', $profesor['nombre'])

@section('content')
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 2rem;">
            <div
                style="width: 150px; height: 150px; background: linear-gradient(135deg, #680D10 0%, #B6A350 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: bold; flex-shrink: 0;">
                {{ strtoupper(substr($profesor['nombre'], 0, 1)) }}
            </div>
            <div>
                <h1>{{ $profesor['nombre'] }}</h1>
                <p style="font-size: 1.2rem; color: #B6A350; font-weight: 500;">{{ $profesor['grado'] }}</p>
                <p><a href="mailto:{{ $profesor['email'] }}" style="color: #680D10;">{{ $profesor['email'] }}</a></p>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="color: #680D10; margin-bottom: 1rem;">Biografía</h2>
        <p style="text-align: justify; line-height: 1.8;">{{ $profesor['biografia'] }}</p>
    </div>

    @if(isset($profesor['lineas_investigacion']) && count($profesor['lineas_investigacion']) > 0)
        <div class="card">
            <h2 style="color: #680D10; margin-bottom: 1rem;">Líneas de Investigación</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach($profesor['lineas_investigacion'] as $linea)
                    <span class="badge"
                        style="background: #e3f2fd; color: #1976d2; padding: 0.5rem 1rem; font-size: 1rem;">{{ $linea }}</span>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($profesor['programas']) && count($profesor['programas']) > 0)
        <div class="card">
            <h2 style="color: #680D10; margin-bottom: 1rem;">Programas en los que Participa</h2>
            <div style="display: grid; gap: 1rem;">
                @foreach($profesor['programas'] as $programaSlug)
                    @php
                        $programa = \App\Helpers\ProgramaHelper::getProgramaBySlug($programaSlug);
                    @endphp
                    @if($programa)
                        <div style="background: #f9f9f9; padding: 1rem; border-radius: 8px; border-left: 4px solid #B6A350;">
                            <h3 style="margin-bottom: 0.5rem;">{{ $programa['titulo'] }}</h3>
                            <a href="{{ route('programas.show', $programaSlug) }}" style="color: #680D10;">Ver programa ↁE/a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="card">
        <h2 style="color: #680D10; margin-bottom: 1rem;">Enlaces Externos</h2>
        <div style="display: flex; gap: 1rem;">
            @if(isset($profesor['orcid']) && $profesor['orcid'])
                <a href="{{ $profesor['orcid'] }}" target="_blank" class="btn">Ver ORCID</a>
            @endif
            @if(isset($profesor['cti_vitae']) && $profesor['cti_vitae'])
                <a href="{{ $profesor['cti_vitae'] }}" target="_blank" class="btn">Ver CTI Vitae</a>
            @endif
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('institucional.profesores') }}" class="btn">ↁEVolver a profesores</a>
    </div>
@endsection
