<?php

namespace Tests\Feature;

use App\Jobs\EnviarAvisoDeSolicitud;
use App\Models\Programa;
use App\Models\TipoOferta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * El único endpoint de la API que escribe.
 *
 * Es lo que hace posible que Astro sustituya al sitio en Blade: un sitio
 * estático no puede procesar un formulario, así que el POST llega aquí. Y por
 * ser el único que escribe, es el que más cuidado necesita.
 */
class SolicitudApiTest extends TestCase
{
    use RefreshDatabase;

    private function programa(): Programa
    {
        return Programa::create([
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);
    }

    private function datos(array $extra = []): array
    {
        return array_merge([
            'nombres' => 'Ana',
            'apellidos' => 'Quispe',
            'correo' => 'ana@ejemplo.test',
            'telefono' => '999888777',
            'pais' => 'Perú',
            'region' => 'Lima',
            'programa' => 'curso-de-prueba',
        ], $extra);
    }

    public function test_registra_la_solicitud_y_encola_el_aviso(): void
    {
        Queue::fake();
        $this->programa();

        $this->postJson('/api/v1/solicitudes/cursos', $this->datos())
            ->assertCreated()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('leads', [
            'correo' => 'ana@ejemplo.test',
            'tipo' => TipoOferta::Curso->value,
        ]);

        Queue::assertPushed(EnviarAvisoDeSolicitud::class);
    }

    public function test_el_programa_se_identifica_por_slug(): void
    {
        Queue::fake();
        $programa = $this->programa();

        $this->postJson('/api/v1/solicitudes/cursos', $this->datos())->assertCreated();

        // El sitio no conoce los ids —ni tiene por qué—: manda el slug, que es
        // la identidad que usa el resto de la API, y aquí se resuelve.
        $this->assertDatabaseHas('leads', ['programa_id' => $programa->id]);
    }

    public function test_el_senuelo_descarta_sin_delatarse(): void
    {
        Queue::fake();
        $this->programa();

        $respuesta = $this->postJson(
            '/api/v1/solicitudes/cursos',
            $this->datos(['sitio_web' => 'soy-un-robot'])
        );

        // Se responde 201 como un envío correcto: decirle a un robot que ha
        // sido detectado es enseñarle a evitarlo la próxima vez.
        $respuesta->assertCreated();
        $this->assertDatabaseCount('leads', 0);
        Queue::assertNothingPushed();
    }

    public function test_valida_los_campos_obligatorios(): void
    {
        $this->postJson('/api/v1/solicitudes/cursos', ['nombres' => 'Ana'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['apellidos', 'correo', 'telefono', 'pais', 'region', 'programa']);
    }

    public function test_rechaza_un_programa_inexistente(): void
    {
        $this->postJson('/api/v1/solicitudes/cursos', $this->datos(['programa' => 'no-existe']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['programa']);
    }

    public function test_un_tipo_desconocido_da_404(): void
    {
        $this->postJson('/api/v1/solicitudes/maestrias', $this->datos())->assertNotFound();
    }

    public function test_la_solicitud_queda_asociada_al_tipo_de_la_ruta(): void
    {
        Queue::fake();
        Programa::create([
            'nombre' => 'Taller de prueba',
            'slug' => 'taller-de-prueba',
            'grado' => TipoOferta::Taller->grado(),
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $this->postJson('/api/v1/solicitudes/talleres', $this->datos(['programa' => 'taller-de-prueba']))
            ->assertCreated();

        $this->assertDatabaseHas('leads', ['tipo' => TipoOferta::Taller->value]);
    }
}
