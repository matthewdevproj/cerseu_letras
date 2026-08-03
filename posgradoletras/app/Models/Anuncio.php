<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Anuncio del popup de la portada.
 *
 * Solo se muestra en la portada, y solo si está visible y dentro de su ventana
 * de fechas. Ver `<x-popup-announcements>`.
 */
class Anuncio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'imagen',
        'imagen_ancho',
        'imagen_alto',
        'alt',
        'link',
        'link_texto',
        'visible_desde',
        'visible_hasta',
        'orden',
        'is_visible',
    ];

    protected $casts = [
        'visible_desde' => 'date',
        'visible_hasta' => 'date',
        'is_visible' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Anuncios que deben verse hoy.
     *
     * Las fechas son opcionales: sin `visible_desde` se muestra desde ya, y sin
     * `visible_hasta` no caduca. Así un anuncio permanente no obliga a inventar
     * una fecha lejana.
     */
    public function scopeVigentes($q)
    {
        $hoy = now()->startOfDay();

        return $q->where('is_visible', true)
            ->where(fn ($q) => $q->whereNull('visible_desde')->orWhere('visible_desde', '<=', $hoy))
            ->where(fn ($q) => $q->whereNull('visible_hasta')->orWhere('visible_hasta', '>=', $hoy));
    }

    /**
     * Proporción que exige el marco del popup, y medidas recomendadas.
     *
     * La imagen llena el marco recortando lo que sobre, así que subirla en
     * esta forma es lo que evita perder parte del cartel.
     */
    public const PROPORCION = 4 / 5;
    public const ANCHO_RECOMENDADO = 1000;
    public const ALTO_RECOMENDADO = 1250;

    /**
     * Cuánto se recortará de la imagen, en porcentaje del lado que sobra.
     *
     * `null` si aún no se conocen las medidas. Por debajo del 2 % se considera
     * que cuadra: no merece la pena avisar de un recorte inapreciable.
     */
    public function getRecortePorcentajeAttribute(): ?int
    {
        if (! $this->imagen_ancho || ! $this->imagen_alto) {
            return null;
        }

        $suya = $this->imagen_ancho / $this->imagen_alto;

        // Con `cover` se recorta el lado que sobra respecto al marco.
        $sobra = $suya > self::PROPORCION
            ? 1 - (self::PROPORCION / $suya)   // demasiado ancha: se van los lados
            : 1 - ($suya / self::PROPORCION);  // demasiado alta: se van arriba y abajo

        return (int) round($sobra * 100);
    }

    /** ¿Se va a recortar lo suficiente como para avisar? */
    public function getRecorteNotableAttribute(): bool
    {
        return ($this->recorte_porcentaje ?? 0) >= 2;
    }

    /** Por dónde se recorta, para decirlo en el panel. */
    public function getRecorteLadoAttribute(): ?string
    {
        if (! $this->recorte_notable) {
            return null;
        }

        return ($this->imagen_ancho / $this->imagen_alto) > self::PROPORCION
            ? 'por los lados'
            : 'por arriba y por abajo';
    }

    /** ¿Ya pasó de fecha? Sirve para avisar en el panel. */
    public function getCaducadoAttribute(): bool
    {
        return $this->visible_hasta !== null
            && $this->visible_hasta->lt(now()->startOfDay());
    }

    /** ¿Aún no ha empezado? */
    public function getProgramadoAttribute(): bool
    {
        return $this->visible_desde !== null
            && $this->visible_desde->gt(now()->startOfDay());
    }

    public function getImagenUrlAttribute(): string
    {
        // Admite tanto una imagen subida al panel como una URL externa.
        return str_starts_with((string) $this->imagen, 'http')
            ? $this->imagen
            : Storage::url($this->imagen);
    }

    /**
     * Lo que consume el componente del popup, ya en el formato que espera.
     */
    public static function paraPopup(): array
    {
        return Cache::remember('anuncios_popup_' . now()->toDateString(), 3600, function () {
            return self::vigentes()
                ->orderBy('orden')
                ->get()
                ->map(fn (self $a) => [
                    'imagen' => $a->imagen_url,
                    'alt' => $a->alt ?: $a->titulo,
                    'link' => $a->link ?: '',
                    'link_texto' => $a->link_texto ?: '',
                    // Dimensiones reales: el navegador reserva el sitio exacto
                    // y la ventana no da un salto al llegar el archivo.
                    'ancho' => $a->imagen_ancho,
                    'alto' => $a->imagen_alto,
                ])
                ->all();
        });
    }

    public static function clearCache(): void
    {
        // La clave lleva la fecha porque la vigencia cambia al pasar la
        // medianoche; se limpian la de hoy y la de ayer por si el cambio se
        // hace justo en la frontera.
        Cache::forget('anuncios_popup_' . now()->toDateString());
        Cache::forget('anuncios_popup_' . now()->subDay()->toDateString());
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
        static::restored(fn () => self::clearCache());
    }
}
