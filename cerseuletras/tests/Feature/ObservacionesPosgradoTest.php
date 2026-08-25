<?php

namespace Tests\Feature;

use App\Models\CronogramaAdmision;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre las observaciones del documento de requerimientos de Posgrado
 * ("Ajustes para la página web de Diplomados").
 */
class ObservacionesPosgradoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * Obs. N.º 1 — la portada lleva a la oferta y a su inscripción.
     *
     * La observación original pedía que los accesos apuntaran a diplomados,
     * que es lo que entonces ofrecía la Unidad. Se conserva la intención
     * —que el visitante aterrice en algo que existe— con el destino que hoy
     * tiene contenido: Cursos, la única de las tres secciones con programas
     * publicados. Talleres y Especializaciones siguen vacías.
     */
    public function test_la_portada_ofrece_los_accesos_a_la_oferta(): void
    {
        $res = $this->get('/');

        $res->assertOk()
            ->assertSee('Ver cursos')
            ->assertSee('Cómo inscribirte')
            ->assertDontSee('>Ver Programas<', false);

        // Los indicadores de Maestrías/Doctorados/Diplomados enlazan a su sección.
        $res->assertSee('stats-link', false);
    }

    /** Obs. N.º 2 — la sección del cronograma es editable y ocultable. */
    public function test_el_cronograma_de_admision_se_administra_y_se_puede_ocultar(): void
    {
        // La sección es un registro único (la migración ya lo siembra), así que
        // se adapta el existente: es justo lo que hace el panel.
        $cronograma = CronogramaAdmision::firstOrFail();
        $cronograma->update([
            'eyebrow' => 'Proceso de Admisión de Diplomados 2026-II',
            'titulo' => 'Cronograma de Admisión',
            'boton_texto' => 'Iniciar inscripción',
            'boton_url' => 'https://ejemplo.test/inscripcion',
            'is_visible' => true,
        ]);
        $cronograma->pasos()->delete();
        $cronograma->pasos()->create([
            'titulo' => 'Inscripción de postulantes',
            'fecha_inicio' => '5 ene',
            'fecha_fin' => '02 abr',
            'detalle' => '+ Envío de expediente',
            'icono' => 'inscripcion',
            'orden' => 0,
            'destacado' => true,
            'is_visible' => true,
        ]);
        CronogramaAdmision::clearCache();

        $this->get('/')
            ->assertOk()
            ->assertSee('Proceso de Admisión de Diplomados 2026-II')
            ->assertSee('Inscripción de postulantes')
            ->assertSee('5 ene - 02 abr')
            ->assertSee('https://ejemplo.test/inscripcion', false);

        // Ocultar la sección completa cuando no hay convocatoria activa.
        $cronograma->update(['is_visible' => false]);
        CronogramaAdmision::clearCache();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Proceso de Admisión de Diplomados 2026-II');
    }

    /** Obs. N.º 2 — el panel guarda encabezado, etapas, orden y botón. */
    public function test_el_panel_guarda_el_cronograma_completo(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.cronograma-admision.index'))
            ->assertOk()
            ->assertSee('Cronograma de Admisión');

        $payload = [
            ['id' => null, 'titulo' => 'Examen', 'fecha_inicio' => '06 de abril', 'fecha_fin' => '',
             'detalle' => '', 'publico' => 'Maestrías', 'icono' => 'examen', 'destacado' => false, 'is_visible' => true],
            ['id' => null, 'titulo' => 'Resultados', 'fecha_inicio' => '09 de abril', 'fecha_fin' => '',
             'detalle' => 'Lista oficial', 'publico' => '', 'icono' => 'check', 'destacado' => true, 'is_visible' => true],
        ];

        $this->actingAs($this->admin())
            ->put(route('admin.cronograma-admision.update'), [
                'eyebrow' => 'Proceso de Admisión de Diplomados 2026-II',
                'titulo' => 'Cronograma de Diplomados',
                'boton_texto' => 'Postular',
                'boton_url' => '/diplomados/admision',
                'is_visible' => '1',
                'pasos_payload' => json_encode($payload),
            ])
            ->assertRedirect(route('admin.cronograma-admision.index'));

        $cronograma = CronogramaAdmision::first();
        $this->assertSame('Cronograma de Diplomados', $cronograma->titulo);
        $this->assertTrue($cronograma->is_visible);

        $pasos = $cronograma->pasos()->get();
        $this->assertCount(2, $pasos);
        // El orden se toma de la posición en el payload.
        $this->assertSame('Examen', $pasos[0]->titulo);
        $this->assertSame(0, $pasos[0]->orden);
        $this->assertSame('Resultados', $pasos[1]->titulo);
        $this->assertSame(1, $pasos[1]->orden);
        $this->assertTrue($pasos[1]->destacado);

        // Un ícono inválido cae al de respaldo en lugar de romper la vista.
        $this->actingAs($this->admin())->put(route('admin.cronograma-admision.update'), [
            'pasos_payload' => json_encode([
                ['id' => null, 'titulo' => 'Etapa', 'icono' => '<script>', 'is_visible' => true],
            ]),
        ]);
        $this->assertSame('documento', CronogramaAdmision::first()->pasos()->first()->icono);
    }

    /** Obs. N.º 3 — filtros reordenados y "Diplomados" activo por defecto. */
    public function test_los_filtros_priorizan_talleres(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $orden = [];
        preg_match_all('/data-filter="([a-z]+)"/', $html, $m);
        $orden = $m[1];

        $this->assertSame(['taller', 'curso', 'especializacion', 'todos'], $orden);
        $this->assertStringContainsString(
            'data-filter="taller" id="filter-taller" aria-pressed="true"',
            $html,
        );
    }

    /** Obs. N.º 4 — botón Brochure antes de Postular, oculto si no hay archivo. */
    public function test_el_brochure_aparece_antes_de_postular_y_se_oculta_sin_archivo(): void
    {
        $programa = Programa::create([
            'grado' => 'Taller',
            'nombre' => 'Diplomado de Prueba',
            'modalidad' => 'Virtual',
            'duracion' => 2,
            'creditos' => 12,
            'is_active' => true,
            'brochure_url' => 'documents/brochure.pdf',
        ]);

        $html = $this->get($programa->url)->assertOk()->getContent();

        $posBrochure = strpos($html, 'Brochure');
        $posPostular = strpos($html, 'Postular Ahora');
        $this->assertNotFalse($posBrochure, 'No se encontró el botón Brochure');
        $this->assertLessThan($posPostular, $posBrochure, 'Brochure debe ir antes de Postular Ahora');
        $this->assertStringContainsString('storage/documents/brochure.pdf', $html);

        // Sin brochure el botón desaparece por completo.
        $programa->update(['brochure_url' => null]);
        $this->get($programa->url)
            ->assertOk()
            ->assertDontSee('Brochure');
    }

    /** Obs. N.º 5 — buscador: resultados con categoría, relevancia y vacío. */
    public function test_el_buscador_devuelve_resultados_por_relevancia(): void
    {
        Programa::create([
            'grado' => 'Taller', 'nombre' => 'Taller en Lingüística Forense',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12, 'is_active' => true,
        ]);

        $res = $this->getJson('/buscar/sugerencias?q=linguistica')->assertOk();
        $primero = $res->json('resultados.0');

        $this->assertStringContainsString('Lingüística Forense', $primero['titulo']);
        $this->assertSame('Talleres', $primero['categoria']);
        $this->assertArrayHasKey('url', $primero);
        // Los campos internos de puntuación no deben filtrarse al JSON.
        $this->assertArrayNotHasKey('_titulo_norm', $primero);

        // Sin acentos también encuentra ("admision" → "Admisión").
        $this->assertGreaterThan(0, $this->getJson('/buscar/sugerencias?q=admision')->json('total'));

        // Sin coincidencias: respuesta vacía y mensaje en la página.
        $this->getJson('/buscar/sugerencias?q=zzzqqq')->assertOk()->assertJsonPath('total', 0);
        $this->get('/buscar?q=zzzqqq')->assertOk()->assertSee('No encontramos resultados');

        // La página de resultados agrupa por categoría.
        $this->get('/buscar?q=linguistica')->assertOk()->assertSee('Talleres');
    }

    /** Obs. N.º 4 — el enlace del brochure no depende del entorno donde se subió. */
    public function test_el_brochure_se_reancla_al_host_actual(): void
    {
        $programa = new Programa();

        // Subido en local: el host quedó congelado en la base de datos.
        $programa->brochure_url = 'http://127.0.0.1:8123/storage/documents/folleto.pdf';
        $this->assertSame(asset('storage/documents/folleto.pdf'), $programa->brochure_link);

        // Ruta relativa: se resuelve igual.
        $programa->brochure_url = 'documents/folleto.pdf';
        $this->assertSame(asset('storage/documents/folleto.pdf'), $programa->brochure_link);

        // Enlace externo: se respeta sin tocarlo.
        $externo = 'https://drive.google.com/file/d/abc/view';
        $programa->brochure_url = $externo;
        $this->assertSame($externo, $programa->brochure_link);

        // Vacío: no hay botón.
        $programa->brochure_url = '';
        $this->assertNull($programa->brochure_link);
    }

    /** Inversión: los importes salen del panel y hay un único cálculo. */
    public function test_la_inversion_se_calcula_desde_los_datos_del_programa(): void
    {
        $programa = Programa::create([
            'grado' => 'Curso', 'nombre' => 'Maestría con Tarifas',
            'modalidad' => 'Presencial', 'duracion' => 4, 'creditos' => 30,
            'estado' => Programa::ESTADO_PUBLICADO,
            'costo_por_credito' => 100,
            'semestres_inversion' => [
                ['matricula' => 300, 'creditos' => 10],
                ['matricula' => 400, 'creditos' => 20],
            ],
        ]);

        $filas = $programa->semestres_calculados;
        $this->assertCount(2, $filas);
        $this->assertSame(1000, $filas[0]['costo_semestre']);   // 10 créditos × 100
        $this->assertSame(250.0, $filas[0]['cuota_mensual']);   // dividido en 4
        // Total = (300 + 1000) + (400 + 2000)
        $this->assertSame(3700, $programa->costo_total);

        // La ficha muestra ese mismo total, sin recalcularlo por su cuenta.
        $this->get($programa->url)
            ->assertOk()
            ->assertSee('3,700');
    }

    /** Inversión: un importe cerrado (caso diplomados) tiene prioridad. */
    public function test_el_costo_total_cerrado_manda_sobre_el_calculo(): void
    {
        $programa = Programa::create([
            'grado' => 'Taller', 'nombre' => 'Diplomado con Precio Cerrado',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12,
            'estado' => Programa::ESTADO_PUBLICADO,
            'costo_por_credito' => 120,
            'semestres_inversion' => [['matricula' => 200, 'creditos' => 12]],
            'inversion_economica' => ['costo_total' => 3280],
        ]);

        $this->assertSame(3280, $programa->costo_total);
    }

    /** Inversión: sin tarifas cargadas se avisa en vez de mostrar ceros. */
    public function test_sin_tarifas_la_ficha_dice_por_definir(): void
    {
        $programa = Programa::create([
            'grado' => 'Curso', 'nombre' => 'Maestría sin Tarifas',
            'modalidad' => 'Presencial', 'duracion' => 4, 'creditos' => 30,
            'estado' => Programa::ESTADO_PUBLICADO,
            'costo_por_credito' => null,
            'semestres_inversion' => [],
        ]);

        $this->assertNull($programa->costo_total);
        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Por definir');
    }

    /** Estados: borrador responde 404 aunque se conozca la URL. */
    public function test_un_programa_en_borrador_responde_404(): void
    {
        $programa = Programa::create([
            'grado' => 'Taller', 'nombre' => 'Diplomado Secreto',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12,
            'estado' => Programa::ESTADO_BORRADOR,
        ]);

        $this->get($programa->url)->assertNotFound();

        // Tampoco aparece en listados ni en el buscador.
        $this->get('/')->assertOk()->assertDontSee('Diplomado Secreto');
        $this->get(route('talleres.index'))->assertOk()->assertDontSee('Diplomado Secreto');
        $this->getJson('/buscar/sugerencias?q=secreto')->assertJsonPath('total', 0);

        // `is_active` se mantiene sincronizado con el estado.
        $this->assertFalse($programa->fresh()->is_active);
    }

    /** Estados: "próximamente" se anuncia y su ficha muestra el aviso. */
    public function test_un_programa_proximamente_muestra_el_aviso(): void
    {
        $programa = Programa::create([
            'grado' => 'Taller', 'nombre' => 'Diplomado en Archivística',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12,
            'estado' => Programa::ESTADO_PROXIMAMENTE,
        ]);

        // Su página avisa en lugar de mostrar una ficha vacía.
        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Pronto publicaremos la información de este curso')
            ->assertSee('Diplomado en Archivística')
            ->assertSee('noindex', false)
            ->assertDontSee('Postular Ahora');

        // Sí se anuncia en los listados, con su etiqueta.
        $this->get(route('talleres.index'))
            ->assertOk()
            ->assertSee('Diplomado en Archivística')
            ->assertSee('Próximamente');

        $this->assertFalse($programa->fresh()->is_active);
    }

    /** Estados: publicado mantiene el comportamiento completo de siempre. */
    public function test_un_programa_publicado_muestra_su_ficha_completa(): void
    {
        $programa = Programa::create([
            'grado' => 'Taller', 'nombre' => 'Diplomado Vigente',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12,
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $this->get($programa->url)
            ->assertOk()
            ->assertSee('Diplomado Vigente')
            ->assertSee('Postular Ahora')
            ->assertDontSee('Pronto publicaremos la información');

        $this->assertTrue($programa->fresh()->is_active);
    }

    /** Estados: un valor inválido cae a borrador en vez de dejar el programa visible. */
    public function test_un_estado_invalido_cae_a_borrador(): void
    {
        $programa = Programa::create([
            'grado' => 'Curso', 'nombre' => 'Maestría Rara',
            'modalidad' => 'Presencial', 'duracion' => 4, 'creditos' => 40,
            'estado' => 'lo-que-sea',
        ]);

        $this->assertSame(Programa::ESTADO_BORRADOR, $programa->fresh()->estado);
        $this->get($programa->url)->assertNotFound();
    }

    /** Estados: el atajo del panel alterna entre publicado y borrador. */
    public function test_el_panel_alterna_publicado_y_borrador(): void
    {
        $programa = Programa::create([
            'grado' => 'Curso', 'nombre' => 'Maestría Toggle',
            'modalidad' => 'Presencial', 'duracion' => 4, 'creditos' => 40,
            'estado' => Programa::ESTADO_PUBLICADO,
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.programas.toggle', $programa));
        $this->assertSame(Programa::ESTADO_BORRADOR, $programa->fresh()->estado);

        $this->actingAs($admin)->post(route('admin.programas.toggle', $programa));
        $this->assertSame(Programa::ESTADO_PUBLICADO, $programa->fresh()->estado);
    }

    /** Obs. N.º 5 — el índice se refresca al cambiar el contenido, sin esperar al TTL. */
    public function test_el_indice_del_buscador_se_actualiza_al_editar_contenido(): void
    {
        $this->getJson('/buscar/sugerencias?q=paleografia')->assertJsonPath('total', 0);

        Programa::create([
            'grado' => 'Taller', 'nombre' => 'Diplomado en Paleografía',
            'modalidad' => 'Virtual', 'duracion' => 2, 'creditos' => 12, 'is_active' => true,
        ]);

        $this->getJson('/buscar/sugerencias?q=paleografia')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }
}
