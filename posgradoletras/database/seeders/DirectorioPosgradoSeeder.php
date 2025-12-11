<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DirectorioPosgrado;

class DirectorioPosgradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directorio = [
            // AUTORIDADES
            [
                'unidad_nombre' => 'AUTORIDADES',
                'cargo' => 'Vicedecana de Investigación y Posgrado',
                'nombre_persona' => 'Dra. María Jacqueline Oyarce Cruz',
                'correo_persona' => 'vip.letras@unmsm.edu.pe',
                'anexo' => '2803',
                'orden' => 1,
            ],
            [
                'unidad_nombre' => 'AUTORIDADES',
                'cargo' => 'Directora de la Unidad de Posgrado',
                'nombre_persona' => 'Dra. María Jacqueline Oyarce Cruz',
                'correo_persona' => null,
                'anexo' => null,
                'orden' => 2,
            ],
            [
                'unidad_nombre' => 'AUTORIDADES',
                'cargo' => 'Secretaria Académica Docente',
                'nombre_persona' => 'Mg. Patricia Ciriani Espejo',
                'correo_persona' => 'upg.letras@unmsm.edu.pe',
                'anexo' => '956288383',
                'orden' => 3,
            ],
            [
                'unidad_nombre' => 'AUTORIDADES',
                'cargo' => 'Director de la Unidad de Investigación y Posgrado',
                'nombre_persona' => 'Dra. Veronica Matilde Sanchez Montenegro',
                'correo_persona' => 'unidadinvestigacion.letras@unmsm.edu.pe',
                'anexo' => '2827',
                'orden' => 4,
            ],

            // PERSONAL ADMINISTRATIVO
            [
                'unidad_nombre' => 'PERSONAL ADMINISTRATIVO',
                'cargo' => 'Secretaria de Vicedecanato de Investigación y Posgrado',
                'nombre_persona' => 'Sra. Joseline Isabel Milla Hidones',
                'correo_persona' => 'direccionposgrado.letras@unmsm.edu.pe',
                'anexo' => '2803',
                'orden' => 1,
            ],
            [
                'unidad_nombre' => 'PERSONAL ADMINISTRATIVO',
                'cargo' => 'Secretaria de la Unidad de Posgrado – Trámite obtención de Grado Académico',
                'nombre_persona' => 'Sra. Clotilde Montejo Ugaz',
                'correo_persona' => 'upg.letras@unmsm.edu.pe',
                'anexo' => null,
                'orden' => 2,
            ],
            [
                'unidad_nombre' => 'PERSONAL ADMINISTRATIVO',
                'cargo' => 'Secretaria de la Unidad de Investigación',
                'nombre_persona' => 'Srta. Viviana Beatriz Velarde Castillo',
                'correo_persona' => 'unidadinvestigacion.letras@unmsm.edu.pe',
                'anexo' => '2827',
                'orden' => 3,
            ],
            [
                'unidad_nombre' => 'PERSONAL ADMINISTRATIVO',
                'cargo' => 'Secretaria de la Unidad de Posgrado – Gestión Repositorio de Posgrado',
                'nombre_persona' => 'Sra. Verónica Arana Zumarán',
                'correo_persona' => 'repositorioposgrado.flch@unmsm.edu.pe',
                'anexo' => null,
                'orden' => 4,
            ],
            [
                'unidad_nombre' => 'PERSONAL ADMINISTRATIVO',
                'cargo' => 'Secretaria de la Unidad de Posgrado – Gestión de cobranzas y pagos de estudiantes',
                'nombre_persona' => 'Srta. Antoinettee Ramirez Huicho',
                'correo_persona' => 'posgradoletraspagos.flch@unmsm.edu.pe',
                'anexo' => null,
                'orden' => 5,
            ],
        ];

        foreach ($directorio as $persona) {
            DirectorioPosgrado::create($persona);
        }
    }
}
