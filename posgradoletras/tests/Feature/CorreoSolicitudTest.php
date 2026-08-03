<?php

namespace Tests\Feature;

use App\Mail\NuevaSolicitudDiplomado;
use App\Models\DiplomadoLead;
use App\Models\Programa;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Aviso por correo de las solicitudes de diplomado.
 */
class CorreoSolicitudTest extends TestCase
{
    use RefreshDatabase;

    private function programa(): Programa
    {
        return Programa::create([
            'nombre' => 'Diplomado de prueba',
            'slug' => 'diplomado-de-prueba',
            'grado' => 'Diplomado',
            'is_active' => true,
        ]);
    }

    private function solicitud(array $extra = []): array
    {
        return $extra + [
            'nombres' => 'María',
            'apellidos' => 'Quispe Ríos',
            'correo' => 'maria.quispe@ejemplo.pe',
            'pais' => 'PE',
            'region' => 'Cusco',
            'telefono' => '987 654 321',
            'programa_id' => $this->programa()->id,
        ];
    }

    public function test_al_enviar_el_formulario_sale_un_aviso(): void
    {
        Mail::fake();

        $this->post(route('diplomados.solicitud'), $this->solicitud())
            ->assertRedirect();

        Mail::assertSent(NuevaSolicitudDiplomado::class);
    }

    public function test_el_aviso_va_al_correo_de_admision_del_panel(): void
    {
        Mail::fake();

        // El destino sale de Configuración → Contacto, no de un valor fijo en
        // el código: cambiarlo ahí cambia a dónde llegan las solicitudes.
        $ajustes = SiteSetting::first() ?: SiteSetting::create(['site_name' => 'Posgrado']);
        $ajustes->update(['email_admision' => 'admisionposgrado.letras@unmsm.edu.pe']);
        SiteSetting::clearCache();

        $this->post(route('diplomados.solicitud'), $this->solicitud());

        Mail::assertSent(
            NuevaSolicitudDiplomado::class,
            fn ($correo) => $correo->hasTo('admisionposgrado.letras@unmsm.edu.pe')
        );
    }

    public function test_responder_al_aviso_escribe_al_solicitante(): void
    {
        Mail::fake();

        $this->post(route('diplomados.solicitud'), $this->solicitud());

        // Sin esto habría que copiar el correo a mano desde el cuerpo.
        Mail::assertSent(
            NuevaSolicitudDiplomado::class,
            fn ($correo) => $correo->hasReplyTo('maria.quispe@ejemplo.pe')
        );
    }

    public function test_el_remitente_es_un_buzon_real_del_dominio(): void
    {
        // Estaba en «hello@example.com», el valor de fábrica de Laravel: con un
        // remitente ajeno al dominio, SPF y DKIM mandan el correo a spam.
        $remitente = config('mail.from.address');

        $this->assertStringEndsWith('@unmsm.edu.pe', $remitente);
        $this->assertNotSame('hello@example.com', $remitente);
    }

    public function test_el_mensaje_lleva_los_datos_de_la_solicitud(): void
    {
        Mail::fake();

        $this->post(route('diplomados.solicitud'), $this->solicitud());

        Mail::assertSent(NuevaSolicitudDiplomado::class, function ($correo) {
            $cuerpo = $correo->render();

            return str_contains($cuerpo, 'María')
                && str_contains($cuerpo, 'Quispe Ríos')
                && str_contains($cuerpo, 'maria.quispe@ejemplo.pe')
                && str_contains($cuerpo, '987 654 321');
        });
    }

    public function test_la_solicitud_se_guarda_aunque_el_correo_falle(): void
    {
        // El envío va dentro de un try/catch: un fallo de SMTP no puede hacer
        // que se pierda la solicitud ni que el visitante vea un error.
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP caído'));

        $this->post(route('diplomados.solicitud'), $this->solicitud())
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, DiplomadoLead::count());
    }
}
