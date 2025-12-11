<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectorioPosgrado extends Model
{
    protected $table = 'directorio_posgrado';

    protected $fillable = [
        'unidad_nombre',
        'cargo',
        'nombre_persona',
        'anexo',
        'correo_persona',
        'orden',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Scope para filtrar solo registros activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener registros agrupados por unidad_nombre
     */
    public static function agrupadosPorUnidad()
    {
        return self::activos()
            ->orderByRaw("FIELD(unidad_nombre, 'AUTORIDADES', 'PERSONAL ADMINISTRATIVO') ASC")
            ->orderBy('orden')
            ->get()
            ->groupBy('unidad_nombre');
    }

    /**
     * Obtener lista de unidades únicas
     */
    public static function unidadesUnicas()
    {
        return self::distinct()->pluck('unidad_nombre')->toArray();
    }
}
