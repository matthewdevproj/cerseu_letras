<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Vuelca el menú que estaba escrito en `navbar.blade.php`.
 *
 * Se conserva tal cual estaba en producción, incluidos los enlaces que caducan
 * por convocatoria: el objetivo de este seeder es que el sitio arranque igual
 * que antes, y a partir de ahí que el equipo los edite desde el panel.
 */
class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::query()->exists()) {
            $this->command?->info('El menú ya tiene elementos; no se toca.');

            return;
        }

        foreach ($this->menu() as $ordenPadre => $padre) {
            $hijos = $padre['hijos'] ?? [];
            unset($padre['hijos']);

            $item = MenuItem::create($padre + ['orden' => $ordenPadre]);

            foreach ($hijos as $ordenHijo => $hijo) {
                MenuItem::create($hijo + [
                    'parent_id' => $item->id,
                    'orden' => $ordenHijo,
                ]);
            }
        }

        MenuItem::clearCache();
    }

    private function menu(): array
    {
        return [
            [
                'etiqueta' => 'Nosotros', 'icono' => 'fas-info-circle',
                'hijos' => [
                    ['etiqueta' => 'Quiénes somos', 'route_name' => 'nosotros', 'icono' => 'fas-info-circle'],
                    ['etiqueta' => 'Directorio FLCH', 'url' => 'https://letras.unmsm.edu.pe/directorio/', 'icono' => 'fas-address-book', 'nueva_pestana' => true],
                    ['etiqueta' => 'Directorio Posgrado', 'route_name' => 'directorio', 'icono' => 'fas-users'],
                    ['etiqueta' => 'Documentos y Recursos', 'route_name' => 'informativos.index', 'icono' => 'fas-file-alt'],
                    ['etiqueta' => 'Grupos de Investigación', 'url' => 'https://letras.unmsm.edu.pe/unidad-de-investigacion', 'icono' => 'fas-flask', 'nueva_pestana' => true],
                ],
            ],
            [
                'etiqueta' => 'Admisión', 'icono' => 'fas-user-plus',
                'hijos' => [
                    ['etiqueta' => 'Proceso de Admisión', 'route_name' => 'admision', 'icono' => 'fas-user-plus'],
                    ['etiqueta' => 'Cuadro de Vacantes', 'url' => 'https://posgrado.unmsm.edu.pe/doc/cuadro-de-vacantes-2026-i-f-1-f-1765884106-0', 'icono' => 'fas-th-list', 'nueva_pestana' => true],
                    ['etiqueta' => 'Criterios de Evaluación', 'url' => 'https://posgrado.unmsm.edu.pe/doc/criterios-evaluacion-admision-2025', 'icono' => 'fas-clipboard-check', 'nueva_pestana' => true],
                    ['etiqueta' => 'Cronograma Académico', 'route_name' => 'cronograma', 'icono' => 'fas-calendar-alt'],
                ],
            ],
            [
                'etiqueta' => 'Programas', 'icono' => 'fas-graduation-cap',
                'route_name' => 'programas.index',
                'hijos' => [
                    ['etiqueta' => 'Maestrías', 'route_name' => 'programas.index', 'route_params' => '{"tipo":"maestria"}', 'icono' => 'fas-graduation-cap'],
                    ['etiqueta' => 'Doctorados', 'route_name' => 'programas.index', 'route_params' => '{"tipo":"doctorado"}', 'icono' => 'fas-book-reader'],
                ],
            ],
            [
                'etiqueta' => 'Diplomados', 'icono' => 'fas-certificate',
                'route_name' => 'diplomados.index',
            ],
            [
                // Sin desplegable: su única subentrada apuntaba a esta misma
                // página, así que el menú se abría para ofrecer lo que ya
                // ofrecía la propia entrada.
                'etiqueta' => 'Trámites', 'icono' => 'fas-file-alt',
                'route_name' => 'tramites',
            ],
            [
                'etiqueta' => 'Actualidad', 'icono' => 'fas-newspaper',
                'hijos' => [
                    ['etiqueta' => 'Eventos', 'route_name' => 'eventos.index', 'icono' => 'fas-calendar-day'],
                    ['etiqueta' => 'Noticias', 'url' => 'https://letras.unmsm.edu.pe/categoria/noticias/', 'icono' => 'fas-newspaper', 'nueva_pestana' => true],
                    ['etiqueta' => 'Conferencias', 'url' => 'https://letras.unmsm.edu.pe/categoria/conferencias/', 'icono' => 'fas-microphone', 'nueva_pestana' => true],
                ],
            ],
            [
                'etiqueta' => 'Idiomas', 'icono' => 'fas-language',
                'hijos' => [
                    ['etiqueta' => 'CEID Letras', 'url' => 'https://ceidletras.unmsm.edu.pe/', 'icono' => 'fas-language', 'nueva_pestana' => true],
                    ['etiqueta' => 'Examen de Suficiencia', 'url' => 'https://letras.unmsm.edu.pe/oficina-de-examen-de-suficiencia-en-idiomas/', 'icono' => 'fas-file-circle-check', 'nueva_pestana' => true],
                    ['etiqueta' => 'Tarifario', 'url' => 'https://letras.unmsm.edu.pe/tarifario-centro-de-idiomas/', 'icono' => 'fas-tags', 'nueva_pestana' => true],
                ],
            ],
            [
                'etiqueta' => 'Facultad', 'icono' => 'fas-university',
                'url' => 'https://letras.unmsm.edu.pe',
                'nueva_pestana' => true,
            ],
        ];
    }
}
