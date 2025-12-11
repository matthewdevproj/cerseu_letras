<?php

namespace App\Http\Controllers;

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

        $autoridades = [
            [
                'nombre' => 'Dr. Marco Martos Carrera',
                'cargo' => 'Director de la Unidad de Posgrado',
                'email' => 'mmartos@unmsm.edu.pe',
            ],
            [
                'nombre' => 'Dra. Dorian Espezúa Salmón',
                'cargo' => 'Coordinador Académico',
                'email' => 'despezua@unmsm.edu.pe',
            ],
            [
                'nombre' => 'Mg. Carlos García Miranda',
                'cargo' => 'Coordinador de Investigación',
                'email' => 'cgarciam@unmsm.edu.pe',
            ],
        ];

        return view('nosotros.index', compact('mision', 'vision', 'valores', 'autoridades'));
    }
}
