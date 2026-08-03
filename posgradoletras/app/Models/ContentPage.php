<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Página de contenido editable (/tramites, /admision).
 */
class ContentPage extends Model
{
    protected $fillable = ['slug', 'titulo', 'subtitulo'];

    /** Páginas gestionadas y su etiqueta en el panel. */
    public const PAGINAS = [
        'tramites' => 'Trámites',
        'admision' => 'Admisión',
        'nosotros' => 'Nosotros',
    ];

    /** Pestañas disponibles por página (vacío = sin pestañas). */
    public const GRUPOS = [
        'tramites' => ['maestria' => 'Grado de Magíster', 'doctorado' => 'Grado de Doctor'],
        'admision' => [],
        // En /nosotros el grupo no es una pestaña sino el sitio donde va cada
        // sección: misión y visión son bloques únicos y cada valor es una
        // entrada de la lista de la tarjeta guinda.
        'nosotros' => ['mision' => 'Misión', 'vision' => 'Visión', 'valor' => 'Valor (uno por entrada)'],
    ];

    public function secciones()
    {
        return $this->hasMany(ContentSection::class)->orderBy('orden')->orderBy('id');
    }

    /**
     * Página con sus secciones, cacheada. `null` si aún no se ha creado.
     */
    public static function porSlug(string $slug): ?self
    {
        return Cache::remember("content_page_{$slug}", 3600, function () use ($slug) {
            return self::with('secciones')->where('slug', $slug)->first();
        });
    }

    /**
     * Secciones visibles de un grupo (pestaña) concreto.
     */
    public function seccionesDe(?string $grupo = null)
    {
        return $this->secciones
            ->where('is_visible', true)
            ->when($grupo !== null, fn ($c) => $c->where('grupo', $grupo))
            ->values();
    }

    public static function clearCache(?string $slug = null): void
    {
        foreach ($slug ? [$slug] : array_keys(self::PAGINAS) as $s) {
            Cache::forget("content_page_{$s}");
        }
    }
}
