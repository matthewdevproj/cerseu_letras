<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contenido editable de /nosotros y del hero de la portada.
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

        $this->get('/nosotros')
            ->assertOk()
            ->assertSee('Misión editada desde el panel.', false)
            ->assertSee('Visión editada desde el panel.', false)
            ->assertSee('Valor de prueba');
    }

    public function test_el_encabezado_toma_titulo_y_subtitulo_de_la_pagina(): void
    {
        $this->paginaNosotros();

        $this->get('/nosotros')
            ->assertOk()
            ->assertSee('Quiénes somos')
            ->assertSee('Subtítulo desde el panel');
    }

    public function test_un_valor_oculto_no_aparece(): void
    {
        $pagina = $this->paginaNosotros();
        $pagina->secciones()->create([
            'grupo' => 'valor', 'titulo' => 'Valor retirado', 'orden' => 9, 'is_visible' => false,
        ]);
        ContentPage::clearCache('nosotros');

        $this->get('/nosotros')->assertOk()->assertDontSee('Valor retirado');
    }

    public function test_sin_pagina_creada_se_muestran_los_textos_de_respaldo(): void
    {
        // Una instalación nueva no debe quedarse con la sección en blanco.
        $this->get('/nosotros')
            ->assertOk()
            ->assertSee('Formar profesionales e investigadores', false)
            ->assertSee('Excelencia académica');
    }

    public function test_el_hero_de_la_portada_usa_los_textos_del_panel(): void
    {
        // La tabla es de una sola fila: se edita la que haya en lugar de crear.
        $this->ajustes([
            'home_hero_kicker' => 'Antetítulo propio',
            'home_hero_titulo' => 'Titular propio',
            'home_hero_texto' => 'Bajada propia.',
            'home_hero_cta1_texto' => 'Botón propio',
            'home_hero_cta1_url' => '/programas',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Antetítulo propio')
            ->assertSee('Titular propio')
            ->assertSee('Bajada propia.')
            ->assertSee('Botón propio');
    }

    public function test_un_campo_vacio_del_hero_cae_al_texto_original(): void
    {
        $this->ajustes(['home_hero_titulo' => 'Solo cambio el titular']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Solo cambio el titular')
            // El resto sigue como estaba: se puede migrar campo a campo.
            ->assertSee('Decana de América')
            ->assertSee('Ver diplomados');
    }

    public function test_los_docentes_renacyt_se_editan_desde_el_panel(): void
    {
        $this->ajustes(['home_stat_docentes' => 34]);

        $this->get('/')->assertOk()->assertSee('data-count-to="34"', false);
    }

    public function test_sin_valor_los_docentes_renacyt_caen_al_de_siempre(): void
    {
        $this->get('/')->assertOk()->assertSee('data-count-to="20"', false);
    }

    public function test_los_anios_de_historia_se_calculan_desde_la_fundacion(): void
    {
        // Estaba fijado en 473 —correcto en 2024— y envejecía solo cada 12 de mayo.
        $esperado = (int) \Carbon\Carbon::create(1551, 5, 12)->diffInYears(now());

        $this->get('/')
            ->assertOk()
            ->assertSee('data-count-to="' . $esperado . '"', false)
            // Y sin decimales: `diffInYears` devuelve float en Carbon 3.
            ->assertDontSee('data-count-to="' . $esperado . '.', false);
    }
}
