<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\DirectorioCerseu;

class NosotrosController extends Controller
{
    public function index()
    {
        // Misión, visión y valores se editan en el panel (Contenido →
        // Nosotros). Los textos que estaban escritos aquí quedan de respaldo
        // por si la página aún no se ha creado en una instalación nueva.
        $pagina = ContentPage::porSlug('nosotros');

        $mision = $pagina?->seccionesDe('mision')->first()?->cuerpo_renderizado
            ?: '<p>Promover, coordinar y ejecutar acciones de responsabilidad social universitaria, articulando la formación académica, la investigación y la extensión cultural al servicio de la sociedad peruana.</p>';

        $vision = $pagina?->seccionesDe('vision')->first()?->cuerpo_renderizado
            ?: '<p>Consolidarse como referente nacional e internacional en responsabilidad social universitaria, garantizando el acceso democrático al saber humanístico y científico.</p>';

        $valores = $pagina?->seccionesDe('valor')->pluck('titulo')->all() ?: [
            'Responsabilidad social universitaria',
            'Interculturalidad y diálogo con las comunidades',
            'Acceso democrático al conocimiento',
            'Pensamiento crítico',
            'Justicia y equidad',
            'Compromiso con el desarrollo sostenible',
        ];

        $autoridades = DirectorioCerseu::activos()
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
                ?: 'La misión, la visión y los valores que guían al CERSEU de la Facultad de Letras y Ciencias Humanas.',
        ]);
    }
}
