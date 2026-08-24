<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\CronogramaAdmision;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los componentes Alpine del panel se definían dentro de las plantillas; ahora
 * viven en `resources/js/repetidores.js` y se exponen como globales desde
 * `app.js`. Estas pruebas vigilan el contrato entre ambas mitades: que la vista
 * siga invocando la fábrica con los argumentos que la fábrica espera, y que no
 * haya vuelto a colarse una definición inline que la sombree.
 */
class ComponentesAlpineTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_el_cronograma_del_panel_invoca_la_fabrica_con_pasos_visibilidad_e_iconos(): void
    {
        CronogramaAdmision::create(['titulo' => 'Convocatoria 2026', 'is_visible' => true]);

        $html = $this->actingAs($this->admin())
            ->get('/admin/cronograma-admision')
            ->assertOk()
            ->getContent();

        // Tres argumentos: pasos iniciales, visibilidad y mapa de íconos.
        $this->assertMatchesRegularExpression('~x-data="cronogramaAdmision\(.+,.+,.+\)"~s', $html);
        $this->assertStringNotContainsString('function cronogramaAdmision', $html);
    }

    public function test_el_editor_de_contenido_recibe_las_secciones_y_el_grupo_por_defecto(): void
    {
        $pagina = ContentPage::create(['slug' => 'tramites', 'titulo' => 'Trámites']);
        $pagina->secciones()->create([
            'titulo' => 'Paso I',
            'cuerpo' => '<p>Texto</p>',
            'grupo' => 'maestria',
            'orden' => 0,
            'is_visible' => true,
        ]);

        $html = $this->actingAs($this->admin())
            ->get('/admin/contenido/tramites')
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression('~x-data="editorContenido\(.+,.+\)"~s', $html);
        $this->assertStringNotContainsString('function editorContenido', $html);
    }

    public function test_las_tarifas_por_periodo_usan_la_fabrica_y_sus_metodos(): void
    {
        $programa = Programa::create([
            'nombre' => 'Maestría de prueba',
            'slug' => 'maestria-de-prueba',
            'grado' => 'Curso',
            'is_active' => true,
            'costo_por_credito' => 160,
        ]);

        $html = $this->actingAs($this->admin())
            ->get("/admin/programas/{$programa->id}/edit")
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('x-data="inversionPeriodos(', $html);
        $this->assertStringNotContainsString('function inversionPeriodos', $html);
        // La vista ya no manipula el array a mano: delega en el repetidor.
        $this->assertStringContainsString('@click="agregar()"', $html);
        $this->assertStringContainsString('@click="eliminar(i)"', $html);
    }

    public function test_el_buscador_recibe_la_ruta_de_sugerencias_desde_la_plantilla(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('x-data="siteSearch(\'' . route('search.suggest') . '\')"', $html);
        $this->assertStringNotContainsString('function siteSearch', $html);
    }

    public function test_el_filtro_de_programas_ya_no_va_escrito_en_las_vistas(): void
    {
        $portada = $this->get('/')->assertOk()->getContent();
        $listado = $this->get('/cursos')->assertOk()->getContent();

        $this->assertStringNotContainsString('function filterPrograms', $portada);
        $this->assertStringNotContainsString('function filterPrograms', $listado);

        // El listado de cada módulo ya no monta filtro propio: /cursos y
        // /talleres muestran un solo tipo cada uno, así que no hay nada que
        // filtrar. El único filtro que queda es el de la portada, que mezcla
        // ambos y se monta desde el bundle (ver app.js).
        $this->assertStringNotContainsString('montarFiltroProgramas({', $listado);
    }

    public function test_el_editor_de_contenido_ya_no_pide_html_a_mano(): void
    {
        $pagina = \App\Models\ContentPage::create(['slug' => 'tramites', 'titulo' => 'Trámites']);
        $pagina->secciones()->create([
            'titulo' => 'Paso I', 'cuerpo' => '<p>Texto</p>',
            'grupo' => 'maestria', 'orden' => 0, 'is_visible' => true,
        ]);

        $html = $this->actingAs($this->admin())
            ->get('/admin/contenido/tramites')
            ->assertOk()
            ->getContent();

        // El campo lo toma el editor con formato; el textarea sigue debajo
        // como respaldo sin JavaScript.
        $this->assertStringContainsString('data-editor-texto', $html);
        $this->assertStringNotContainsString('Contenido (HTML)', $html);
    }

    public function test_los_formularios_largos_avisan_de_cambios_sin_guardar(): void
    {
        \App\Models\ContentPage::create(['slug' => 'tramites', 'titulo' => 'Trámites']);

        $rutas = ['/admin/contenido/tramites', '/admin/menu', '/admin/settings'];

        foreach ($rutas as $ruta) {
            $this->actingAs($this->admin())
                ->get($ruta)
                ->assertOk()
                ->assertSee('data-avisar-sin-guardar', false);
        }
    }
}
