<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use App\Models\CronogramaAdmision;
use Illuminate\Database\Seeder;

/**
 * Contenido de /tramites, /admision, /nosotros y el cronograma de la portada.
 *
 * Sin esto, una instalación nueva arranca con esas páginas **en blanco**: el
 * texto no está en las vistas —es administrable desde el panel— y no había
 * ningún seeder que lo cargara. El contenido va en
 * `database/seeders/data/contenido-inicial.json`.
 *
 * No pisa nada: si la página ya tiene secciones, se deja como está. Así se
 * puede ejecutar sobre una base que ya lleva contenido editado sin destruirlo.
 */
class ContenidoInicialSeeder extends Seeder
{
    public function run(): void
    {
        $ruta = database_path('seeders/data/contenido-inicial.json');

        if (! is_file($ruta)) {
            $this->command?->warn('Falta contenido-inicial.json; no se cargó el contenido de las páginas.');

            return;
        }

        $datos = json_decode((string) file_get_contents($ruta), true);

        if (! is_array($datos)) {
            $this->command?->error('contenido-inicial.json está corrupto.');

            return;
        }

        $this->paginas($datos);
        $this->cronograma($datos['cronograma'] ?? []);
    }

    private function paginas(array $datos): void
    {
        foreach ($datos['content_pages'] ?? [] as $pagina) {
            ContentPage::firstOrCreate(
                ['slug' => $pagina['slug']],
                ['titulo' => $pagina['titulo'], 'subtitulo' => $pagina['subtitulo'] ?? null]
            );
        }

        $paginas = ContentPage::with('secciones')->get()->keyBy('slug');

        // Qué páginas tienen ya contenido editado. Se calcula **antes** del
        // bucle: comprobarlo dentro hacía que, tras crear la primera sección,
        // el resto de esa misma página se saltara.
        $yaTienenContenido = $paginas->filter(fn ($p) => $p->secciones->isNotEmpty())->keys()->all();

        $cargadas = 0;

        foreach ($datos['content_sections'] ?? [] as $seccion) {
            $pagina = $paginas[$seccion['pagina']] ?? null;

            if (! $pagina || in_array($seccion['pagina'], $yaTienenContenido, true)) {
                continue;
            }

            $pagina->secciones()->create([
                'grupo' => $seccion['grupo'] ?? null,
                'numeral' => $seccion['numeral'] ?? null,
                'titulo' => $seccion['titulo'],
                'cuerpo' => $seccion['cuerpo'] ?? null,
                'orden' => $seccion['orden'] ?? 0,
                'is_visible' => $seccion['is_visible'] ?? true,
            ]);

            $cargadas++;
        }

        ContentPage::clearCache();

        $this->command?->info("Contenido de páginas: {$cargadas} secciones.");
    }

    private function cronograma(array $cronogramas): void
    {
        if (CronogramaAdmision::query()->exists()) {
            return;
        }

        foreach ($cronogramas as $datos) {
            $cronograma = CronogramaAdmision::create([
                'eyebrow' => $datos['eyebrow'] ?? null,
                'titulo' => $datos['titulo'],
                'boton_texto' => $datos['boton_texto'] ?? null,
                'boton_url' => $datos['boton_url'] ?? null,
                'is_visible' => $datos['is_visible'] ?? true,
            ]);

            foreach ($datos['pasos'] ?? [] as $paso) {
                $cronograma->pasos()->create($paso);
            }

            $this->command?->info('Cronograma de admisión: ' . count($datos['pasos'] ?? []) . ' etapas.');
        }
    }
}
