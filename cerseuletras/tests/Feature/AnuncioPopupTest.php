<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Popup de anuncios de la portada.
 *
 * Se parte en dos: lo que el panel guarda —que sigue igual— y lo que llega al
 * sitio, que ahora viaja por /api/v1/anuncios en lugar de pintarse dentro del
 * HTML de Blade. Las reglas de vigencia no cambian.
 *
 * Lo que era marcado —la proporción del marco, que la imagen llene sin bandas,
 * que el popup no se cuele fuera de la portada— se comprueba ahora contra el
 * sitio, en sitio/e2e: es donde vive ese componente.
 */
class AnuncioPopupTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function anuncio(array $extra = []): Anuncio
    {
        Anuncio::clearCache();

        return Anuncio::create($extra + [
            'titulo' => 'Convocatoria',
            'imagen' => 'https://ejemplo.pe/anuncio.jpg',
            'alt' => 'Convocatoria abierta',
            'orden' => 0,
            'is_visible' => true,
        ]);
    }

    public function test_sin_anuncios_vigentes_la_lista_llega_vacia(): void
    {
        // Con la lista vacía el sitio no pinta nada: ni marcado, ni CSS, ni
        // JS. Que el popup viva solo en la portada lo comprueba sitio/e2e.
        $this->getJson('/api/v1/anuncios')->assertOk()->assertJsonPath('data.items', []);
    }

    public function test_un_anuncio_caducado_no_se_muestra(): void
    {
        $this->anuncio(['alt' => 'Ya pasó', 'visible_hasta' => now()->subDay()]);

        $this->getJson('/api/v1/anuncios')->assertOk()->assertJsonPath('data.items', []);
    }

    public function test_un_anuncio_programado_todavia_no_se_muestra(): void
    {
        $this->anuncio(['alt' => 'Aún no toca', 'visible_desde' => now()->addWeek()]);

        $this->getJson('/api/v1/anuncios')->assertOk()->assertJsonPath('data.items', []);
    }

    public function test_el_ultimo_dia_de_vigencia_todavia_cuenta(): void
    {
        // La comparación es por día, no por instante: si caduca hoy, hoy se ve.
        $this->anuncio(['alt' => 'Último día', 'visible_hasta' => now()]);

        $this->getJson('/api/v1/anuncios')->assertOk()
            ->assertJsonFragment(['alt' => 'Último día']);
    }

    public function test_un_anuncio_sin_fechas_se_muestra_siempre(): void
    {
        $this->anuncio(['alt' => 'Permanente']);

        $this->getJson('/api/v1/anuncios')->assertOk()
            ->assertJsonFragment(['alt' => 'Permanente']);
    }

    public function test_un_anuncio_oculto_no_se_muestra_aunque_esté_en_fecha(): void
    {
        $this->anuncio(['alt' => 'Apagado', 'is_visible' => false]);

        $this->getJson('/api/v1/anuncios')->assertOk()->assertJsonPath('data.items', []);
    }

    public function test_el_panel_crea_un_anuncio_con_imagen(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/anuncios', [
                'titulo' => 'Convocatoria 2026-I',
                'imagen' => UploadedFile::fake()->image('anuncio.jpg'),
                'alt' => 'Convocatoria 2026-I abierta',
                'is_visible' => '1',
            ])
            ->assertRedirect(route('admin.anuncios.index'));

        $anuncio = Anuncio::firstOrFail();
        $this->assertSame('Convocatoria 2026-I', $anuncio->titulo);
        Storage::disk('public')->assertExists($anuncio->imagen);
    }

    public function test_se_rechaza_un_archivo_que_no_es_imagen(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/anuncios', [
                'titulo' => 'Malicioso',
                'imagen' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
                'is_visible' => '1',
            ])
            ->assertSessionHasErrors('imagen');

        $this->assertSame(0, Anuncio::count());
    }

    public function test_la_fecha_de_fin_no_puede_ser_anterior_a_la_de_inicio(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/anuncios', [
                'titulo' => 'Fechas al revés',
                'imagen' => UploadedFile::fake()->image('a.jpg'),
                'visible_desde' => '2026-06-01',
                'visible_hasta' => '2026-05-01',
            ])
            ->assertSessionHasErrors('visible_hasta');
    }

    public function test_borrar_lo_manda_a_la_papelera_y_se_puede_restaurar(): void
    {
        $anuncio = $this->anuncio(['alt' => 'Recuperable']);

        $this->actingAs($this->admin())
            ->delete("/admin/anuncios/{$anuncio->id}")
            ->assertRedirect(route('admin.anuncios.index'));

        // Fuera del sitio, pero no perdido.
        $this->assertSoftDeleted($anuncio);
        $this->getJson('/api/v1/anuncios')->assertOk()->assertJsonPath('data.items', []);

        $this->actingAs($this->admin())
            ->post("/admin/anuncios/{$anuncio->id}/restaurar")
            ->assertRedirect(route('admin.anuncios.index'));

        Anuncio::clearCache();
        $this->getJson('/api/v1/anuncios')->assertOk()
            ->assertJsonFragment(['alt' => 'Recuperable']);
    }

    public function test_editar_sin_subir_imagen_conserva_la_que_habia(): void
    {
        $anuncio = $this->anuncio(['imagen' => 'anuncios/original.jpg']);

        $this->actingAs($this->admin())
            ->put("/admin/anuncios/{$anuncio->id}", [
                'titulo' => 'Nombre cambiado',
                'is_visible' => '1',
            ])
            ->assertRedirect(route('admin.anuncios.index'));

        $anuncio->refresh();
        $this->assertSame('Nombre cambiado', $anuncio->titulo);
        $this->assertSame('anuncios/original.jpg', $anuncio->imagen);
    }

    public function test_el_panel_exige_sesion_de_administrador(): void
    {
        $this->get('/admin/anuncios')->assertRedirect();
    }

    public function test_las_medidas_reales_de_la_imagen_llegan_al_popup(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/anuncios', [
            'titulo' => 'Con medidas',
            'imagen' => UploadedFile::fake()->image('cartel.jpg', 900, 1273),
            'is_visible' => '1',
        ]);

        $anuncio = Anuncio::firstOrFail();
        $this->assertSame(900, $anuncio->imagen_ancho);
        $this->assertSame(1273, $anuncio->imagen_alto);

        Anuncio::clearCache();

        // Sin medidas reales el navegador reservaba un hueco inventado y la
        // ventana daba un salto de 260 px al cargar la imagen.
        $this->getJson('/api/v1/anuncios')->assertOk()
            ->assertJsonFragment(['ancho' => 900, 'alto' => 1273]);
    }

    public function test_los_ajustes_del_popup_se_guardan_sin_tocar_el_resto(): void
    {
        $ajustes = SiteSetting::first() ?: SiteSetting::create(['site_name' => 'Posgrado']);
        $ajustes->update(['email' => 'contacto@unmsm.edu.pe', 'telefono' => '982 085 037']);

        $this->actingAs($this->admin())->post('/admin/anuncios/ajustes', [
            'popup_retardo_ms' => 3000,
            'popup_frecuencia' => 'dia',
            'popup_auto_avance' => '1',
        ])->assertRedirect();

        $ajustes->refresh();
        $this->assertSame(3000, $ajustes->popup_retardo_ms);
        $this->assertSame('dia', $ajustes->popup_frecuencia);
        $this->assertTrue((bool) $ajustes->popup_auto_avance);

        // Lo que no viaja en el formulario no se toca: enviarlo a
        // `settings.update` habría vaciado correo, teléfono y redes.
        $this->assertSame('contacto@unmsm.edu.pe', $ajustes->email);
        $this->assertSame('982 085 037', $ajustes->telefono);
    }

    public function test_una_frecuencia_inventada_se_rechaza(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/anuncios/ajustes', ['popup_frecuencia' => 'cuando-sea'])
            ->assertSessionHasErrors('popup_frecuencia');
    }

    public function test_el_retardo_configurado_llega_a_la_portada(): void
    {
        $this->anuncio();
        $ajustes = SiteSetting::first() ?: SiteSetting::create(['site_name' => 'Posgrado']);
        $ajustes->update(['popup_retardo_ms' => 4500]);
        SiteSetting::clearCache();

        // El retardo viaja junto a los anuncios: separarlos obligaría al
        // sitio a decidirlo por su cuenta y dejaría de ser administrable.
        $this->getJson('/api/v1/anuncios')->assertOk()
            ->assertJsonPath('data.ajustes.retardo', 4500);
    }

    public function test_calcula_cuanto_se_recortara_de_cada_imagen(): void
    {
        // Marco 4:5. Una imagen más ancha pierde por los lados; una más alta,
        // por arriba y por abajo.
        $exacta = $this->anuncio(['imagen_ancho' => 1000, 'imagen_alto' => 1250]);
        $this->assertSame(0, $exacta->recorte_porcentaje);
        $this->assertFalse($exacta->recorte_notable);
        $this->assertNull($exacta->recorte_lado);

        $ancha = $this->anuncio(['imagen_ancho' => 1200, 'imagen_alto' => 900]);
        $this->assertSame(40, $ancha->recorte_porcentaje);
        $this->assertSame('por los lados', $ancha->recorte_lado);

        $alta = $this->anuncio(['imagen_ancho' => 900, 'imagen_alto' => 1600]);
        $this->assertSame(30, $alta->recorte_porcentaje);
        $this->assertSame('por arriba y por abajo', $alta->recorte_lado);
    }

    public function test_sin_medidas_conocidas_no_inventa_un_recorte(): void
    {
        $anuncio = $this->anuncio(['imagen_ancho' => null, 'imagen_alto' => null]);

        $this->assertNull($anuncio->recorte_porcentaje);
        $this->assertFalse($anuncio->recorte_notable);
    }

    public function test_el_panel_indica_la_resolucion_y_avisa_del_recorte(): void
    {
        $this->anuncio(['titulo' => 'Cartel apaisado', 'imagen_ancho' => 1200, 'imagen_alto' => 900]);

        $this->actingAs($this->admin())
            ->get('/admin/anuncios')
            ->assertOk()
            ->assertSee('Se recorta 40%');
    }

    public function test_el_formulario_dice_la_resolucion_que_hace_falta(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/anuncios/create')
            ->assertOk()
            ->assertSee('1000 × 1250 px', false);
    }
}
