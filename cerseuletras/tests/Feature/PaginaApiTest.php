<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El contenido editable es lo que permite portar /nosotros, /tramites y
 * /admision sin copiar su texto a una plantilla.
 */
class PaginaApiTest extends TestCase
{
    use RefreshDatabase;

    private function pagina(string $slug = 'nosotros'): ContentPage
    {
        return ContentPage::firstOrCreate(['slug' => $slug], ['titulo' => 'Nosotros']);
    }

    public function test_devuelve_la_pagina_con_sus_secciones_en_orden(): void
    {
        $pagina = $this->pagina();
        $pagina->secciones()->delete();
        $pagina->secciones()->create(['titulo' => 'Segunda', 'cuerpo' => '<p>b</p>', 'orden' => 1, 'is_visible' => true]);
        $pagina->secciones()->create(['titulo' => 'Primera', 'cuerpo' => '<p>a</p>', 'orden' => 0, 'is_visible' => true]);

        $titulos = collect(
            $this->getJson('/api/v1/paginas/nosotros')->assertOk()->json('data.secciones')
        )->pluck('titulo');

        $this->assertSame(['Primera', 'Segunda'], $titulos->all());
    }

    public function test_los_tokens_de_contacto_llegan_resueltos(): void
    {
        SiteSetting::get()->update(['email' => 'contacto@unmsm.edu.pe']);
        SiteSetting::clearCache();

        $pagina = $this->pagina('tramites');
        $pagina->secciones()->delete();
        $pagina->secciones()->create([
            'titulo' => 'Constancia',
            'cuerpo' => '<p>Escribe a {{email_general}}.</p>',
            'orden' => 0,
            'is_visible' => true,
        ]);

        $cuerpo = $this->getJson('/api/v1/paginas/tramites')->assertOk()->json('data.secciones.0.cuerpo');

        // Si viajara el cuerpo en crudo, el sitio publicaria «{{email_general}}»
        // tal cual, o habria que reimplementar la sustitucion en TypeScript.
        $this->assertStringContainsString('contacto@unmsm.edu.pe', $cuerpo);
        $this->assertStringNotContainsString('{{email_general}}', $cuerpo);
    }

    public function test_las_secciones_ocultas_no_se_publican(): void
    {
        $pagina = $this->pagina();
        $pagina->secciones()->delete();
        $pagina->secciones()->create(['titulo' => 'Visible', 'cuerpo' => '', 'orden' => 0, 'is_visible' => true]);
        $pagina->secciones()->create(['titulo' => 'Oculta', 'cuerpo' => '', 'orden' => 1, 'is_visible' => false]);

        $titulos = collect($this->getJson('/api/v1/paginas/nosotros')->json('data.secciones'))->pluck('titulo');

        $this->assertContains('Visible', $titulos);
        $this->assertNotContains('Oculta', $titulos);
    }

    public function test_el_grupo_viaja_para_que_el_sitio_pueda_repartir(): void
    {
        $pagina = $this->pagina();
        $pagina->secciones()->delete();
        $pagina->secciones()->create(['titulo' => 'Misión', 'grupo' => 'mision', 'cuerpo' => '', 'orden' => 0, 'is_visible' => true]);

        $this->getJson('/api/v1/paginas/nosotros')
            ->assertOk()
            ->assertJsonPath('data.secciones.0.grupo', 'mision');
    }

    public function test_una_pagina_inexistente_da_404(): void
    {
        $this->getJson('/api/v1/paginas/no-existe')->assertNotFound();
    }
}
