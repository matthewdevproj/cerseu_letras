<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;

/**
 * Contenido inicial de /nosotros: misión, visión y valores.
 *
 * Es el texto que estaba escrito dentro de `NosotrosController` como respaldo.
 * Al retirar las vistas de Blade ese respaldo se fue con ellas, así que una
 * instalación nueva depende de este seeder para no abrir la página en blanco;
 * a partir de ahí se edita desde el panel, como el resto del contenido.
 *
 * La misión y los valores que había aquí eran los de la Unidad de Posgrado
 * —«formar profesionales e investigadores de alto nivel»— y no coincidían con
 * lo que el sitio publica: la base de datos ya llevaba los del CERSEU. Un
 * despliegue nuevo habría sembrado la misión de otra unidad.
 *
 * Las autoridades salen del directorio, no de aquí.
 */
class NosotrosContentSeeder extends Seeder
{
    public function run(): void
    {
        $pagina = ContentPage::firstOrCreate(
            ['slug' => 'nosotros'],
            [
                'titulo' => 'Nosotros',
                'subtitulo' => 'La misión, la visión y los valores que guían al CERSEU de la Facultad de Letras y Ciencias Humanas.',
            ]
        );

        if ($pagina->secciones()->exists()) {
            $this->command?->info('/nosotros ya tiene contenido; no se toca.');

            return;
        }

        $orden = 0;

        $pagina->secciones()->create([
            'grupo' => 'mision', 'titulo' => 'Misión', 'orden' => $orden++, 'is_visible' => true,
            'cuerpo' => '<p>Promover, coordinar y ejecutar acciones de responsabilidad social universitaria, articulando la formación académica, la investigación y la extensión cultural al servicio de la sociedad peruana.</p>',
        ]);

        $pagina->secciones()->create([
            'grupo' => 'vision', 'titulo' => 'Visión', 'orden' => $orden++, 'is_visible' => true,
            'cuerpo' => '<p>Consolidarse como referente nacional e internacional en responsabilidad social universitaria, garantizando el acceso democrático al saber humanístico y científico.</p>',
        ]);

        foreach ([
            'Responsabilidad social universitaria',
            'Interculturalidad y diálogo con las comunidades',
            'Acceso democrático al conocimiento',
            'Pensamiento crítico',
            'Justicia y equidad',
            'Compromiso con el desarrollo sostenible',
        ] as $valor) {
            $pagina->secciones()->create([
                'grupo' => 'valor', 'titulo' => $valor, 'cuerpo' => null,
                'orden' => $orden++, 'is_visible' => true,
            ]);
        }

        ContentPage::clearCache('nosotros');
    }
}
