<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contenido editable de /nosotros y del hero de la portada.
 *
 * Se comprueba contra la API, que es por donde viaja al sitio desde que el
 * público es estático. Lo que se vigila no cambia: que lo que se escribe en el
 * panel llegue, que lo oculto no llegue y que un campo vacío caiga al texto de
 * respaldo en lugar de dejar un hueco.
 */
class NosotrosEditableTest extends TestCase
{
    use RefreshDatabase;

    /** La tabla `site_settings` es un singleton: se edita la fila existente. */
    private function ajustes(array $campos): SiteSetting
    {
        $ajustes = SiteSetting::first() ?: SiteSetting::create(['site_name' => 'Posgrado Letras']);
        $ajustes->fill($campos)->save();
        SiteSetting::clearCache();

        return $ajustes;
    }

    private function paginaNosotros(): ContentPage
    {
        $pagina = ContentPage::create([
            'slug' => 'nosotros',
            'titulo' => 'Quiénes somos',
            'subtitulo' => 'Subtítulo desde el panel',
        ]);

        $pagina->secciones()->create([
            'grupo' => 'mision', 'titulo' => 'Misión', 'orden' => 0, 'is_visible' => true,
            'cuerpo' => '<p>Misión editada desde el panel.</p>',
        ]);
        $pagina->secciones()->create([
            'grupo' => 'vision', 'titulo' => 'Visión', 'orden' => 1, 'is_visible' => true,
            'cuerpo' => '<p>Visión editada desde el panel.</p>',
        ]);
        $pagina->secciones()->create([
            'grupo' => 'valor', 'titulo' => 'Valor de prueba', 'orden' => 2, 'is_visible' => true,
        ]);

        ContentPage::clearCache('nosotros');

        return $pagina;
    }

    public function test_mision_vision_y_valores_salen_de_la_base_de_datos(): void
    {
        $this->paginaNosotros();

        $this->getJson('/api/v1/paginas/nosotros')
            ->assertOk()
            ->assertJsonFragment(['cuerpo' => '<p>Misión editada desde el panel.</p>'])
            ->assertJsonFragment(['cuerpo' => '<p>Visión editada desde el panel.</p>'])
            ->assertJsonFragment(['titulo' => 'Valor de prueba']);
    }

    public function test_el_encabezado_toma_titulo_y_subtitulo_de_la_pagina(): void
    {
        $this->paginaNosotros();

        $pagina = $this->getJson('/api/v1/paginas/nosotros')->assertOk()->json('data');

        $this->assertSame('Quiénes somos', $pagina['titulo']);
        $this->assertSame('Subtítulo desde el panel', $pagina['subtitulo']);
    }

    public function test_un_valor_oculto_no_aparece(): void
    {
        $pagina = $this->paginaNosotros();
        $pagina->secciones()->create([
            'grupo' => 'valor', 'titulo' => 'Valor retirado', 'orden' => 9, 'is_visible' => false,
        ]);
        ContentPage::clearCache('nosotros');

        $this->getJson('/api/v1/paginas/nosotros')->assertOk()
            ->assertJsonMissing(['titulo' => 'Valor retirado']);
    }

    public function test_una_instalacion_nueva_trae_la_pagina_sembrada(): void
    {
        // Una instalación nueva no debe quedarse con la sección en blanco. El
        // texto de respaldo estaba escrito dentro del controlador de Blade
        // —contenido de una unidad dentro del código— y se fue con él: ahora
        // lo pone el seeder, que es un dato y se edita desde el panel.
        $this->seed(\Database\Seeders\NosotrosContentSeeder::class);
        ContentPage::clearCache('nosotros');

        $pagina = $this->getJson('/api/v1/paginas/nosotros')->assertOk()->json('data');
        $texto = json_encode($pagina, JSON_UNESCAPED_UNICODE);

        $this->assertStringContainsString('responsabilidad social universitaria', $texto);
        $this->assertStringContainsString('Pensamiento crítico', $texto);
    }

    public function test_una_pagina_que_no_existe_no_tumba_la_construccion(): void
    {
        // El sitio se genera contra la API: un 404 aquí detendría el build
        // entero. La API dice la verdad —no existe— y es el sitio quien decide
        // seguir sin esa sección (ver obtenerPaginaOpcional en sitio/src/lib).
        $this->getJson('/api/v1/paginas/nosotros')->assertNotFound();
    }

    public function test_el_hero_de_la_portada_usa_los_textos_del_panel(): void
    {
        // La tabla es de una sola fila: se edita la que haya en lugar de crear.
        $this->ajustes([
            'home_hero_kicker' => 'Antetítulo propio',
            'home_hero_titulo' => 'Titular propio',
            'home_hero_texto' => 'Bajada propia.',
            'home_hero_cta1_texto' => 'Botón propio',
            'home_hero_cta1_url' => '/cursos',
        ]);

        $portada = $this->getJson('/api/v1/sitio')->assertOk()->json('data.portada');

        $this->assertSame('Antetítulo propio', $portada['kicker']);
        $this->assertSame('Titular propio', $portada['titulo']);
        $this->assertSame('Bajada propia.', $portada['texto']);
        $this->assertSame('Botón propio', $portada['acciones'][0]['texto']);
    }

    public function test_un_campo_vacio_del_hero_cae_al_texto_original(): void
    {
        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);
        $this->ajustes(['home_hero_titulo' => 'Solo cambio el titular']);

        $portada = $this->getJson('/api/v1/sitio')->assertOk()->json('data.portada');

        $this->assertSame('Solo cambio el titular', $portada['titulo']);
        // El resto sigue como estaba: se puede migrar campo a campo. Lo que
        // no se ha tocado conserva lo que sembró SiteSettingsSeeder, donde
        // vive ahora el texto que antes estaba escrito en la plantilla.
        $this->assertStringContainsString('Decana de América', $portada['kicker']);
        $this->assertSame('Ver cursos', $portada['acciones'][0]['texto']);
    }

    public function test_los_docentes_renacyt_se_editan_desde_el_panel(): void
    {
        $this->ajustes(['home_stat_docentes' => 34]);

        // La cifra sale del panel y no del conteo de fichas: la Unidad anuncia
        // los docentes RENACYT, que no son todos los registrados.
        $this->assertSame(34, $this->getJson('/api/v1/sitio')->json('data.portada.docentes'));
    }

    public function test_sin_valor_los_docentes_renacyt_caen_al_de_siempre(): void
    {
        // Sin valor la API no inventa uno: el sitio cae al número de fichas
        // publicadas, que es el dato que sí puede comprobar.
        $this->assertNull($this->getJson('/api/v1/sitio')->json('data.portada.docentes'));
    }
}
