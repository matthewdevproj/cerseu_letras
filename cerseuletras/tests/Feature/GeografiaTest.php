<?php

namespace Tests\Feature;

use App\Services\GeografiaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Países y regiones del formulario de diplomados.
 *
 * Los países salen de `symfony/intl` (ISO 3166-1 + nombres CLDR) y las
 * regiones del archivo del repositorio: ninguna de las dos cosas depende de
 * una llamada de red, así que aquí no hace falta simular HTTP.
 */
class GeografiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        GeografiaService::limpiarCache();
    }

    public function test_trae_los_paises_del_estandar_iso(): void
    {
        $paises = GeografiaService::paises();

        // ISO 3166-1 ronda los 249.
        $this->assertGreaterThan(240, count($paises));
        $this->assertArrayHasKey('nombre', $paises[0]);
        $this->assertArrayHasKey('codigo', $paises[0]);
    }

    public function test_todos_los_nombres_van_en_espanol(): void
    {
        $nombres = collect(GeografiaService::paises())->pluck('nombre');

        // Traducirlos a mano solo cubría los más comunes y el resto salía en
        // inglés en un formulario que está entero en español.
        foreach (['Perú', 'Alemania', 'Japón', 'Estados Unidos', 'Afganistán', 'Turquía'] as $esperado) {
            $this->assertContains($esperado, $nombres);
        }
        foreach (['Germany', 'Japan', 'United States', 'Afghanistan', 'Turkey'] as $ingles) {
            $this->assertNotContains($ingles, $nombres);
        }
    }

    public function test_peru_va_primero_y_el_resto_alfabetico(): void
    {
        $paises = GeografiaService::paises();

        // Casi todo el alumnado es peruano: ahorrarle buscar entre 249.
        $this->assertSame('Perú', $paises[0]['nombre']);
        $this->assertSame('PE', $paises[0]['codigo']);

        $resto = collect($paises)->skip(1)->pluck('nombre')->values()->all();
        $posicion = fn (string $n) => array_search($n, $resto, true);

        // Alfabeto español: «Afganistán» con la A, no al final por la tilde.
        $this->assertLessThan($posicion('Bélgica'), $posicion('Afganistán'));
        $this->assertLessThan($posicion('China'), $posicion('Bélgica'));
    }

    public function test_las_regiones_de_peru_son_las_oficiales(): void
    {
        $regiones = GeografiaService::regiones('PE');

        // El dato externo traía 24: le faltaba Loreto y «Huánuco» venía sin
        // tilde. Son 24 departamentos más la Provincia Constitucional del Callao.
        $this->assertCount(25, $regiones);
        $this->assertContains('Loreto', $regiones);
        $this->assertContains('Huánuco', $regiones);
        $this->assertContains('Callao', $regiones);
        $this->assertNotContains('Huanuco', $regiones);

        // Y en orden: «Áncash» va segunda, no al final.
        $this->assertSame('Amazonas', $regiones[0]);
        $this->assertSame('Áncash', $regiones[1]);
    }

    public function test_hay_regiones_de_todos_los_continentes(): void
    {
        foreach (['ES', 'FR', 'DE', 'JP', 'IN', 'ZA', 'AU', 'CN', 'NG', 'BR'] as $codigo) {
            $this->assertNotEmpty(
                GeografiaService::regiones($codigo),
                "Faltan las regiones de {$codigo}."
            );
        }
    }

    public function test_las_regiones_se_ordenan_con_el_alfabeto_espanol(): void
    {
        $espanolas = GeografiaService::regiones('ES');
        $posicion = fn (string $n) => array_search($n, $espanolas, true);

        // Con la ordenación por bytes de PHP lo acentuado caía al final:
        // «Ávila» acababa detrás de Zaragoza en vez de con la A.
        $this->assertNotFalse($posicion('Ávila'));
        $this->assertLessThan($posicion('Barcelona'), $posicion('Ávila'));
    }

    public function test_un_pais_sin_division_administrativa_devuelve_vacio(): void
    {
        // El formulario lo detecta y deja escribirla a mano.
        $this->assertSame([], GeografiaService::regiones('VA'));
    }

    public function test_un_codigo_desconocido_no_revienta(): void
    {
        $this->assertSame([], GeografiaService::regiones('ZZ'));
    }

    public function test_el_codigo_no_distingue_mayusculas(): void
    {
        $this->assertSame(
            GeografiaService::regiones('PE'),
            GeografiaService::regiones('pe')
        );
    }

    public function test_el_campo_se_llama_distinto_en_cada_pais(): void
    {
        // Decir «Región» en los 249 es impreciso.
        $this->assertSame('Departamento', GeografiaService::etiquetaSubdivision('PE'));
        $this->assertSame('Provincia', GeografiaService::etiquetaSubdivision('ES'));
        $this->assertSame('Prefectura', GeografiaService::etiquetaSubdivision('JP'));
        $this->assertSame('Estado federado', GeografiaService::etiquetaSubdivision('DE'));
        $this->assertSame('Estado', GeografiaService::etiquetaSubdivision('BR'));

        // Para el resto, un término genérico razonable.
        $this->assertSame('Región', GeografiaService::etiquetaSubdivision('ZZ'));
    }

    public function test_el_sitio_sirve_los_paises_sin_llamar_a_terceros(): void
    {
        $respuesta = $this->get('/api/v1/geografia/v2/paises')->assertOk();

        $respuesta->assertJsonStructure(['paises' => [['nombre', 'codigo']]]);
        $this->assertGreaterThan(240, count($respuesta->json('paises')));
    }

    public function test_el_sitio_sirve_las_regiones_con_su_etiqueta(): void
    {
        $this->get('/api/v1/geografia/v2/paises/PE/regiones')
            ->assertOk()
            ->assertJsonPath('etiqueta', 'Departamento')
            ->assertJsonCount(25, 'regiones');
    }

    public function test_conserva_los_nombres_oficiales_intactos(): void
    {
        // Se llegó a recortar el sufijo genérico («A Coruña Province») cuando
        // el dato venía de un intermediario que lo añadía. Con la fuente
        // original sobra, y recortar rompía nombres reales.
        $this->assertContains('Free State', GeografiaService::regiones('ZA'));
        $this->assertContains('Eastern Province', GeografiaService::regiones('SA'));
        $this->assertContains('Mountain Province', GeografiaService::regiones('PH'));
    }
}
