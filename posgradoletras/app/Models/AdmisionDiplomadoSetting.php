<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdmisionDiplomadoSetting extends Model
{
    protected $fillable = [
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
        'pasos' => 'array',
        'requisitos_lista' => 'array',
        'pago_instrucciones' => 'array',
    ];

    public function cronogramaItems()
    {
        return $this->hasMany(AdmisionDiplomadoCronogramaItem::class)->orderBy('orden')->orderBy('id');
    }

    public static function get(): ?self
    {
        return Cache::remember('admision_diplomado_settings', 3600, function () {
            return self::with('cronogramaItems')->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('admision_diplomado_settings');
    }
}
