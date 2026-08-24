<?php

namespace Tests\Feature;

use App\Models\AdmisionSetting;
use App\Models\Lead;
use App\Models\Programa;
use App\Models\SiteSetting;
use App\Models\TipoOferta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Talleres y cursos comparten todo el código: rutas, controladores, plantillas
 * y pantallas del panel, distinguidos por `tipo`.
 *
 * Eso es justamente lo que hay que vigilar. Un módulo servido con las
 * plantillas del otro no falla de forma ruidosa: responde 200 y muestra los
 * datos equivocados. Estas pruebas recorren los dos por separado y comprueban
 * que cada uno trae lo suyo.
 */
class OfertaPorTipoTest extends TestCase
{
    use RefreshDatabase;

    public static function tipos(): array
    {
        // Del enum, no a mano: al añadir un tipo queda cubierto sin tocar esto.
        $tipos = [];

        foreach (TipoOferta::cases() as $tipo) {
            $tipos[$tipo->slug()] = [$tipo];
        }

        return $tipos;
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function programa(TipoOferta $tipo, string $nombre): Programa
    {
        return Programa::create([
            'grado' => $tipo->grado(),
            'nombre' => $nombre,
            'modalidad' => 'Virtual',
            'duracion' => 3,
            'creditos' => 12,
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tipos')]
    public function test_el_listado_muestra_solo_la_oferta_de_su_tipo(TipoOferta $tipo): void
    {
        $propio = $this->programa($tipo, 'Lo propio de ' . $tipo->plural());
        $otro = collect(TipoOferta::cases())->first(fn ($t) => $t !== $tipo);
        $ajeno = $this->programa($otro, 'Lo ajeno de ' . $otro->plural());

        $this->get(route($tipo->slug() . '.index'))
            ->assertOk()
            ->assertSee($propio->nombre)
            ->assertDontSee($ajeno->nombre);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tipos')]
    public function test_la_admision_de_cada_modulo_trae_sus_propios_ajustes(TipoOferta $tipo): void
    {
        foreach (TipoOferta::cases() as $t) {
            AdmisionSetting::updateOrCreate(
                ['tipo' => $t->value],
                ['hero_titulo' => 'Convocatoria de ' . $t->plural()],
            );
        }

        $this->get(route($tipo->slug() . '.admision'))
            ->assertOk()
            ->assertSee('Convocatoria de ' . $tipo->plural());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tipos')]
    public function test_la_solicitud_queda_registrada_con_su_tipo(TipoOferta $tipo): void
    {
        $programa = $this->programa($tipo, 'Oferta de ' . $tipo->plural());

        $this->post(route($tipo->slug() . '.solicitud'), [
            'nombres' => 'Ana',
            'apellidos' => 'Ruiz',
            'correo' => 'ana@example.test',
            'pais' => 'Perú',
            'region' => 'Lima',
            'telefono' => '999999999',
            'programa_id' => $programa->id,
        ])->assertSessionHasNoErrors();

        $lead = Lead::firstOrFail();
        $this->assertSame($tipo, $lead->tipo);
        $this->assertSame($programa->id, $lead->programa_id);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tipos')]
    public function test_la_ficha_cuelga_del_modulo_que_le_toca(TipoOferta $tipo): void
    {
        $programa = $this->programa($tipo, 'Ficha de ' . $tipo->plural());

        $this->assertStringContainsString('/' . $tipo->slug() . '/', $programa->url);
        $this->get($programa->url)->assertOk()->assertSee($programa->nombre);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tipos')]
    public function test_el_panel_edita_la_admision_de_cada_modulo_por_separado(TipoOferta $tipo): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.admision.update', $tipo->slug()), [
                'hero_titulo' => 'Titular de ' . $tipo->plural(),
            ])
            ->assertRedirect(route('admin.admision.index', $tipo->slug()));

        $this->assertSame(
            'Titular de ' . $tipo->plural(),
            AdmisionSetting::deTipo($tipo)->firstOrFail()->hero_titulo,
        );

        // El otro módulo no se toca al guardar este.
        $otro = collect(TipoOferta::cases())->first(fn ($t) => $t !== $tipo);
        $this->assertNotSame(
            'Titular de ' . $tipo->plural(),
            AdmisionSetting::deTipo($otro)->first()?->hero_titulo,
        );
    }

    public function test_el_panel_de_ajustes_guarda_el_hero_de_los_dos_modulos(): void
    {
        // `site_settings` es de una sola fila y la migración ya la crea.
        SiteSetting::firstOrFail()->update(['site_name' => 'CERSEU Letras']);

        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'site_name' => 'CERSEU Letras',
                'talleres_hero_titulo' => 'Nuestros talleres',
                'cursos_hero_titulo' => 'Nuestros cursos',
            ])
            ->assertSessionHasNoErrors();

        $settings = SiteSetting::firstOrFail();
        $this->assertSame('Nuestros talleres', $settings->talleres_hero_titulo);
        $this->assertSame('Nuestros cursos', $settings->cursos_hero_titulo);
    }

    public function test_las_rutas_anteriores_redirigen_en_vez_de_romperse(): void
    {
        $this->get('/diplomados')->assertRedirect('/talleres');
        $this->get('/diplomados/admision')->assertRedirect('/talleres/admision');
        $this->get('/programas')->assertRedirect('/cursos');
    }

    public function test_un_modulo_inventado_da_404(): void
    {
        $this->get('/seminarios')->assertNotFound();
        $this->actingAs($this->admin())->get('/admin/admision/seminarios')->assertNotFound();
    }
}
