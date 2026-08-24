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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Coordinador del Curso');
    }

    public function test_la_denominacion_puede_ser_coordinadora_en_cada_programa(): void
    {
        $programa = $this->diplomado();
        $this->coordinador($programa, 'Coordinadora');

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Coordinadora del Curso')
            ->assertDontSee('Coordinador del Curso');
    }

    public function test_no_queda_la_etiqueta_que_repetia_la_denominacion(): void
    {
        $programa = $this->diplomado();
        $this->coordinador($programa, 'Coordinadora');

        $html = $this->get($programa->url)->assertOk()->getContent();

        // El encabezado aparece una sola vez y ya no le acompaña la etiqueta
        // con la estrella que repetía la denominación.
        $this->assertSame(1, substr_count($html, 'Coordinadora del Curso'));
        $this->assertStringNotContainsString('Coordinadora</span>', $html);
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

        $html = $this->get($programa->url)->assertOk()->getContent();

        $posiciones = [
            'Costo total del taller' => strpos($html, 'Costo total del taller'),
            'Modalidades de pago' => strpos($html, 'Modalidades de pago'),
            'Pago de diploma' => strpos($html, 'Pago de diploma'),
            'Condiciones de pago' => strpos($html, 'Condiciones de pago'),
            'Informes' => strpos($html, 'Informes'),
        ];

        foreach ($posiciones as $bloque => $posicion) {
            $this->assertNotFalse($posicion, "No se encontró el bloque «{$bloque}»");
        }

        $this->assertSame(array_keys($posiciones), array_keys(collect($posiciones)->sort()->all()));
    }

    public function test_el_costo_total_lleva_la_nota_de_lo_que_incluye(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('3,650')
            ->assertSee('Incluye la totalidad de los derechos de enseñanza y el costo del diploma.');
    }

    public function test_el_pago_de_diploma_dice_costo_del_diploma_y_su_plazo(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_diploma' => 650]]);

        $html = $this->get($programa->url)->assertOk()->getContent();

        $this->assertStringContainsString('Costo del diploma', $html);
        $this->assertStringContainsString(
            'El pago por derecho de diploma deberá efectuarse dentro de los cinco días hábiles posteriores',
            $html,
        );
        // El rótulo «Costo total» pertenece al bloque del costo total, que aquí
        // no existe: no debe reaparecer bajo el importe del diploma.
        $this->assertStringNotContainsString('Costo total', $html);
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

        $html = $this->get($programa->url)->assertOk()->getContent();

        $this->assertStringContainsString('Costo por matrícula', $html);
        $this->assertStringContainsString('S/&nbsp;200', $html);
        $this->assertLessThan(
            strpos($html, 'Costo por matrícula'),
            strpos($html, 'Pago de diploma'),
            'El costo por matrícula debe ir después del bloque de pago de diploma',
        );
        // Y antes de las condiciones, que cierran el apartado junto a Informes.
        $this->assertLessThan(
            strpos($html, 'Informes'),
            strpos($html, 'Costo por matrícula'),
        );
    }

    public function test_sin_costo_por_matricula_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Costo por matrícula');
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

        $html = $this->get($programa->url)->assertOk()->getContent();

        foreach (['Pago único', 'Cuota única', '3,000', '16, 17 y 18 de septiembre',
                  'Pago fraccionado', 'Cuota 1', 'Cuota 2', '1,500',
                  'Del 16 al 18 de septiembre', 'Hasta el 30 de noviembre'] as $texto) {
            $this->assertStringContainsString($texto, $html, "Falta «{$texto}»");
        }

        // La fecha se imprime después del monto dentro de cada cuota.
        $this->assertLessThan(
            strpos($html, '16, 17 y 18 de septiembre'),
            strpos($html, '3,000'),
        );
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Cuota 3')
            ->assertSee('Noviembre');
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('1,500')
            ->assertSee('Marzo');
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

        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Modalidades de pago');
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

        $html = $this->get($programa->url)->assertOk()->getContent();

        $this->assertStringContainsString('Condiciones de pago', $html);
        foreach ($programa->condiciones_de_pago as $condicion) {
            $this->assertStringContainsString(e($condicion), $html);
        }
        // Se respeta el orden en que se administraron.
        $this->assertLessThan(
            strpos($html, 'La matrícula se habilita'),
            strpos($html, 'Descuento del 10'),
        );
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('10 % por pago adelantado')
            ->assertSee('Pagos vía SanMarket');
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Única condición vigente.')
            ->assertDontSee('Texto antiguo que no debe mostrarse');
    }

    public function test_sin_condiciones_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['inversion_economica' => ['costo_total' => 3650]]);

        $this->assertSame([], $programa->condiciones_de_pago);

        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Condiciones de pago');
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

        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Grado que otorga');
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Otorga: Diploma en Curaduría con Énfasis en Arte Peruano');
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
        SiteSetting::query()->update(['talleres_hero_texto' => null]);
        SiteSetting::clearCache();

        $this->get(route('talleres.index'))
            ->assertOk()
            ->assertDontSee('ciencias sociales')
            ->assertSee('Especializa tus conocimientos con talleres diseñados para responder a los desafíos contemporáneos desde las humanidades y las nuevas tecnologías.');
    }

    // Obs. N.º 6 — Horas académicas

    public function test_las_horas_academicas_aparecen_debajo_de_creditos(): void
    {
        $programa = $this->diplomado(['horas_academicas' => 480]);

        $html = $this->get($programa->url)->assertOk()->getContent();

        $this->assertStringContainsString('Horas académicas', $html);
        $this->assertStringContainsString('480 horas', $html);
        $this->assertLessThan(strpos($html, 'Horas académicas'), strpos($html, 'Créditos'));
        $this->assertLessThan(strpos($html, 'Duración'), strpos($html, 'Horas académicas'));
    }

    public function test_sin_horas_academicas_no_se_muestra_el_bloque(): void
    {
        $programa = $this->diplomado(['horas_academicas' => null]);

        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Horas académicas');
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
        $html = $this->get(route('talleres.index'))->assertOk()->getContent();

        $this->assertStringNotContainsString('| Contáctanos', $html);
        $this->assertStringNotContainsString('Horario de atención', $html);

        // El pie de página sigue siendo el punto de contacto.
        $this->assertStringContainsString('Enlaces Rápidos', $html);
        $this->assertStringContainsString('Institucional', $html);
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

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Grado que otorga: Magíster en Lingüística')
            ->assertSee('Cursos')
            ->assertSee('Postular Ahora');
    }
}
