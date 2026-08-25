<?php

namespace Tests\Feature;

use App\Models\TipoOferta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Prueba de humo del panel: recorre TODAS sus pantallas GET con un admin
 * autenticado y comprueba que ninguna revienta.
 *
 * No sustituye a las pruebas de comportamiento; sirve para detectar lo que
 * esas no ven: una vista que dejó de compilar, un controlador que quedó
 * pasando una variable que la plantilla ya no usa, o una ruta que se quedó
 * apuntando a un método borrado.
 */
class PanelHumoTest extends TestCase
{
    use RefreshDatabase;

    public function test_todas_las_pantallas_del_panel_responden(): void
    {
        Artisan::call('db:seed');

        $admin = User::factory()->create(['role' => 'admin']);

        // Un id real por cada recurso con parámetro; si la tabla está vacía se
        // omite esa ruta en lugar de inventar un id que daría un 404 legítimo.
        $ids = [
            'anuncio' => \App\Models\Anuncio::query()->value('id'),
            'directorio' => \DB::table('directorio_cerseu')->value('id'),
            'docente' => \App\Models\Docente::query()->value('id'),
            'document' => \App\Models\Document::query()->value('id'),
            'evento' => \DB::table('eventos')->value('id'),
            'informativo' => \DB::table('informativos')->value('id'),
            'programa' => \App\Models\Programa::query()->value('id'),
            'testimonio' => \DB::table('testimonios')->value('id'),
            'user' => $admin->id,
            'tipoOferta' => TipoOferta::Taller->slug(),
            'slug' => 'tramites',
        ];

        $revisadas = 0;
        $fallos = [];

        foreach (Route::getRoutes() as $ruta) {
            if (! in_array('GET', $ruta->methods(), true)) {
                continue;
            }

            $uri = $ruta->uri();
            if (! str_starts_with($uri, 'admin')) {
                continue;
            }

            // Exportar solicitudes devuelve un CSV; se comprueba aparte.
            if (str_contains($uri, 'leads/export')) {
                continue;
            }

            $destino = $uri;
            $omitir = false;

            foreach ($ruta->parameterNames() as $parametro) {
                $valor = $ids[$parametro] ?? null;
                if ($valor === null) {
                    $omitir = true;
                    break;
                }
                $destino = preg_replace('/\{' . $parametro . '\??\}/', (string) $valor, $destino);
            }

            if ($omitir) {
                continue;
            }

            $respuesta = $this->actingAs($admin)->get('/' . $destino);
            $revisadas++;

            if ($respuesta->status() >= 400) {
                $fallos[] = $destino . ' → ' . $respuesta->status() . ' :: '
                    . ($respuesta->exception?->getMessage() ?? '(sin excepcion)')
                    . ' @ ' . basename($respuesta->exception?->getFile() ?? '?') . ':' . ($respuesta->exception?->getLine() ?? '?');
            }
        }

        $this->assertGreaterThan(20, $revisadas, 'Se recorrieron muy pocas pantallas; la deteccion de rutas falla.');
        $this->assertSame([], $fallos, "Pantallas del panel que no responden:\n" . implode("\n", $fallos));
    }

    public function test_la_admision_de_cada_tipo_tiene_su_pantalla(): void
    {
        Artisan::call('db:seed');
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (TipoOferta::cases() as $tipo) {
            $this->actingAs($admin)
                ->get('/admin/admision/' . $tipo->slug())
                ->assertOk();
        }
    }

    public function test_la_exportacion_de_solicitudes_devuelve_un_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $respuesta = $this->actingAs($admin)->get('/admin/leads/export');

        $respuesta->assertOk();
        $this->assertStringContainsString('csv', strtolower((string) $respuesta->headers->get('content-type')));
    }
}
