<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Papelera única del panel.
 */
class PapeleraTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function programa(array $extra = []): Programa
    {
        return Programa::create($extra + [
            'nombre' => 'Maestría en Lingüística',
            'slug' => 'maestria-en-linguistica',
            'grado' => 'Curso',
            'is_active' => true,
        ]);
    }

    public function test_borrar_un_programa_lo_saca_del_sitio_pero_no_lo_pierde(): void
    {
        $programa = $this->programa();

        $this->actingAs($this->admin())->delete("/admin/programas/{$programa->id}");

        $this->assertSoftDeleted($programa);
        $this->get('/cursos')->assertOk()->assertDontSee('Maestría en Lingüística');
    }

    public function test_lo_borrado_aparece_en_la_papelera(): void
    {
        $programa = $this->programa();
        $programa->delete();

        $this->actingAs($this->admin())
            ->get('/admin/papelera')
            ->assertOk()
            ->assertSee('Maestría en Lingüística');
    }

    public function test_restaurar_lo_devuelve_al_sitio(): void
    {
        $programa = $this->programa();
        $programa->delete();

        $this->actingAs($this->admin())
            ->post("/admin/papelera/programas/{$programa->id}/restaurar")
            ->assertRedirect(route('admin.papelera.index'));

        $this->assertNull($programa->fresh()->deleted_at);
        $this->get('/cursos')->assertOk()->assertSee('Maestría en Lingüística');
    }

    public function test_la_papelera_junta_lo_borrado_de_varias_secciones(): void
    {
        $this->programa()->delete();
        Evento::create(['titulo' => 'Coloquio de Letras', 'fecha_inicio' => now()->addWeek(), 'activo' => true])->delete();

        $this->actingAs($this->admin())
            ->get('/admin/papelera')
            ->assertOk()
            ->assertSee('Maestría en Lingüística')
            ->assertSee('Coloquio de Letras');
    }

    public function test_se_puede_filtrar_por_tipo(): void
    {
        $this->programa()->delete();
        Evento::create(['titulo' => 'Coloquio de Letras', 'fecha_inicio' => now()->addWeek(), 'activo' => true])->delete();

        $this->actingAs($this->admin())
            ->get('/admin/papelera?tipo=eventos')
            ->assertOk()
            ->assertSee('Coloquio de Letras')
            ->assertDontSee('Maestría en Lingüística');
    }

    public function test_un_tipo_inventado_devuelve_404(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/papelera/inventado/1/restaurar')
            ->assertNotFound();
    }

    public function test_no_deja_restaurar_algo_que_no_esta_borrado(): void
    {
        $programa = $this->programa();

        $this->actingAs($this->admin())
            ->post("/admin/papelera/programas/{$programa->id}/restaurar")
            ->assertNotFound();
    }

    public function test_la_papelera_exige_sesion_de_administrador(): void
    {
        $this->get('/admin/papelera')->assertRedirect();
    }
}
