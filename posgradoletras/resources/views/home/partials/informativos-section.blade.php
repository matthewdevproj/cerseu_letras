<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                <i class="fas fa-file-alt text-unmsm-guinda mr-2"></i>
                Documentos y Recursos
            </h2>
            <p class="text-gray-600">Accede a reglamentos, directivas e información institucional</p>
        </div>

        @php
            $informativos = \App\Http\Controllers\InformativoController::getForHome()->groupBy('categoria');
        @endphp

        @if($informativos->count() > 0)
            <style>
                .home-section-title {
                    font-size: 1.25rem;
                    font-weight: bold;
                    text-transform: uppercase;
                    margin-bottom: 1rem;
                    margin-top: 2rem;
                    padding-bottom: 0.5rem;
                    border-bottom: 2px solid #8B1538;
                    color: #1f2937;
                }

                .home-section-title:first-of-type {
                    margin-top: 0;
                }

                .home-pdf-container {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 15px;
                    border: 1px solid #e0e0e0;
                    padding: 12px 16px;
                    border-radius: 8px;
                    background: white;
                    margin-bottom: 0.75rem;
                    transition: box-shadow 0.3s ease;
                }

                .home-pdf-container:hover {
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .home-pdf-icon {
                    flex-shrink: 0;
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                }

                .home-pdf-icon svg {
                    width: 40px;
                    height: 40px;
                    fill: #dc2626;
                }

                .home-pdf-content {
                    flex-grow: 1;
                }

                .home-pdf-title {
                    margin: 0 0 3px 0;
                    font-size: 1em;
                    font-weight: bold;
                    color: #1f2937;
                }

                .home-pdf-category {
                    margin: 0;
                    font-size: 0.8em;
                    color: #6b7280;
                }

                .home-pdf-button {
                    padding: 8px 16px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    text-align: center;
                    transition: opacity 0.3s ease;
                    display: inline-block;
                    background-color: #8B1538;
                    color: #ffffff;
                    flex-shrink: 0;
                    margin-left: auto;
                    font-size: 0.9em;
                }

                .home-pdf-button:hover {
                    opacity: 0.8;
                }

                @media (max-width: 767px) {
                    .home-pdf-container {
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .home-pdf-icon {
                        justify-content: center;
                        width: 100%;
                        margin-bottom: 10px;
                    }

                    .home-pdf-button {
                        margin-left: 0;
                        width: 100%;
                    }
                }
            </style>

            @foreach($informativos as $categoria => $items)
                <h3 class="home-section-title">
                    <i class="{{ $items->first()->icono }} mr-2"></i>
                    {{ strtoupper($categoria) }}
                </h3>

                @foreach($items as $item)
                    <div class="home-pdf-container">
                        <div class="home-pdf-icon">
                            @if($item->es_pdf)
                                <svg aria-hidden="true" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z" />
                                </svg>
                            @else
                                <svg aria-hidden="true" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" style="fill: #2563eb;">
                                    <path
                                        d="M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z" />
                                </svg>
                            @endif
                        </div>

                        <div class="home-pdf-content">
                            <h3 class="home-pdf-title">{{ $item->titulo }}</h3>
                            <p class="home-pdf-category">
                                <i class="{{ $item->icono }} text-xs mr-1"></i>{{ $item->categoria }}
                            </p>
                        </div>

                        <a href="{{ $item->url }}" target="{{ $item->es_enlace ? '_blank' : '_self' }}" rel="nofollow"
                            class="home-pdf-button">
                            {{ $item->texto_boton }}
                        </a>
                    </div>
                @endforeach
            @endforeach

            <div class="text-center mt-8">
                <a href="{{ route('informativos.index') }}"
                    class="inline-flex items-center gap-2 bg-white border-2 border-unmsm-guinda text-unmsm-guinda px-6 py-3 rounded-lg font-bold hover:bg-unmsm-guinda hover:text-white transition-colors">
                    Ver todos los documentos
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            <p class="text-center text-gray-500">No hay documentos disponibles en este momento.</p>
        @endif
    </div>
</section>