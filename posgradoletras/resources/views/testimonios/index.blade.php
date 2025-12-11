@extends('layouts.public')

@section('title', 'Testimonios')

@section('content')
    <div class="page-header">
        <h1>Testimonios de Nuestros Egresados</h1>
        <p>Conoce las experiencias de quienes han pasado por nuestros programas</p>
    </div>

    @if(count($testimonios) > 0)
        <div class="grid">
            @foreach($testimonios as $testimonio)
                <div class="card">
                    <div
                        style="background: linear-gradient(135deg, #680D10 0%, #8B1114 100%); color: white; padding: 1rem; border-radius: 8px 8px 0 0; margin: -1.5rem -1.5rem 1rem -1.5rem;">
                        <h3 style="color: white; margin: 0;">{{ $testimonio['nombre'] }}</h3>
                        @if(isset($testimonio['cargo_actual']) && $testimonio['cargo_actual'])
                            <p style="font-size: 0.9rem; margin: 0.25rem 0 0 0; opacity: 0.9;">{{ $testimonio['cargo_actual'] }}</p>
                        @endif
                    </div>

                    <p style="font-style: italic; color: #555; margin-bottom: 1rem; line-height: 1.8;">"{{ $testimonio['texto'] }}"
                    </p>

                    <div
                        style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #B6A350; font-weight: 500;">Promoción {{ $testimonio['promocion'] }}</span>
                        @php
                            $programa = \App\Helpers\ProgramaHelper::getProgramaBySlug($testimonio['programa_slug']);
                        @endphp
                        @if($programa)
                            <a href="{{ route('programas.show', $testimonio['programa_slug']) }}"
                                style="color: #680D10; text-decoration: none; font-size: 0.9rem;">
                                Ver programa ↁE
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <p>No hay testimonios disponibles en este momento.</p>
        </div>
    @endif
@endsection
