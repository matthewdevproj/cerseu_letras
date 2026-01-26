@if(isset($testimonios) && $testimonios->count() > 0)
    @php
        $count = $testimonios->count();
        // Necesitamos al menos 9 slides para un loop fluido con 3 visibles
        $minSlides = 9;
        $repetitions = $count > 0 ? max(1, ceil($minSlides / $count)) : 1;
    @endphp
    <section class="py-20 bg-white border-b border-gray-100 overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2
                    class="section-title text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6 inline-block relative">
                    Nuestros Egresados
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">
                    Voces que inspiran y demuestran el impacto de nuestra formación académica.
                </p>
            </div>

            <!-- Swiper Container -->
            <div class="swiper testimonios-swiper px-4 pb-4 mb-12">
                <div class="swiper-wrapper items-stretch">
                    {{-- Repetimos el foreach para generar suficientes slides --}}
                    @for($rep = 0; $rep < $repetitions; $rep++)
                        @foreach($testimonios as $testimonio)
                            <div class="swiper-slide !h-auto">
                                <div
                                    class="bg-gray-50 rounded-2xl p-8 relative hover:shadow-xl transition-all duration-300 group border border-gray-100 h-full flex flex-col">
                                    <!-- Comillas decorativas -->
                                    <div
                                        class="absolute top-6 right-8 text-6xl text-unmsm-guinda/10 font-serif leading-none group-hover:text-unmsm-guinda/20 transition-colors">
                                        &rdquo;
                                    </div>

                                    <!-- Contenido -->
                                    <div class="mb-8 relative z-10 flex-grow">
                                        <p class="text-gray-600 italic leading-relaxed">
                                            "{{ Str::limit($testimonio->contenido, 350) }}"
                                        </p>
                                    </div>

                                    <!-- Autor -->
                                    <div class="flex items-center gap-4 border-t border-gray-200 pt-6">
                                        <div class="flex-shrink-0">
                                            @if($testimonio->photo)
                                                <img src="{{ $testimonio->photo_url }}" alt="{{ $testimonio->nombre }}"
                                                    class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-md">
                                            @else
                                                <div
                                                    class="w-14 h-14 rounded-full bg-unmsm-guinda text-white flex items-center justify-center font-bold text-lg border-2 border-white shadow-md">
                                                    {{ strtoupper(substr($testimonio->nombre, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-bold text-gray-900 group-hover:text-unmsm-guinda transition-colors truncate">
                                                {{ $testimonio->nombre }}
                                            </h4>
                                            @if($testimonio->programa)
                                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">
                                                    {{ $testimonio->programa->titulo_completo }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
            <!-- Pagination fuera del contenedor swiper -->
            <div class="swiper-pagination !relative !bottom-0 mb-8"></div>

            <div class="text-center">
                <a href="{{ route('testimonios.index') }}"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-white border-2 border-unmsm-guinda text-unmsm-guinda font-bold rounded-xl hover:bg-unmsm-guinda hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg group">
                    Leer más historias
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swiper !== 'undefined') {
                    new Swiper('.testimonios-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 30,
                        loop: true,
                        autoplay: {
                            delay: 3500,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        breakpoints: {
                            768: { slidesPerView: 2, spaceBetween: 30 },
                            1024: { slidesPerView: 3, spaceBetween: 40 },
                        }
                    });
                }
            });
        </script>
    @endpush
@endif