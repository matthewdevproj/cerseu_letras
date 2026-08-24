<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectorioCerseu extends Model
{
    use SoftDeletes;

    protected $table = 'directorio_cerseu';

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
        // CASE WHEN en vez de FIELD(): FIELD() es específico de MySQL y no
        // existe en SQLite (usado en desarrollo local), lo que rompía esta
        // página con un error 500 fuera de producción.
        return self::activos()
            ->orderByRaw("CASE unidad_nombre WHEN 'AUTORIDADES' THEN 1 WHEN 'PERSONAL ADMINISTRATIVO' THEN 2 ELSE 3 END ASC")
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
