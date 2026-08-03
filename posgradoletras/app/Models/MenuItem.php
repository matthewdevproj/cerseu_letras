<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Un elemento del menú de navegación.
 *
 * El árbol completo se sirve desde caché y se recorre una sola vez por
 * petición: la barra lo pinta dos veces (escritorio y móvil) y antes eso
 * significaba mantener dos copias del marcado a mano.
 */
class MenuItem extends Model
{
    protected $fillable = [
        'parent_id',
        'etiqueta',
        'route_name',
        'url',
        'route_params',
        'icono',
        'nueva_pestana',
        'orden',
        'is_visible',
        'vigente_hasta',
    ];

    protected $casts = [
        'nueva_pestana' => 'boolean',
        'is_visible' => 'boolean',
        'orden' => 'integer',
        'vigente_hasta' => 'date',
    ];

    public function hijos()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('orden');
    }

    public function padre()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Entradas que deben verse hoy.
     *
     * Una entrada caducada deja de mostrarse sola: es lo que evita que el
     * enlace de una convocatoria pasada siga colgado meses.
     */
    public function scopeVisibles($q)
    {
        return $q->where('is_visible', true)
            ->where(fn ($q) => $q->whereNull('vigente_hasta')
                ->orWhere('vigente_hasta', '>=', now()->startOfDay()));
    }

    /** ¿Pasó de fecha? Para avisar en el panel, donde sí se siguen viendo. */
    public function getCaducadoAttribute(): bool
    {
        return $this->vigente_hasta !== null
            && $this->vigente_hasta->lt(now()->startOfDay());
    }

    /**
     * Árbol de menús visibles, listo para pintar.
     *
     * `once()` evita repetir el trabajo entre la versión de escritorio y la de
     * móvil dentro de la misma petición.
     */
    public static function arbol()
    {
        return once(fn () => Cache::remember('menu_items_tree_' . now()->toDateString(), 3600, function () {
            return self::visibles()
                ->whereNull('parent_id')
                ->with(['hijos' => fn ($q) => $q->visibles()])
                ->orderBy('orden')
                ->get();
        }));
    }

    public static function clearCache(): void
    {
        Cache::forget('menu_items_tree_' . now()->toDateString());
        Cache::forget('menu_items_tree_' . now()->subDay()->toDateString());
        \Illuminate\Support\Once::flush();
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }

    /**
     * Destino final del elemento.
     *
     * Se prefiere `route_name` porque sobrevive a un cambio de URL; si la ruta
     * ya no existe (se renombró o se retiró) se devuelve null en lugar de
     * reventar la barra de navegación entera.
     */
    public function getEnlaceAttribute(): ?string
    {
        if ($this->route_name) {
            if (! \Illuminate\Support\Facades\Route::has($this->route_name)) {
                return null;
            }

            $params = $this->route_params
                ? json_decode($this->route_params, true) ?: []
                : [];

            return route($this->route_name, $params);
        }

        return $this->url ?: null;
    }

    /** Un elemento sin destino y con hijos es solo la cabecera del desplegable. */
    public function getEsDesplegableAttribute(): bool
    {
        return $this->hijos->isNotEmpty();
    }

    /** Marca el menú activo comparando con la ruta actual. */
    public function getEstaActivoAttribute(): bool
    {
        if ($this->route_name && request()->routeIs($this->route_name)) {
            return true;
        }

        return $this->hijos->contains(
            fn (self $h) => $h->route_name && request()->routeIs($h->route_name)
        );
    }
}
