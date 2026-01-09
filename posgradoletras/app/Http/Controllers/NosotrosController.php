<?php

namespace App\Http\Controllers;

use App\Models\DirectorioPosgrado;

class NosotrosController extends Controller
{
    public function index()
    {
        $mision = 'Formar profesionales e investigadores de alto nivel académico en el campo de las Letras y las Humanidades, capaces de contribuir al desarrollo cultural, científico y social del país, con una visión crítica, ética y comprometida con la realidad nacional e internacional.';

        $vision = 'Ser reconocidos como el programa de posgrado líder en el ámbito de las Letras y Humanidades en el Perú y Latinoamérica, destacando por la excelencia académica, la investigación de alto impacto y la formación de líderes intelectuales comprometidos con el desarrollo de la sociedad.';

        $valores = [
            'Excelencia académica',
            'Integridad y ética profesional',
            'Compromiso social',
            'Investigación e innovación',
            'Respeto a la diversidad cultural',
            'Responsabilidad y servicio a la comunidad',
        ];

        // Obtener autoridades desde la base de datos
        $autoridades = DirectorioPosgrado::activos()
            ->where('unidad_nombre', 'AUTORIDADES')
            ->orderBy('orden')
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->nombre_persona,
                    'cargo' => $item->cargo,
                    'email' => $item->correo_persona,
                ];
            })
            ->toArray();

        return view('nosotros.index', compact('mision', 'vision', 'valores', 'autoridades'));
    }
}
