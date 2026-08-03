<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Sección "Cronograma de Admisión" de la portada (registro único).
 *
 * Permite adaptar la sección a cualquier convocatoria —maestrías y doctorados,
 * diplomados o nuevos periodos— editando títulos, etapas y el botón principal
 * desde el panel, sin tocar la plantilla.
 */
class CronogramaAdmision extends Model
{
    protected $table = 'cronograma_admisiones';

    protected $fillable = [
        'eyebrow',
        'titulo',
        'boton_texto',
        'boton_url',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Catálogo de íconos disponibles para las etapas. Son trazados de Heroicons
     * (outline, 24×24) ya usados en el resto del sitio: se dibujan inline, así
     * que no añaden ninguna petición ni peso de librería.
     */
    public const ICONOS = [
        'inscripcion' => ['label' => 'Inscripción / carné', 'path' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
        'examen' => ['label' => 'Examen / envío', 'path' => 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5'],
        'birrete' => ['label' => 'Birrete académico', 'path' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.221 69.17 69.17 0 00-2.923.897'],
        'expediente' => ['label' => 'Expediente / revisión', 'path' => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'],
        'check' => ['label' => 'Resultados / aprobado', 'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'documento' => ['label' => 'Documento', 'path' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        'calendario' => ['label' => 'Calendario', 'path' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
        'reloj' => ['label' => 'Reloj / plazo', 'path' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        'correo' => ['label' => 'Correo', 'path' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
        'personas' => ['label' => 'Personas / entrevistas', 'path' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        'institucion' => ['label' => 'Institución', 'path' => 'M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z'],
    ];

    /**
     * Trazado SVG de un ícono, con un respaldo seguro si la clave no existe.
     */
    public static function iconoPath(?string $key): string
    {
        return self::ICONOS[$key]['path'] ?? self::ICONOS['documento']['path'];
    }

    public function pasos()
    {
        return $this->hasMany(CronogramaAdmisionPaso::class)->orderBy('orden')->orderBy('id');
    }

    /**
     * Solo las etapas visibles, para el frontend.
     */
    public function pasosVisibles()
    {
        return $this->pasos()->where('is_visible', true);
    }

    /**
     * Configuración actual (registro único) con sus etapas, cacheada.
     */
    public static function get(): ?self
    {
        return Cache::remember('cronograma_admision', 3600, function () {
            return self::with('pasos')->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('cronograma_admision');
    }
}
