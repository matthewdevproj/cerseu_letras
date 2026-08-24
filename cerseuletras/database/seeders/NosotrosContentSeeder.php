<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;

/**
 * Vuelca a la base de datos el texto de /nosotros que estaba escrito en
 * `NosotrosController`. Las autoridades ya salían del directorio, así que aquí
 * solo van misión, visión y valores.
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
            'cuerpo' => '<p>Formar profesionales e investigadores de alto nivel académico en el campo de las Letras y las Humanidades, capaces de contribuir al desarrollo cultural, científico y social del país, con una visión crítica, ética y comprometida con la realidad nacional e internacional.</p>',
        ]);

        $pagina->secciones()->create([
            'grupo' => 'vision', 'titulo' => 'Visión', 'orden' => $orden++, 'is_visible' => true,
            'cuerpo' => '<p>Consolidarse como referente nacional e internacional en responsabilidad social universitaria, garantizando el acceso democrático al saber humanístico y científico.</p>',
        ]);

        foreach ([
            'Excelencia académica',
            'Integridad y ética profesional',
            'Compromiso social',
            'Investigación e innovación',
            'Respeto a la diversidad cultural',
            'Responsabilidad y servicio a la comunidad',
        ] as $valor) {
            $pagina->secciones()->create([
                'grupo' => 'valor', 'titulo' => $valor, 'cuerpo' => null,
                'orden' => $orden++, 'is_visible' => true,
            ]);
        }

        ContentPage::clearCache('nosotros');
    }
}
