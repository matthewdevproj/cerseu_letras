<?php

namespace Tests\Feature;

use App\Jobs\EnviarAvisoDeSolicitud;
use App\Models\Lead;
use App\Models\SiteSetting;
use App\Models\TipoOferta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * El aviso de una solicitud se envía en una cola con reintentos.
 *
 * Antes iba en la misma petición y con un solo intento: un SMTP caído medio
 * minuto perdía el aviso hasta que alguien revisara `leads.aviso_error` y
 * reenviara a mano. Nadie revisa una columna.
 */
class ColaDeAvisosTest extends TestCase
{
    use RefreshDatabase;

    /** La API exige el programa por slug: la solicitud es siempre sobre algo. */
    private function programa(): \App\Models\Programa
    {
        return \App\Models\Programa::create([
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'grado' => TipoOferta::Curso->grado(),
            'estado' => \App\Models\Programa::ESTADO_PUBLICADO,
        ]);
    }

    private function formulario(array $extra = []): array
    {
        return array_merge([
            'nombres' => 'Ana',
            'apellidos' => 'Quispe',
            'correo' => 'ana@ejemplo.test',
            'telefono' => '999888777',
            'pais' => 'PE',
            'region' => 'Lima',
        ], $extra);
    }

    private function lead(): Lead
    {
        return Lead::create([
            'tipo' => TipoOferta::Curso->value,
            'nombres' => 'Ana',
            'apellidos' => 'Quispe',
            'correo' => 'ana@ejemplo.test',
            'telefono' => '999888777',
            'pais' => 'PE',
            'region' => 'Lima',
        ]);
    }

    public function test_el_formulario_encola_el_aviso_en_vez_de_enviarlo_en_la_peticion(): void
    {
        Queue::fake();

        $this->postJson('/api/v1/solicitudes/cursos', $this->formulario(['programa' => $this->programa()->slug]));

        // El visitante no espera a que responda un servidor de correo.
        Queue::assertPushed(EnviarAvisoDeSolicitud::class);
    }

    public function test_la_solicitud_se_guarda_aunque_el_correo_falle(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP caído'));
        config(['mail.default' => 'smtp']);

        $this->postJson('/api/v1/solicitudes/cursos', $this->formulario(['programa' => $this->programa()->slug]));

        // Lo que no puede perderse es la solicitud; el aviso es secundario.
        $this->assertDatabaseHas('leads', ['correo' => 'ana@ejemplo.test']);
    }

    public function test_un_fallo_de_entrega_se_relanza_para_que_la_cola_reintente(): void
    {
        config(['mail.default' => 'smtp']);
        SiteSetting::get()->update(['email_admision' => 'destino@unmsm.edu.pe']);
        SiteSetting::clearCache();

        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP caído'));

        $lead = $this->lead();

        // El trabajo debe dejar subir la excepción: es lo que hace que la cola
        // programe el siguiente intento en vez de darlo por terminado.
        $this->expectException(\RuntimeException::class);

        (new EnviarAvisoDeSolicitud($lead))->handle();
    }

    public function test_un_fallo_de_configuracion_no_se_reintenta(): void
    {
        // Correo en modo «log»: reintentarlo cinco veces no lo arregla, solo
        // llena el log. Se anota y se termina.
        config(['mail.default' => 'log']);

        $lead = $this->lead();

        (new EnviarAvisoDeSolicitud($lead))->handle();

        $this->assertNotNull($lead->fresh()->aviso_error);
        $this->assertNull($lead->fresh()->aviso_enviado_en);
    }

    public function test_los_reintentos_van_espaciados(): void
    {
        $job = new EnviarAvisoDeSolicitud($this->lead());

        // Espaciados y no seguidos: un servidor que rechaza por saturación
        // necesita tiempo, no insistencia.
        $this->assertSame(5, $job->tries);
        $this->assertSame([60, 300, 900, 3600], $job->backoff);
    }

    public function test_al_agotar_los_intentos_queda_el_motivo_en_el_lead(): void
    {
        $lead = $this->lead();

        (new EnviarAvisoDeSolicitud($lead))->failed(new \RuntimeException('SMTP caído'));

        $error = $lead->fresh()->aviso_error;
        $this->assertStringContainsString('5 intentos', $error);
        $this->assertStringContainsString('SMTP caído', $error);
    }
}
