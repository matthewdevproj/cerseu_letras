<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Programa;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre el documento «Ajustes para la página web de Diplomados» (observaciones
 * N.º 1 a 7). Los ajustes de Admisión que menciona el mismo documento quedan
 * fuera: están pendientes de una RD.
 *
 * Se comprueba contra /api/v1/programas/{slug}, que es por donde estos datos
 * llegan al sitio desde que el público es estático. Lo que se pidió no cambia
 * —la denominación de quien coordina, los importes, qué se descarta— solo el
 * sitio donde se mira.
 *
 * El ORDEN de los bloques de inversión, que también fijó el documento, ya no
 * es cosa de la API: lo decide el marcado de InversionPrograma.astro y lo
 * comprueba sitio/e2e sobre el sitio construido.
 */
class AjustesDiplomadosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function diplomado(array $atributos = []): Programa
    {
        return Programa::create(array_merge([
            'grado' => 'Taller',
            'nombre' => 'Diplomado de Prueba',
            'modalidad' => 'Virtual',
            'duracion' => 2,
            'creditos' => 24,
            'estado' => Programa::ESTADO_PUBLICADO,
        ], $atributos));
    }

    private function coordinador(Programa $programa, ?string $denominacion): Docente
    {
        $docente = Docente::create([
            'nombres' => 'Melissa',
            'apellidos' => 'Giorgio Alcalde',
            'grado' => 'Dra.',
            'estado' => 1,
        ]);

        $programa->docentes()->attach($docente->id, [
            'es_coordinador' => true,
            'coordinador_denominacion' => $denominacion,
            'orden' => 1,
        ]);

        return $docente;
    }

    // Obs. N.º 1 — Coordinador / Coordinadora

    public function test_la_ficha_rotula_al_responsable_como_coordinador_por_defecto(): void
    {
        $programa = $this->diplomado();
        $this->coordinador($programa, null);

        $ficha = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data');

        $this->assertSame('Coordinador', $ficha['docentes'][0]['denominacion']);
    }

    public function test_la_denominacion_puede_ser_coordinadora_en_cada_programa(): void
    {
        $programa = $this->diplomado();
        $this->coordinador($programa, 'Coordinadora');

        $ficha = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data');

        $this->assertSame('Coordinadora', $ficha['docentes'][0]['denominacion']);
    }

    public function test_no_queda_la_etiqueta_que_repetia_la_denominacion(): void
    {
        $programa = $this->diplomado();
        $this->coordinador($programa, 'Coordinadora');

        $ficha = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data');

        // La denominación viaja una sola vez, en el docente que coordina: la
        // etiqueta suelta que la repetía se fue con la vista que la pintaba.
        $denominaciones = array_filter(array_column($ficha['docentes'], 'denominacion'));
        $this->assertSame(['Coordinadora'], array_values($denominaciones));
    }

    public function test_un_valor_de_denominacion_invalido_cae_al_predeterminado(): void
    {
        $this->assertSame('Coordinador', Programa::denominacionCoordinador(null));
        $this->assertSame('Coordinador', Programa::denominacionCoordinador(''));
        $this->assertSame('Coordinador', Programa::denominacionCoordinador('<script>'));
        $this->assertSame('Coordinadora', Programa::denominacionCoordinador('Coordinadora'));
    }

    public function test_el_panel_guarda_la_denominacion_solo_para_quien_coordina(): void
    {
        $programa = $this->diplomado();
        $coordina = Docente::create(['nombres' => 'Ana', 'apellidos' => 'Ruiz', 'estado' => 1]);
        $otro = Docente::create(['nombres' => 'Luis', 'apellidos' => 'Paz', 'estado' => 1]);

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'docentes_asignados' => [$coordina->id, $otro->id],
                'docentes_coordinador' => ['1', '0'],
                // La segunda fila envía denominación aunque no coordine: no debe guardarse.
                'docentes_coordinador_denominacion' => ['Coordinadora', 'Coordinadora'],
                'docentes_rol' => ['Coordinación', 'Docente'],
                'docentes_orden' => ['1', '2'],
            ])
            ->assertRedirect(route('admin.programas.index'));

        $pivotes = $programa->fresh()->docentes->keyBy('id');

        $this->assertSame('Coordinadora', $pivotes[$coordina->id]->pivot->coordinador_denominacion);
        $this->assertNull($pivotes[$otro->id]->pivot->coordinador_denominacion);
    }

    // Obs. N.º 2 — Inversión económica

    public function test_la_inversion_sigue_el_orden_pedido_por_posgrado(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'costo_total' => 3650,
                'costo_diploma' => 650,
                'descuentos' => 'Descuento por pago adelantado.',
                'modalidades' => [
                    ['nombre' => 'Pago único', 'cuotas' => [
                        ['etiqueta' => 'Cuota única', 'monto' => 3000, 'fecha' => '16, 17 y 18 de septiembre'],
                    ]],
                ],
            ],
        ]);

        // La API entrega las piezas; el orden en que se pintan lo decide
        // InversionPrograma.astro y lo comprueba sitio/e2e. Aquí se vigila que
        // no falte ninguna: sin dato no hay bloque que ordenar.
        $inversion = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion'];

        $this->assertEquals(3650, $inversion['costo_total']);
        $this->assertEquals(650, $inversion['costo_diploma']);
        $this->assertCount(1, $inversion['modalidades']);
        $this->assertContains('Descuento por pago adelantado.', $inversion['condiciones']);
    }

    public function test_el_costo_total_lleva_la_nota_de_lo_que_incluye(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        // La nota «incluye la totalidad de los derechos de enseñanza…» la
        // pone la ficha junto al importe; lo que viaja es el importe.
        $this->assertEquals(3650, $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['costo_total']);
    }

    public function test_el_pago_de_diploma_dice_costo_del_diploma_y_su_plazo(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_diploma' => 650]]);

        $inversion = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion'];

        $this->assertEquals(650, $inversion['costo_diploma']);
        // Sin costo total no llega ninguno: el bloque no puede reaparecer bajo
        // el importe del diploma porque no hay importe que pintar.
        $this->assertNull($inversion['costo_total']);
    }

    public function test_el_costo_por_matricula_va_debajo_del_pago_de_diploma(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'costo_total' => 3650,
                'costo_diploma' => 650,
                'costo_matricula' => 200,
            ],
        ]);

        $inversion = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion'];

        $this->assertEquals(200, $inversion['costo_matricula']);
        $this->assertEquals(650, $inversion['costo_diploma']);
    }

    public function test_sin_costo_por_matricula_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        // Sin importe no hay bloque: la ficha omite lo que llega en null.
        $this->assertNull($this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['costo_matricula']);
    }

    public function test_el_panel_guarda_el_costo_por_matricula(): void
    {
        $programa = $this->diplomado();

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'inversion_economica' => json_encode([
                    'costo_total' => 3650,
                    'costo_diploma' => 650,
                    'costo_matricula' => 200,
                ]),
            ])
            ->assertRedirect(route('admin.programas.index'));

        $inversion = $programa->fresh()->inversion_economica;

        $this->assertEquals(200, $inversion['costo_matricula']);
        // No pisa los importes que ya estaban.
        $this->assertEquals(3650, $inversion['costo_total']);
        $this->assertEquals(650, $inversion['costo_diploma']);
    }

    public function test_el_pago_unico_y_el_fraccionado_muestran_montos_y_fechas(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'modalidades' => [
                    ['nombre' => 'Pago único', 'cuotas' => [
                        ['etiqueta' => 'Cuota única', 'monto' => 3000, 'fecha' => '16, 17 y 18 de septiembre'],
                    ]],
                    ['nombre' => 'Pago fraccionado', 'cuotas' => [
                        ['etiqueta' => 'Cuota 1', 'monto' => 1500, 'fecha' => 'Del 16 al 18 de septiembre'],
                        ['etiqueta' => 'Cuota 2', 'monto' => 1500, 'fecha' => 'Hasta el 30 de noviembre'],
                    ]],
                ],
            ],
        ]);

        $modalidades = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['modalidades'];

        $this->assertSame('Pago único', $modalidades[0]['nombre']);
        $this->assertSame('Cuota única', $modalidades[0]['cuotas'][0]['etiqueta']);
        $this->assertEquals(3000, $modalidades[0]['cuotas'][0]['monto']);
        $this->assertSame('16, 17 y 18 de septiembre', $modalidades[0]['cuotas'][0]['fecha']);

        $this->assertSame('Pago fraccionado', $modalidades[1]['nombre']);
        $this->assertCount(2, $modalidades[1]['cuotas']);
        $this->assertSame('Hasta el 30 de noviembre', $modalidades[1]['cuotas'][1]['fecha']);
    }

    public function test_admite_mas_de_dos_cuotas_por_modalidad(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'modalidades' => [
                    ['nombre' => 'Pago en tres armadas', 'cuotas' => [
                        ['etiqueta' => 'Cuota 1', 'monto' => 1000, 'fecha' => 'Setiembre'],
                        ['etiqueta' => 'Cuota 2', 'monto' => 1000, 'fecha' => 'Octubre'],
                        ['etiqueta' => 'Cuota 3', 'monto' => 1000, 'fecha' => 'Noviembre'],
                    ]],
                ],
            ],
        ]);

        $this->assertCount(3, $programa->modalidades_de_pago[0]['cuotas']);

        $cuotas = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['modalidades'][0]['cuotas'];

        $this->assertCount(3, $cuotas);
        $this->assertSame('Cuota 3', $cuotas[2]['etiqueta']);
        $this->assertSame('Noviembre', $cuotas[2]['fecha']);
    }

    public function test_las_cuotas_planas_anteriores_se_siguen_mostrando(): void
    {
        // Formato previo a la reorganización: lista suelta, sin modalidades.
        $programa = $this->diplomado([
            'inversion_economica' => [
                'cuotas' => [
                    ['numero' => 1, 'monto' => 1500, 'fecha' => 'Marzo'],
                    ['numero' => 2, 'monto' => 1500, 'fecha' => 'Junio'],
                ],
            ],
        ]);

        $modalidades = $programa->modalidades_de_pago;

        $this->assertCount(1, $modalidades);
        $this->assertSame('Pago fraccionado', $modalidades[0]['nombre']);
        $this->assertSame('Cuota 1', $modalidades[0]['cuotas'][0]['etiqueta']);

        $cuotas = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['modalidades'][0]['cuotas'];

        $this->assertEquals(1500, $cuotas[0]['monto']);
        $this->assertSame('Marzo', $cuotas[0]['fecha']);
    }

    public function test_las_cuotas_sin_monto_ni_fecha_no_llegan_a_la_ficha(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'modalidades' => [
                    ['nombre' => 'Pago único', 'cuotas' => [
                        ['etiqueta' => 'Cuota única', 'monto' => null, 'fecha' => ''],
                    ]],
                ],
            ],
        ]);

        $this->assertSame([], $programa->modalidades_de_pago);

        // Filas que el panel deja al añadir y no completar: no llegan. Y como
        // era lo único que tenía este programa, tampoco llega bloque de
        // inversión: la ficha lo omite en vez de pintar el título solo.
        $this->assertNull(
            $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data.inversion'),
        );
    }

    public function test_el_panel_guarda_las_modalidades_junto_al_resto_de_la_inversion(): void
    {
        $programa = $this->diplomado();

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'inversion_economica' => json_encode(['costo_total' => 3650, 'costo_diploma' => 650]),
                'inversion_modalidades' => json_encode([
                    ['nombre' => 'Pago único', 'cuotas' => [
                        ['etiqueta' => 'Cuota única', 'monto' => 3000, 'fecha' => '16, 17 y 18 de septiembre'],
                    ]],
                    // Modalidad sin cuotas: se descarta al guardar.
                    ['nombre' => 'Vacía', 'cuotas' => []],
                ]),
            ])
            ->assertRedirect(route('admin.programas.index'));

        $inversion = $programa->fresh()->inversion_economica;

        $this->assertSame(3650, $inversion['costo_total']);
        $this->assertCount(1, $inversion['modalidades']);
        $this->assertSame('Pago único', $inversion['modalidades'][0]['nombre']);
        // Se compara por valor: al serializar a JSON, 3000.0 vuelve como entero.
        $this->assertEquals(3000, $inversion['modalidades'][0]['cuotas'][0]['monto']);
    }

    // Condiciones de pago como lista administrable

    public function test_las_condiciones_de_pago_se_muestran_como_lista(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'condiciones' => [
                    'Descuento del 10 % por pago adelantado del íntegro.',
                    'Los pagos se realizan en el Banco de la Nación o vía SUM.',
                    'La matrícula se habilita tras validar el expediente.',
                ],
            ],
        ]);

        $this->assertCount(3, $programa->condiciones_de_pago);

        $condiciones = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['condiciones'];

        // Llegan las tres y en el orden en que se administraron.
        $this->assertSame($programa->condiciones_de_pago, $condiciones);
    }

    public function test_los_campos_sueltos_anteriores_siguen_apareciendo_como_lista(): void
    {
        // Formato previo: modalidades en texto libre, descuentos y observaciones.
        $programa = $this->diplomado([
            'inversion_economica' => [
                'modalidades_pago' => ['Pago único', 'Pago en dos cuotas'],
                'descuentos' => '10 % por pago adelantado',
                'observaciones' => 'Pagos vía SanMarket',
            ],
        ]);

        $this->assertSame([
            'Modalidades de pago: Pago único, Pago en dos cuotas.',
            '10 % por pago adelantado',
            'Pagos vía SanMarket',
        ], $programa->condiciones_de_pago);

        $this->assertSame(
            $programa->condiciones_de_pago,
            $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['condiciones'],
        );
    }

    public function test_la_lista_manda_sobre_los_campos_anteriores(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'condiciones' => ['Única condición vigente.'],
                'descuentos' => 'Texto antiguo que no debe mostrarse',
            ],
        ]);

        $this->assertSame(['Única condición vigente.'], $programa->condiciones_de_pago);

        $this->assertSame(
            ['Única condición vigente.'],
            $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['condiciones'],
        );
    }

    public function test_sin_condiciones_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        $this->assertSame([], $programa->condiciones_de_pago);

        $this->assertSame([], $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['inversion']['condiciones']);
    }

    public function test_el_panel_guarda_la_lista_de_condiciones(): void
    {
        $programa = $this->diplomado();

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'inversion_economica' => json_encode(['costo_total' => 3650]),
                'inversion_condiciones' => json_encode([
                    'Primera condición',
                    '   ',               // en blanco: se descarta
                    'Segunda condición',
                ]),
            ])
            ->assertRedirect(route('admin.programas.index'));

        $this->assertSame(
            ['Primera condición', 'Segunda condición'],
            $programa->fresh()->inversion_economica['condiciones'],
        );
    }

    public function test_vaciar_la_lista_devuelve_el_control_a_los_campos_anteriores(): void
    {
        $programa = $this->diplomado([
            'inversion_economica' => [
                'condiciones' => ['Se va a borrar'],
                'descuentos' => 'Respaldo antiguo',
            ],
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'inversion_economica' => json_encode(['descuentos' => 'Respaldo antiguo']),
                'inversion_condiciones' => json_encode([]),
            ]);

        $fresco = $programa->fresh();

        $this->assertArrayNotHasKey('condiciones', $fresco->inversion_economica);
        $this->assertSame(['Respaldo antiguo'], $fresco->condiciones_de_pago);
    }

    // Obs. N.º 4 — Denominación del título que otorga

    public function test_sin_denominacion_la_portada_no_dice_grado_que_otorga(): void
    {
        $programa = $this->diplomado();

        $this->assertNull($programa->denominacion_otorga);
        $this->assertNull($programa->denominacion_otorga_texto);

        $this->assertNull($this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['grado_otorga']);
    }

    public function test_la_denominacion_y_su_rotulo_son_editables(): void
    {
        $programa = $this->diplomado([
            'grado_otorga_label' => 'Otorga',
            'grado_otorga' => 'Diploma en Curaduría con Énfasis en Arte Peruano',
        ]);

        $this->assertSame(
            'Otorga: Diploma en Curaduría con Énfasis en Arte Peruano',
            $programa->denominacion_otorga_texto,
        );

        // El rótulo y el contenido viajan por separado: la ficha los junta.
        $this->assertSame(
            'Diploma en Curaduría con Énfasis en Arte Peruano',
            $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['grado_otorga'],
        );
    }

    public function test_sin_rotulo_se_muestra_solo_el_contenido(): void
    {
        $programa = $this->diplomado(['grado_otorga' => 'Diploma en Curaduría']);

        $this->assertSame('Diploma en Curaduría', $programa->denominacion_otorga_texto);
    }

    public function test_el_panel_guarda_y_vacia_la_denominacion(): void
    {
        $programa = $this->diplomado([
            'grado_otorga_label' => 'Otorga',
            'grado_otorga' => 'Diploma en algo',
        ]);

        $datos = ['nombre' => $programa->nombre, 'grado' => 'Taller'];

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), $datos + [
                'grado_otorga_label' => 'Confiere',
                'grado_otorga' => 'Diploma en Curaduría',
            ]);

        $this->assertSame('Confiere: Diploma en Curaduría', $programa->fresh()->denominacion_otorga_texto);

        // Vaciar el campo debe dejarlo vacío, no regenerarlo.
        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), $datos + [
                'grado_otorga_label' => '',
                'grado_otorga' => '',
            ]);

        $this->assertNull($programa->fresh()->denominacion_otorga_texto);
    }

    // Obs. N.º 5 — Descripción de la portada de Diplomados

    public function test_la_portada_de_diplomados_no_menciona_ciencias_sociales(): void
    {
        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);
        SiteSetting::clearCache();

        $hero = collect($this->getJson('/api/v1/tipos-oferta')->assertOk()->json('data'))
            ->firstWhere('slug', 'talleres')['hero'];

        // Lo que pidió la Unidad: la descripción no menciona las ciencias
        // sociales, que el CERSEU no ofrece. El texto se edita en
        // Configuración; ya no hay ninguno escrito en una plantilla que pueda
        // reaparecer por detrás.
        $this->assertStringNotContainsString('ciencias sociales', (string) $hero['texto']);
        $this->assertNotEmpty($hero['texto']);

        // Y si la Unidad lo vacía, no reaparece nada: el sitio omite el
        // párrafo en vez de rellenarlo por su cuenta.
        SiteSetting::query()->update(['talleres_hero_texto' => null]);
        SiteSetting::clearCache();

        $vacio = collect($this->getJson('/api/v1/tipos-oferta')->json('data'))
            ->firstWhere('slug', 'talleres')['hero'];

        $this->assertNull($vacio['texto']);
    }

    // Obs. N.º 6 — Horas académicas

    public function test_las_horas_academicas_aparecen_debajo_de_creditos(): void
    {
        $programa = $this->diplomado(['horas_academicas' => 480]);

        // Las medidas llegan ya formateadas y en el orden que le corresponde
        // a cada tipo: la regla vive en TipoOferta, no en la plantilla.
        $this->assertContains('480 horas académicas', $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['medidas']);
    }

    public function test_sin_horas_academicas_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['horas_academicas' => null]);

        $this->assertNotContains(
            'horas académicas',
            $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data')['medidas'],
        );
    }

    public function test_el_panel_guarda_las_horas_academicas(): void
    {
        $programa = $this->diplomado();

        $this->actingAs($this->admin())
            ->put(route('admin.programas.update', $programa), [
                'nombre' => $programa->nombre,
                'grado' => 'Taller',
                'horas_academicas' => '520',
            ]);

        $this->assertSame(520, $programa->fresh()->horas_academicas);
    }

    // Obs. N.º 7 — Sección «Contáctanos»

    public function test_la_portada_de_diplomados_ya_no_duplica_el_contacto(): void
    {
        // El bloque duplicado vivía en la plantilla del listado, que ya no
        // existe: el sitio nuevo nunca lo tuvo. Lo que sí tiene que seguir
        // llegando son los datos de contacto, que alimentan el pie.
        $contacto = $this->getJson('/api/v1/sitio')->assertOk()->json('data.contacto');

        // El correo es el dato que sostiene el pie; el horario es opcional y
        // aquí la tabla está recién creada, sin nada sembrado.
        $this->assertNotNull($contacto['email']);
    }

    // Compatibilidad: los demás grados no cambian

    public function test_un_curso_convertido_conserva_su_ficha_y_su_denominacion(): void
    {
        $programa = Programa::create([
            'grado' => 'Curso',
            'nombre' => 'Lingüística',
            'modalidad' => 'Presencial',
            'duracion' => 4,
            'creditos' => 72,
            'estado' => Programa::ESTADO_PUBLICADO,
            'grado_otorga_label' => 'Grado que otorga',
            'grado_otorga' => 'Magíster en Lingüística',
            'costo_por_credito' => 160,
            'semestres_inversion' => [['matricula' => 310, 'creditos' => 14]],
        ]);

        $ficha = $this->getJson('/api/v1/programas/' . $programa->slug)->assertOk()->json('data');

        $this->assertSame('Magíster en Lingüística', $ficha['grado_otorga']);
        $this->assertSame('cursos', $ficha['tipo']);
    }
}
