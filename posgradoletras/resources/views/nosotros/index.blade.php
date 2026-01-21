@extends('layouts.public')

@section('title', 'Nosotros - Posgrado Letras UNMSM')

@push('styles')
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Nosotros" label="Unidad de Posgrado"
        subtitle="Conoce la misión, visión y valores que guían nuestra institución"
        image="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=70&fm=pjpg" />

    <section class="container mx-auto px-6 py-16">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16 overflow-hidden fade-in">
            <!-- Left Column: Misión + Visión -->
            <div class="flex flex-col gap-10" data-aos="fade-right">

                <!-- Misión -->
                <div>
                    <h3
                        class="flex items-center w-full text-2xl font-bold text-unmsm-guinda mb-6 font-serif uppercase tracking-wider">
                        <span class="flex-grow h-px bg-gray-300 mr-4"></span>
                        MISIÓN
                        <span class="flex-grow h-px bg-gray-300 ml-4"></span>
                    </h3>
                    <div class="text-gray-700 text-lg leading-relaxed text-justify">
                        {{ $mision }}
                    </div>
                </div>

                <!-- Visión -->
                <div>
                    <h3
                        class="flex items-center w-full text-2xl font-bold text-unmsm-guinda mb-6 font-serif uppercase tracking-wider">
                        <span class="flex-grow h-px bg-gray-300 mr-4"></span>
                        VISIÓN
                        <span class="flex-grow h-px bg-gray-300 ml-4"></span>
                    </h3>
                    <div class="text-gray-700 text-lg leading-relaxed text-justify">
                        {{ $vision }}
                    </div>
                </div>
            </div>

            <!-- Right Column: Valores -->
            <div class="flex flex-col" data-aos="fade-left" data-aos-delay="200">
                <div>
                    <h3
                        class="flex items-center w-full text-2xl font-bold text-unmsm-guinda mb-6 font-serif uppercase tracking-wider">
                        <span class="flex-grow h-px bg-gray-300 mr-4"></span>
                        VALORES
                        <span class="flex-grow h-px bg-gray-300 ml-4"></span>
                    </h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 pl-4 text-lg">
                        @foreach($valores as $valor)
                            <li>{{ $valor }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Autoridades Section -->
        <div class="mt-16 overflow-hidden" data-aos="fade-up">
            <h3
                class="font-bold text-2xl text-unmsm-guinda mb-8 font-serif border-b-2 border-unmsm-dorado/30 pb-2 inline-block">
                Autoridades
            </h3>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($autoridades as $autoridad)
                    <div class="bg-white p-6 rounded-lg border-l-4 border-unmsm-dorado shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden"
                        data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                        <h4 class="font-bold text-unmsm-guinda text-lg mb-2">{{ $autoridad['nombre'] }}</h4>
                        <p class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wide">{{ $autoridad['cargo'] }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600 min-w-0">
                            <i class="fas fa-envelope text-unmsm-dorado flex-shrink-0"></i>
                            <span class="truncate">{{ $autoridad['email'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </section>
@endsection