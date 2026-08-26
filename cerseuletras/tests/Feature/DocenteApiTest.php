<?php

namespace Tests\Feature;

use App\Models\Docente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocenteApiTest extends TestCase
{
    use RefreshDatabase;

    private function docente(array $extra = []): Docente
    {
        return Docente::create(array_merge([
            'nombres' => 'Nora', 'apellidos' => 'Solis', 'slug' => 'nora-solis', 'estado' => 1,
        ], $extra));
    }

    public function test_devuelve_los_activos_ordenados_por_apellido(): void
    {
        $this->docente(['nombres' => 'Zoe', 'apellidos' => 'Zamora', 'slug' => 'zoe-zamora']);
        $this->docente(['nombres' => 'Ana', 'apellidos' => 'Alvarez', 'slug' => 'ana-alvarez']);

        $nombres = collect($this->getJson('/api/v1/docentes')->assertOk()->json('data'))
            ->pluck('nombre');

        $this->assertSame(['Ana Alvarez', 'Zoe Zamora'], $nombres->all());
    }

    public function test_no_devuelve_los_inactivos(): void
    {
        $this->docente(['estado' => 0]);

        // `estado` es un booleano, no la cadena «activo»: filtrarlo a mano
        // devolvia una lista vacia sin dar ningun error.
        $this->getJson('/api/v1/docentes')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_el_listado_no_expone_el_correo(): void
    {
        $this->docente(['email' => 'privado@unmsm.edu.pe']);

        $respuesta = $this->getJson('/api/v1/docentes')->assertOk();

        // Publicarlo en HTML estatico es entregarselo a cualquier rastreador.
        $respuesta->assertJsonMissingPath('data.0.email');
        $this->assertStringNotContainsString('privado@unmsm.edu.pe', $respuesta->getContent());
    }

    public function test_la_ficha_anade_biografia_y_perfiles(): void
    {
        $this->docente(['biografia' => 'Una biografía.', 'orcid' => 'https://orcid.org/0000']);

        $this->getJson('/api/v1/docentes/nora-solis')
            ->assertOk()
            ->assertJsonPath('data.biografia', 'Una biografía.')
            ->assertJsonPath('data.orcid', 'https://orcid.org/0000');
    }

    public function test_la_foto_cae_al_marcador_por_defecto(): void
    {
        $this->docente();

        $foto = $this->getJson('/api/v1/docentes')->json('data.0.foto');

        $this->assertStringStartsWith(config('app.url'), $foto);
        $this->assertStringContainsString('profesor-default', $foto);
    }
}
