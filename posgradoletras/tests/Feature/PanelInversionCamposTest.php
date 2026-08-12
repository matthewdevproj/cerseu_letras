<?php

namespace Tests\Feature;

use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El formulario de programas debe exponer todos los campos de inversión
 * económica que la ficha sabe mostrar. Sin esto, un campo puede existir en la
 * base y en la vista pública pero no tener dónde escribirse.
 */
class PanelInversionCamposTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function diplomado(): Programa
    {
        return Programa::create([
            'grado' => 'Diplomado',
            'nombre' => 'Diplomado de Prueba',
            'modalidad' => 'Virtual',
            'duracion' => 2,
            'creditos' => 24,
            'estado' => Programa::ESTADO_PUBLICADO,
            'inversion_economica' => ['costo_matricula' => 200],
        ]);
    }

    public function test_el_formulario_de_edicion_expone_los_campos_de_inversion(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.programas.edit', $this->diplomado()))
            ->assertOk()
            ->getContent();

        foreach ([
            'inv_derecho_bachiller',
            'inv_derecho_otras',
            'inv_costo_total',
            'inv_costo_diploma',
            'inv_costo_matricula',
        ] as $campo) {
            $this->assertStringContainsString('id="' . $campo . '"', $html, "Falta el campo {$campo}");
        }

        // El valor guardado se precarga en el formulario.
        $this->assertMatchesRegularExpression(
            '~id="inv_costo_matricula"[^>]*value="200"~',
            $html,
        );

        // Y los repetidores de modalidades y condiciones están montados.
        $this->assertStringContainsString('name="inversion_modalidades"', $html);
        $this->assertStringContainsString('name="inversion_condiciones"', $html);
    }

    public function test_el_formulario_de_alta_expone_los_mismos_campos(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.programas.create'))
            ->assertOk()
            ->getContent();

        foreach ([
            'inv_costo_total',
            'inv_costo_diploma',
            'inv_costo_matricula',
        ] as $campo) {
            $this->assertStringContainsString('id="' . $campo . '"', $html, "Falta el campo {$campo}");
        }

        $this->assertStringContainsString('name="inversion_modalidades"', $html);
        $this->assertStringContainsString('name="inversion_condiciones"', $html);
    }
}
