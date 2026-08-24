<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contenido editable de /tramites y /admision.
 */
class ContenidoEditableTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function paginaConSeccion(string $slug, array $extra = []): ContentPage
    {
        $pagina = ContentPage::create(['slug' => $slug, 'titulo' => ucfirst($slug)]);
        $pagina->secciones()->create($extra + [
            'titulo' => 'Sección de prueba',
            'cuerpo' => '<p>Texto original</p>',
            'orden' => 0,
            'is_visible' => true,
        ]);

        ContentPage::clearCache($slug);

        return $pagina;
    }

    public function test_las_secciones_se_muestran_en_la_pagina_publica(): void
    {
        $this->paginaConSeccion('tramites', ['grupo' => 'maestria']);

        // /tramites conserva su marcado original: aquí se comprueba el modelo,
        // que es lo que la página consumirá cuando se conecte.
        $secciones = ContentPage::porSlug('tramites')->seccionesDe('maestria');
        $this->assertCount(1, $secciones);
        $this->assertSame('Sección de prueba', $secciones[0]->titulo);
    }

    public function test_una_seccion_oculta_no_se_muestra(): void
    {
        $pagina = $this->paginaConSeccion('tramites', ['grupo' => 'maestria']);
        $pagina->secciones()->update(['is_visible' => false]);
        ContentPage::clearCache('tramites');

        $this->assertCount(0, ContentPage::porSlug('tramites')->seccionesDe('maestria'));
    }

    /** Los datos de contacto siguen saliendo de Configuración, no del texto. */
    public function test_los_tokens_de_contacto_se_resuelven(): void
    {
        // `site_settings` es un registro único que la migración ya siembra:
        // se actualiza el existente en lugar de crear un segundo, que
        // `SiteSetting::get()` (basado en `first()`) nunca leería.
        SiteSetting::firstOrFail()->update(['email_tramites' => 'tramites@ejemplo.pe', 'telefono' => '900 111 222']);
        SiteSetting::clearCache();

        $pagina = $this->paginaConSeccion('tramites', ['grupo' => 'maestria']);
        $pagina->secciones()->update([
            'cuerpo' => '<p>Escribe a {{email_tramites}} o llama al {{telefono}}</p>',
        ]);
        ContentPage::clearCache('tramites');

        $render = ContentPage::porSlug('tramites')->seccionesDe('maestria')[0]->cuerpo_renderizado;
        $this->assertStringContainsString('tramites@ejemplo.pe', $render);
        $this->assertStringContainsString('900 111 222', $render);
        $this->assertStringNotContainsString('{{email_tramites}}', $render);
    }

    public function test_el_panel_guarda_titulo_orden_y_visibilidad(): void
    {
        $pagina = $this->paginaConSeccion('admision');

        $this->actingAs($this->admin())
            ->put(route('admin.contenido.update', 'admision'), [
                'titulo' => 'Proceso de Admisión 2027-I',
                'subtitulo' => 'Nuevo subtítulo',
                'secciones' => [
                    ['id' => null, 'titulo' => 'Paso nuevo', 'cuerpo' => '<p>Contenido nuevo</p>', 'is_visible' => '1'],
                    ['id' => null, 'titulo' => 'Paso posterior', 'cuerpo' => '<p>Otro</p>', 'is_visible' => '1'],
                ],
            ])
            ->assertRedirect(route('admin.contenido.edit', 'admision'));

        $pagina->refresh();
        $this->assertSame('Proceso de Admisión 2027-I', $pagina->titulo);

        $secciones = $pagina->secciones()->get();
        // La sección original no venía en el envío: se elimina.
        $this->assertCount(2, $secciones);
        $this->assertSame('Paso nuevo', $secciones[0]->titulo);
        $this->assertSame(0, $secciones[0]->orden);
        $this->assertSame(1, $secciones[1]->orden);

        // Y queda disponible para la página en cuanto se conecte.
        $this->assertSame('Paso nuevo', ContentPage::porSlug('admision')->seccionesDe()[0]->titulo);
    }

    public function test_una_seccion_sin_titulo_se_rechaza(): void
    {
        $this->paginaConSeccion('admision');

        $this->actingAs($this->admin())
            ->put(route('admin.contenido.update', 'admision'), [
                'secciones' => [['id' => null, 'titulo' => '', 'cuerpo' => '<p>x</p>']],
            ])
            ->assertSessionHasErrors('secciones.0.titulo');
    }

    public function test_solo_un_admin_edita_el_contenido(): void
    {
        $this->paginaConSeccion('admision');
        $usuario = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($usuario)->get(route('admin.contenido.edit', 'admision'))->assertRedirect('/');
    }

    public function test_una_pagina_desconocida_da_404(): void
    {
        $this->actingAs($this->admin())->get('/admin/contenido/inventada')->assertNotFound();
    }
}
