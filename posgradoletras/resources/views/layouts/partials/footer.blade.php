@php
    $contacto = config('institucional.contacto', array());
@endphp

<footer class="bg-unmsm-guinda text-white border-t-4 border-unmsm-dorado py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8 text-sm">
            <!-- Columna 1: Información Principal -->
            <div class="col-span-1 md:col-span-1">
                <div class="mb-4">
                    <img src="https://letras.unmsm.edu.pe/wp-content/uploads/2022/09/LOGO-BLANCO-LETRAS-WEB_2.png"
                        alt="Logo Letras UNMSM" class="h-16 w-auto mb-3">
                </div>
                <p class="mb-4 leading-relaxed text-white/80 text-xs">
                    La Unidad de Posgrado forma profesionales humanistas especializados en investigación,
                    con alta rigurosidad, ética y calidad académica.
                </p>
                <!-- Redes Sociales -->
                <div class="flex items-center gap-3 mt-4">
                    <a href="https://www.facebook.com/LetrasUNMSM" target="_blank"
                        class="bg-white/10 p-2 rounded hover:bg-unmsm-dorado hover:text-unmsm-guinda transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Contacto -->
            <div>
                <h4 class="font-bold text-white mb-4 text-base border-b border-unmsm-dorado/30 pb-2">Contacto</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2 text-white/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-dorado mt-0.5 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span
                            class="text-xs">{{ isset($contacto['direccion']) ? $contacto['direccion'] : 'Ciudad Universitaria, Lima, Perú' }}</span>
                    </li>
                    <li class="flex items-center gap-2 text-white/80 hover:text-unmsm-dorado transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-dorado flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:{{ isset($contacto['email']) ? $contacto['email'] : 'posgrado-letras@unmsm.edu.pe' }}"
                            class="text-xs">
                            {{ isset($contacto['email']) ? $contacto['email'] : 'posgrado-letras@unmsm.edu.pe' }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2 text-white/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-dorado flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span
                            class="text-xs">{{ isset($contacto['telefono']) ? $contacto['telefono'] : '982 085 037' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Enlaces Rápidos -->
            <div>
                <h4 class="font-bold text-white mb-4 text-base border-b border-unmsm-dorado/30 pb-2">Enlaces Rápidos
                </h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Inicio</a></li>
                    <li><a href="{{ route('programas.index') }}"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Programas</a></li>
                    <li><a href="{{ route('admision') }}"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Admisión 2025</a>
                    </li>
                    <li><a href="{{ route('tramites') }}"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Trámites</a></li>
                    <li><a href="{{ route('nosotros') }}"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Nosotros</a></li>
                </ul>
            </div>

            <!-- Columna 4: Enlaces Institucionales -->
            <div>
                <h4 class="font-bold text-white mb-4 text-base border-b border-unmsm-dorado/30 pb-2">Institucional</h4>
                <ul class="space-y-2">
                    <li><a href="https://unmsm.edu.pe" target="_blank"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">UNMSM</a></li>
                    <li><a href="https://letras.unmsm.edu.pe" target="_blank"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">Facultad de
                            Letras</a></li>
                    <li><a href="https://sanmarket.unmsm.edu.pe" target="_blank"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">SanMarket</a></li>
                    <li><a href="https://sum.unmsm.edu.pe" target="_blank"
                            class="text-white/80 hover:text-unmsm-dorado transition-colors text-xs">SUM</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 pt-6 border-t border-white/10 text-center text-xs text-white/60">
            <p>&copy; {{ date('Y') }} Unidad de Posgrado - Facultad de Letras y Ciencias Humanas - Universidad Nacional
                Mayor de San Marcos</p>
            <p class="mt-1">Decana de América</p>
        </div>
    </div>
</footer>