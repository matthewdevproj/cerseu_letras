<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('institucional.nombre_corto') }} - {{ config('institucional.lema') }}</title>
    <meta name="description" content="{{ config('institucional.mision') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-primary: {{ config('institucional.colores.primario') }};
            --color-secondary: {{ config('institucional.colores.secundario') }};
            --color-white: #FFFFFF;
            --color-dark: #1a1a1a;
            --color-gray: #666;
            --color-light-bg: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--color-dark);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--color-primary) 0%, #8B1114 50%, var(--color-secondary) 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease;
        }

        .hero .subtitle {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .hero .tagline {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-style: italic;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: var(--color-primary);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--color-primary);
        }

        /* Stats Section */
        .stats {
            background: var(--color-dark);
            color: white;
            padding: 3rem 2rem;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            color: var(--color-secondary);
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Section Styles */
        section {
            padding: 5rem 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.2rem;
            color: var(--color-gray);
            max-width: 600px;
            margin: 0 auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Programs Grid */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .program-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-top: 4px solid var(--color-secondary);
        }

        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .program-card-header {
            background: linear-gradient(135deg, var(--color-primary), #8B1114);
            color: white;
            padding: 1.5rem;
        }

        .program-card-header h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .program-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .program-card-body {
            padding: 1.5rem;
        }

        .program-card-body p {
            color: var(--color-gray);
            margin-bottom: 1rem;
        }

        .program-meta {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: var(--color-gray);
        }

        .program-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Testimonials */
        .testimonials {
            background: var(--color-light-bg);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 5rem;
            color: var(--color-secondary);
            opacity: 0.3;
            font-family: Georgia, serif;
        }

        .testimonial-text {
            font-style: italic;
            color: var(--color-gray);
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .testimonial-info h4 {
            color: var(--color-primary);
            margin-bottom: 0.25rem;
        }

        .testimonial-info p {
            font-size: 0.9rem;
            color: var(--color-gray);
        }

        /* Mission/Vision */
        .mission-vision {
            background: white;
        }

        .mv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
        }

        .mv-card {
            padding: 2rem;
            border-left: 4px solid var(--color-secondary);
            background: var(--color-light-bg);
            border-radius: 8px;
        }

        .mv-card h3 {
            color: var(--color-primary);
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .mv-card p {
            color: var(--color-gray);
            text-align: justify;
            line-height: 1.8;
        }

        /* Values */
        .values-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .value-item {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .value-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .value-item::before {
            content: '✓';
            display: block;
            font-size: 2rem;
            color: var(--color-secondary);
            margin-bottom: 0.5rem;
        }

        /* Footer */
        footer {
            background: var(--color-dark);
            color: white;
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: var(--color-secondary);
            margin-bottom: 1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--color-secondary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            opacity: 0.8;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero .subtitle {
                font-size: 1.2rem;
            }
            .programs-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>{{ config('institucional.nombre_unidad') }}</h1>
            <p class="subtitle">{{ config('institucional.universidad') }}</p>
            <p class="tagline">"{{ config('institucional.lema') }}"</p>
            <div class="hero-buttons">
                <a href="{{ route('programas.index') }}" class="btn">Explorar Programas</a>
                <a href="{{ route('testimonios.index') }}" class="btn btn-outline">Ver Testimonios</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>{{ count(config('programas.maestrias')) }}</h3>
                <p>Maestrías</p>
            </div>
            <div class="stat-item">
                <h3>{{ count(config('programas.doctorados')) }}</h3>
                <p>Doctorados</p>
            </div>
            <div class="stat-item">
                <h3>{{ count(config('profesores')) }}</h3>
                <p>Profesores</p>
            </div>
            <div class="stat-item">
                <h3>{{ count(config('testimonios')) }}</h3>
                <p>Testimonios</p>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section>
        <div class="container">
            <div class="section-header">
                <h2>Programas Destacados</h2>
                <p>Descubre nuestros programas de maestría y doctorado de excelencia académica</p>
            </div>
            <div class="programs-grid">
                @php
                    $programasDestacados = array_merge(
                        array_slice(\App\Helpers\ProgramaHelper::getMaestriasActivas(), 0, 3),
                        array_slice(\App\Helpers\ProgramaHelper::getDoctoradosActivos(), 0, 3)
                    );
                @endphp
                @foreach($programasDestacados as $programa)
                    <div class="program-card">
                        <div class="program-card-header">
                            <span class="program-badge">{{ $programa['tipo'] === 'maestria' ? 'Maestría' : 'Doctorado' }}</span>
                            <h3>{{ $programa['titulo'] }}</h3>
                        </div>
                        <div class="program-card-body">
                            <div class="program-meta">
                                <span>⏱️ {{ $programa['duracion'] }}</span>
                                <span>📚 {{ $programa['creditos'] }} créditos</span>
                            </div>
                            <p>{{ Str::limit($programa['sumilla'], 120) }}</p>
                            <a href="{{ route('programas.show', $programa['slug']) }}" class="btn" style="width: 100%; text-align: center; margin-top: 1rem;">Ver más detalles</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="{{ route('programas.index') }}" class="btn">Ver Todos los Programas</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>Lo Que Dicen Nuestros Egresados</h2>
                <p>Experiencias reales de quienes han pasado por nuestros programas</p>
            </div>
            <div class="testimonials-grid">
                @php
                    $testimoniosRecientes = \App\Helpers\TestimonioHelper::getTestimoniosRecientes(3);
                @endphp
                @foreach($testimoniosRecientes as $testimonio)
                    <div class="testimonial-card">
                        <p class="testimonial-text">{{ Str::limit($testimonio['texto'], 200) }}</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                {{ strtoupper(substr($testimonio['nombre'], 0, 1)) }}
                            </div>
                            <div class="testimonial-info">
                                <h4>{{ $testimonio['nombre'] }}</h4>
                                <p>{{ $testimonio['cargo_actual'] ?? 'Egresado' }}</p>
                                <p style="color: var(--color-secondary);">Promoción {{ $testimonio['promocion'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="{{ route('testimonios.index') }}" class="btn">Ver Más Testimonios</a>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision">
        <div class="container">
            <div class="section-header">
                <h2>Nuestra Identidad</h2>
            </div>
            <div class="mv-grid">
                <div class="mv-card">
                    <h3>Misión</h3>
                    <p>{{ config('institucional.mision') }}</p>
                </div>
                <div class="mv-card">
                    <h3>Visión</h3>
                    <p>{{ config('institucional.vision') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>Nuestros Valores</h2>
                <p>Principios que guían nuestra labor académica</p>
            </div>
            <div class="values-list">
                @foreach(config('institucional.valores') as $valor)
                    <div class="value-item">
                        <strong>{{ $valor }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ config('institucional.nombre_corto') }}</h3>
                <p>{{ config('institucional.universidad') }}</p>
                <p style="margin-top: 1rem;">{{ config('institucional.contacto.direccion') }}</p>
            </div>
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="{{ route('programas.maestrias') }}">Maestrías</a></li>
                    <li><a href="{{ route('programas.doctorados') }}">Doctorados</a></li>
                    <li><a href="{{ route('institucional.profesores') }}">Plana Docente</a></li>
                    <li><a href="{{ route('institucional.autoridades') }}">Autoridades</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <ul>
                    <li>📧 {{ config('institucional.contacto.email') }}</li>
                    <li>📱 {{ config('institucional.contacto.telefono') }}</li>
                    <li><a href="{{ config('institucional.contacto.web_facultad') }}" target="_blank" rel="noopener noreferrer">Web Facultad</a></li>
                    <li><a href="{{ config('institucional.contacto.sanmarket') }}" target="_blank" rel="noopener noreferrer">Sanmarket</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ config('institucional.nombre_unidad') }}. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
