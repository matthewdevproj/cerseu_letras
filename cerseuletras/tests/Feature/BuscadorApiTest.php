<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Programa;
use App\Models\TipoOferta;
use App\Support\IndiceDeBusqueda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El índice que descarga el sitio estático para buscar sin servidor.
 */
class BuscadorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // El índice se cachea media hora: sin esto, una prueba vería el
        // índice construido por la anterior.
        IndiceDeBusqueda::olvidar();
    }

    public function test_incluye_la_oferta_publicada(): void
    {
        Programa::create([
            'nombre' => 'Redacción de Tesis',
            'slug' => 'redaccion-de-tesis',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $respuesta = $this->getJson('/api/v1/buscador')->assertOk();

        $titulos = collect($respuesta->json('data'))->pluck('titulo');
        $this->assertTrue($titulos->contains(fn ($t) => str_contains($t, 'Redacción de Tesis')));
    }

    public function test_las_urls_son_rutas_y_no_direcciones_de_laravel(): void
    {
        Programa::create([
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $urls = collect($this->getJson('/api/v1/buscador')->json('data'))->pluck('url');

        // Una URL absoluta apuntaría al dominio de Laravel, y pulsando un
        // resultado el visitante saldría del sitio que está viendo.
        foreach ($urls as $url) {
            $this->assertStringStartsWith('/', $url, "«{$url}» no es una ruta relativa.");
        }
    }

    public function test_trae_los_campos_normalizados(): void
    {
        Programa::create([
            'nombre' => 'Redacción y Ortografía',
            'slug' => 'redaccion-y-ortografia',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $entrada = collect($this->getJson('/api/v1/buscador')->json('data'))
            ->firstWhere('url', '/cursos/redaccion-y-ortografia');

        // Sin tildes y en minúsculas: es lo que permite que quien escribe
        // «redaccion» encuentre «Redacción» sin que el navegador rehaga el
        // trabajo en cada pulsación.
        $this->assertSame('redaccion y ortografia', $entrada['t']);
    }

    public function test_incluye_las_paginas_fijas_de_cada_tipo(): void
    {
        $urls = collect($this->getJson('/api/v1/buscador')->json('data'))->pluck('url');

        foreach (TipoOferta::cases() as $tipo) {
            $this->assertTrue($urls->contains('/' . $tipo->slug()), "Falta /{$tipo->slug()}.");
            $this->assertTrue(
                $urls->contains('/' . $tipo->slug() . '/admision'),
                "Falta /{$tipo->slug()}/admision."
            );
        }
    }

    public function test_un_docente_se_encuentra_por_lo_que_dicta(): void
    {
        $programa = Programa::create([
            'nombre' => 'Normas APA',
            'slug' => 'normas-apa',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $docente = Docente::create([
            'nombres' => 'Luis',
            'apellidos' => 'Mamani',
            'slug' => 'luis-mamani',
            'estado' => true,
        ]);
        $docente->programas()->attach($programa);

        $entrada = collect($this->getJson('/api/v1/buscador')->json('data'))
            ->firstWhere('url', '/profesores/luis-mamani');

        $this->assertNotNull($entrada, 'El docente no aparece en el índice.');
        // Ningún docente tiene biografía cargada: lo que lo identifica —y lo
        // que hace que buscar un curso encuentre a quien lo enseña— es esto.
        $this->assertStringContainsString('Normas APA', $entrada['descripcion']);
    }

    public function test_la_oferta_no_publicada_se_queda_fuera(): void
    {
        Programa::create([
            'nombre' => 'Borrador sin publicar',
            'slug' => 'borrador-sin-publicar',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_BORRADOR,
        ]);

        $urls = collect($this->getJson('/api/v1/buscador')->json('data'))->pluck('url');

        $this->assertFalse($urls->contains('/cursos/borrador-sin-publicar'));
    }
}
