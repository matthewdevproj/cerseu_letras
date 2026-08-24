<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdmisionSetting extends Model
{
    protected $fillable = [
        'tipo',
        'hero_titulo',
        'hero_subtitulo',
        'hero_imagen',
        'pasos',
        'requisitos_email',
        'requisitos_lista',
        'requisitos_observaciones',
        'requisitos_notas',
        'pago_costo',
        'pago_descripcion',
        'pago_instrucciones',
        'pago_link_sanmarket',
        'pago_observaciones',
        'resultados_texto',
        'resultados_enlace',
        'resultados_pdf_url',
        'contacto_telefono',
        'contacto_correo',
        'contacto_direccion',
        'contacto_sitio_web',
        'contacto_qr_path',
        'contacto_whatsapp',
    ];

    protected $casts = [
        'tipo' => TipoOferta::class,
        'pasos' => 'array',
        'requisitos_lista' => 'array',
        'pago_instrucciones' => 'array',
    ];

    public function cronogramaItems()
    {
        return $this->hasMany(AdmisionCronogramaItem::class)->orderBy('orden')->orderBy('id');
    }

    public function scopeDeTipo($query, TipoOferta $tipo)
    {
        return $query->where('tipo', $tipo->value);
    }

    /**
     * Ajustes de admisión de un tipo, cacheados por separado.
     *
     * La clave lleva el tipo dentro: talleres y cursos se editan por separado
     * y una sola clave habría hecho que guardar uno mostrara el otro.
     */
    public static function get(TipoOferta $tipo): ?self
    {
        return Cache::remember("admision_settings:{$tipo->value}", 3600, function () use ($tipo) {
            return self::with('cronogramaItems')->deTipo($tipo)->first();
        });
    }

    public static function clearCache(?TipoOferta $tipo = null): void
    {
        foreach ($tipo ? [$tipo] : TipoOferta::cases() as $t) {
            Cache::forget("admision_settings:{$t->value}");
        }
    }
}
