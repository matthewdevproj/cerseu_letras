<?php

namespace Tests\Feature;

use App\Models\Programa;
use App\Models\TipoOferta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La API de contenido es el contrato con el sitio en Astro.
 *
 * Estas pruebas cuidan la forma de la respuesta, no solo el 200: el sitio se
 * construye contra estos campos, y un renombrado silencioso aquí rompe un
 * build que ocurre en otra máquina y en otro momento.
 */
class OfertaApiTest extends TestCase
{
    use RefreshDatabase;

    private function curso(array $extra = []): Programa
    {
        return Programa::create(array_merge([
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'grado' => TipoOferta::Curso->grado(),
            'modalidad' => 'Virtual',
            'horas_academicas' => 20,
            'sesiones' => 8,
            'sumilla' => 'Una sumilla.',
            'estado' => Programa::ESTADO_PUBLICADO,
        ], $extra));
    }

    public function test_los_tipos_traen_su_unidad_de_medida_y_el_conteo(): void
    {
        $this->curso();

        $respuesta = $this->getJson('/api/v1/tipos-oferta')->assertOk();

        $tipos = collect($respuesta->json('data'));
        $this->assertCount(count(TipoOferta::cases()), $tipos);

        $cursos = $tipos->firstWhere('slug', 'cursos');
        $this->assertSame(['sesiones', 'horas académicas'], $cursos['medidas']);
        $this->assertSame(1, $cursos['publicados']);

        $talleres = $tipos->firstWhere('slug', 'talleres');
        $this->assertSame(['horas académicas'], $talleres['medidas']);
        $this->assertSame(0, $talleres['publicados']);
    }

    public function test_el_listado_expone_los_campos_del_contrato(): void
    {
        $this->curso();

        $this->getJson('/api/v1/programas')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['slug', 'nombre', 'tipo', 'tipo_label',
                'mencion', 'modalidad', 'sumilla', 'medidas', 'inversion', 'estado',
                'imagen']]]);
    }

    public function test_las_medidas_llegan_ya_formateadas(): void
    {
        $this->curso();

        $medidas = $this->getJson('/api/v1/programas')->json('data.0.medidas');

        // La regla de qué unidad usa cada tipo vive en TipoOferta; el sitio no
        // la recompone, solo la imprime.
        $this->assertSame(['8 sesiones', '20 horas académicas'], $medidas);
    }

    public function test_se_puede_filtrar_por_tipo(): void
    {
        $this->curso();
        $this->curso(['nombre' => 'Taller', 'slug' => 'taller', 'grado' => TipoOferta::Taller->grado()]);

        $this->getJson('/api/v1/programas?tipo=talleres')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/programas?tipo=cursos')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_un_tipo_desconocido_da_404_y_dice_cuales_valen(): void
    {
        $this->getJson('/api/v1/programas?tipo=maestrias')
            ->assertNotFound()
            ->assertJsonPath('tipos_validos', ['talleres', 'cursos', 'especializaciones']);
    }

    public function test_la_ficha_responde_por_slug_y_404_si_no_existe(): void
    {
        $this->curso();

        $this->getJson('/api/v1/programas/curso-de-prueba')
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Curso de prueba');

        $this->getJson('/api/v1/programas/no-existe')->assertNotFound();
    }

    public function test_la_ficha_trae_los_docentes_y_el_listado_no(): void
    {
        $curso = $this->curso();
        $docente = \App\Models\Docente::create([
            'nombres' => 'Nora', 'apellidos' => 'Solis', 'slug' => 'nora-solis', 'estado' => 'activo',
        ]);
        $curso->docentes()->attach($docente->id, ['rol' => 'Responsable', 'es_coordinador' => true, 'orden' => 1]);

        // En la ficha si: es donde hacen falta.
        $this->getJson('/api/v1/programas/curso-de-prueba')
            ->assertOk()
            ->assertJsonPath('data.docentes.0.nombre', 'Nora Solis')
            ->assertJsonPath('data.docentes.0.rol', 'Responsable');

        // En el listado no: seria pagar esa consulta una vez por programa.
        $this->getJson('/api/v1/programas')
            ->assertOk()
            ->assertJsonMissingPath('data.0.docentes');
    }

    public function test_las_urls_no_dependen_del_host_de_la_peticion(): void
    {
        $this->curso();

        // Astro pide desde dentro de la red de Docker, donde el host es `web`.
        // Sin forzar la raiz publica, `asset()` devolvia
        // http://web/images/... : valida para el contenedor, muerta para
        // cualquier visitante. Es el fallo caracteristico de separar el
        // frontend, y no se ve hasta que la imagen no carga en el navegador.
        $imagen = $this->withHeader('Host', 'web')
            ->getJson('/api/v1/programas')
            ->assertOk()
            ->json('data.0.imagen');

        $this->assertStringStartsWith(config('app.url'), $imagen);
        $this->assertStringNotContainsString('//web/', $imagen);
    }

    public function test_no_expone_borradores(): void
    {
        $this->curso(['estado' => Programa::ESTADO_BORRADOR]);

        $this->getJson('/api/v1/programas')->assertOk()->assertJsonCount(0, 'data');
    }
}
