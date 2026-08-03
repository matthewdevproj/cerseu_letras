<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menú de navegación administrable.
 */
class MenuNavegacionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function entrada(array $extra = []): MenuItem
    {
        return MenuItem::create($extra + [
            'etiqueta' => 'Nosotros',
            'route_name' => 'nosotros',
            'orden' => 0,
            'is_visible' => true,
        ]);
    }

    public function test_el_menu_se_pinta_en_la_barra_publica(): void
    {
        $padre = $this->entrada(['etiqueta' => 'Vida Universitaria', 'route_name' => null]);
        MenuItem::create([
            'parent_id' => $padre->id,
            'etiqueta' => 'Quiénes somos',
            'route_name' => 'nosotros',
            'orden' => 0,
            'is_visible' => true,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        // Aparece dos veces: una en el menú de escritorio y otra en el de móvil,
        // que es justo lo que antes obligaba a mantener dos copias del marcado.
        $this->assertSame(2, substr_count($html, 'Vida Universitaria'));
        $this->assertSame(2, substr_count($html, 'Quiénes somos'));
    }

    public function test_una_entrada_oculta_no_llega_al_sitio(): void
    {
        $this->entrada(['etiqueta' => 'Borrador interno', 'is_visible' => false]);

        $this->get('/')->assertOk()->assertDontSee('Borrador interno');
    }

    public function test_un_hijo_oculto_no_llega_aunque_su_padre_sea_visible(): void
    {
        $padre = $this->entrada(['etiqueta' => 'Admisión', 'route_name' => null]);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Vacantes 2019',
            'url' => 'https://ejemplo.pe/viejo', 'orden' => 0, 'is_visible' => false,
        ]);

        $this->get('/')->assertOk()->assertDontSee('Vacantes 2019');
    }

    public function test_una_ruta_que_ya_no_existe_no_tumba_la_barra(): void
    {
        $this->entrada(['etiqueta' => 'Sección retirada', 'route_name' => 'ruta.que.no.existe']);

        // Sin destino resoluble el elemento se omite, pero la página responde.
        $this->get('/')->assertOk()->assertDontSee('Sección retirada');
    }

    public function test_el_enlace_externo_abre_en_pestana_nueva_con_rel_seguro(): void
    {
        $padre = $this->entrada(['etiqueta' => 'Admisión', 'route_name' => null]);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Cuadro de Vacantes',
            'url' => 'https://posgrado.unmsm.edu.pe/doc/vacantes',
            'nueva_pestana' => true, 'orden' => 0, 'is_visible' => true,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('https://posgrado.unmsm.edu.pe/doc/vacantes', $html);
        $this->assertMatchesRegularExpression(
            '~href="https://posgrado\.unmsm\.edu\.pe/doc/vacantes"[^>]*rel="noopener noreferrer"~',
            $html
        );
    }

    public function test_el_panel_guarda_el_arbol_completo(): void
    {
        $respuesta = $this->actingAs($this->admin())->put('/admin/menu', [
            'items' => [
                [
                    'id' => null, 'etiqueta' => 'Admisión', 'route_name' => '', 'url' => '',
                    'icono' => 'fas-user-plus', 'is_visible' => '1',
                    'hijos' => [
                        ['id' => null, 'etiqueta' => 'Proceso', 'route_name' => 'admision', 'url' => '', 'icono' => '', 'is_visible' => '1'],
                        ['id' => null, 'etiqueta' => 'Vacantes 2026', 'route_name' => '', 'url' => 'https://posgrado.unmsm.edu.pe/doc/v', 'icono' => '', 'nueva_pestana' => '1', 'is_visible' => '1'],
                    ],
                ],
            ],
        ]);

        $respuesta->assertRedirect(route('admin.menu.index'));

        $padre = MenuItem::whereNull('parent_id')->firstOrFail();
        $this->assertSame('Admisión', $padre->etiqueta);
        $this->assertCount(2, $padre->hijos);
        $this->assertSame('Vacantes 2026', $padre->hijos[1]->etiqueta);
        $this->assertTrue($padre->hijos[1]->nueva_pestana);
    }

    public function test_lo_que_no_se_envia_se_borra(): void
    {
        $viejo = $this->entrada(['etiqueta' => 'Convocatoria caducada']);

        $this->actingAs($this->admin())->put('/admin/menu', [
            'items' => [
                ['id' => null, 'etiqueta' => 'Nuevo', 'route_name' => 'nosotros', 'url' => '', 'icono' => '', 'is_visible' => '1'],
            ],
        ]);

        $this->assertNull(MenuItem::find($viejo->id));
        $this->assertSame(1, MenuItem::count());
    }

    public function test_la_ruta_interna_gana_a_la_url_externa(): void
    {
        $this->actingAs($this->admin())->put('/admin/menu', [
            'items' => [
                ['id' => null, 'etiqueta' => 'Ambos', 'route_name' => 'nosotros',
                 'url' => 'https://ejemplo.pe', 'icono' => '', 'is_visible' => '1'],
            ],
        ]);

        $item = MenuItem::firstOrFail();
        $this->assertSame('nosotros', $item->route_name);
        $this->assertNull($item->url);
    }

    public function test_una_direccion_externa_invalida_se_rechaza(): void
    {
        $this->actingAs($this->admin())
            ->put('/admin/menu', [
                'items' => [
                    ['id' => null, 'etiqueta' => 'Rota', 'route_name' => '', 'url' => 'no-es-una-url', 'icono' => '', 'is_visible' => '1'],
                ],
            ])
            ->assertSessionHasErrors('items.0.url');

        $this->assertSame(0, MenuItem::count());
    }

    public function test_guardar_invalida_la_cache_del_menu(): void
    {
        $this->entrada(['etiqueta' => 'Antes']);
        $this->get('/')->assertSee('Antes');   // deja el árbol en caché

        $this->actingAs($this->admin())->put('/admin/menu', [
            'items' => [
                ['id' => null, 'etiqueta' => 'Después', 'route_name' => 'nosotros', 'url' => '', 'icono' => '', 'is_visible' => '1'],
            ],
        ]);

        $this->get('/')->assertSee('Después')->assertDontSee('Antes');
    }

    public function test_el_panel_exige_sesion_de_administrador(): void
    {
        $this->get('/admin/menu')->assertRedirect();
    }

    public function test_en_movil_el_desplegable_ofrece_el_destino_de_su_propia_cabecera(): void
    {
        // En escritorio la cabecera es un enlace, pero en móvil es el botón que
        // despliega: sin esto no había forma de llegar a /programas desde el menú.
        $padre = $this->entrada(['etiqueta' => 'Programas', 'route_name' => 'programas.index']);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Maestrías',
            'route_name' => 'programas.index', 'route_params' => '{"tipo":"maestria"}',
            'orden' => 0, 'is_visible' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('Ver todo: Programas');
    }

    public function test_una_subentrada_que_repite_el_destino_del_padre_no_se_duplica(): void
    {
        $padre = $this->entrada(['etiqueta' => 'Trámites', 'route_name' => 'tramites']);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Trámites y Procedimientos',
            'route_name' => 'tramites', 'orden' => 0, 'is_visible' => true,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        // Solo aparece en el desplegable de escritorio; en móvil la sustituye
        // «Ver todo», que ya lleva al mismo sitio.
        $this->assertSame(1, substr_count($html, 'Trámites y Procedimientos'));
    }

    public function test_una_entrada_sin_hijos_es_un_enlace_simple_sin_desplegable(): void
    {
        $this->entrada(['etiqueta' => 'Diplomados', 'route_name' => 'diplomados.index']);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Diplomados', $html);
        // Sin hijos no se pinta el botón que despliega en móvil.
        $this->assertStringNotContainsString('Ver todo: Diplomados', $html);
    }

    public function test_un_enlace_caducado_se_retira_del_sitio_solo(): void
    {
        // El caso real: «Criterios de Evaluación» apuntó al documento de 2025
        // durante un año sin que nadie lo notara.
        $this->entrada([
            'etiqueta' => 'Criterios de Evaluación 2025',
            'route_name' => null,
            'url' => 'https://posgrado.unmsm.edu.pe/doc/criterios-2025',
            'vigente_hasta' => now()->subMonths(8),
        ]);

        $this->get('/')->assertOk()->assertDontSee('Criterios de Evaluación 2025');
    }

    public function test_el_ultimo_dia_de_vigencia_el_enlace_sigue_activo(): void
    {
        $this->entrada(['etiqueta' => 'Vence hoy', 'vigente_hasta' => now()]);

        $this->get('/')->assertOk()->assertSee('Vence hoy');
    }

    public function test_sin_fecha_de_retirada_el_enlace_no_caduca(): void
    {
        $this->entrada(['etiqueta' => 'Permanente']);

        $this->get('/')->assertOk()->assertSee('Permanente');
    }

    public function test_una_subentrada_caducada_se_retira_sin_tocar_a_sus_hermanas(): void
    {
        $padre = $this->entrada(['etiqueta' => 'Admisión', 'route_name' => null]);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Vacantes vigentes',
            'url' => 'https://ejemplo.pe/v', 'orden' => 0, 'is_visible' => true,
        ]);
        MenuItem::create([
            'parent_id' => $padre->id, 'etiqueta' => 'Vacantes del año pasado',
            'url' => 'https://ejemplo.pe/viejo', 'orden' => 1, 'is_visible' => true,
            'vigente_hasta' => now()->subYear(),
        ]);

        $this->get('/')->assertOk()
            ->assertSee('Vacantes vigentes')
            ->assertDontSee('Vacantes del año pasado');
    }

    public function test_el_panel_si_muestra_lo_caducado_para_poder_arreglarlo(): void
    {
        $item = $this->entrada([
            'etiqueta' => 'Criterios 2025',
            'vigente_hasta' => now()->subMonths(8),
        ]);

        $respuesta = $this->actingAs($this->admin())->get('/admin/menu')->assertOk();

        $inicial = collect($respuesta->viewData('inicial'));
        $fila = $inicial->firstWhere('id', $item->id);

        $this->assertNotNull($fila, 'El panel debe seguir listando lo caducado.');
        $this->assertTrue($fila['caducado']);
    }

    public function test_la_fecha_de_retirada_se_guarda_desde_el_panel(): void
    {
        $this->actingAs($this->admin())->put('/admin/menu', [
            'items' => [
                ['id' => null, 'etiqueta' => 'Convocatoria', 'route_name' => 'nosotros',
                 'url' => '', 'icono' => '', 'is_visible' => '1', 'vigente_hasta' => '2026-12-31'],
            ],
        ]);

        $this->assertSame('2026-12-31', MenuItem::firstOrFail()->vigente_hasta->format('Y-m-d'));
    }
}
