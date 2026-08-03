<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\DirectorioPosgrado;

class NosotrosController extends Controller
{
    public function index()
    {
        // Misión, visión y valores se editan en el panel (Contenido →
        // Nosotros). Los textos que estaban escritos aquí quedan de respaldo
        // por si la página aún no se ha creado en una instalación nueva.
        $pagina = ContentPage::porSlug('nosotros');

        $mision = $pagina?->seccionesDe('mision')->first()?->cuerpo_renderizado
            ?: '<p>Formar profesionales e investigadores de alto nivel académico en el campo de las Letras y las Humanidades, capaces de contribuir al desarrollo cultural, científico y social del país, con una visión crítica, ética y comprometida con la realidad nacional e internacional.</p>';

        $vision = $pagina?->seccionesDe('vision')->first()?->cuerpo_renderizado
            ?: '<p>Ser reconocidos como el programa de posgrado líder en el ámbito de las Letras y Humanidades en el Perú y Latinoamérica, destacando por la excelencia académica, la investigación de alto impacto y la formación de líderes intelectuales comprometidos con el desarrollo de la sociedad.</p>';

        $valores = $pagina?->seccionesDe('valor')->pluck('titulo')->all() ?: [
            'Excelencia académica',
            'Integridad y ética profesional',
            'Compromiso social',
            'Investigación e innovación',
            'Respeto a la diversidad cultural',
            'Responsabilidad y servicio a la comunidad',
        ];

        $autoridades = DirectorioPosgrado::activos()
            ->where('unidad_nombre', 'AUTORIDADES')
            ->orderBy('orden')
            ->get()
            ->map(fn ($item) => [
                'nombre' => $item->nombre_persona,
                'cargo' => $item->cargo,
                'email' => $item->correo_persona,
            ])
            ->toArray();

        return view('nosotros.index', [
            'mision' => $mision,
            'vision' => $vision,
            'valores' => $valores,
            'autoridades' => $autoridades,
            'tituloHero' => $pagina?->titulo ?: 'Nosotros',
            'subtituloHero' => $pagina?->subtitulo
                ?: 'La misión, la visión y los valores que guían a la Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas.',
        ]);
    }
}
