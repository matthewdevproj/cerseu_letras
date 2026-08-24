<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Cronograma extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Ítems del cronograma ordenados
     */
    public function items()
    {
        return $this->hasMany(CronogramaItem::class)->orderBy('orden')->orderBy('id');
    }

    /**
     * Documentos asociados (pivot)
     */
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'cronograma_documents')
            ->withPivot(['position'])
            ->withTimestamps()
            ->orderBy('pivot_position');
    }

    /**
     * Obtiene el cronograma activo actual (el más reciente con is_active = true)
     */
    public static function getActive()
    {
        return Cache::remember('cronograma_active', 3600, function () {
            return static::where('is_active', true)
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->with(['items', 'documents'])
                ->first();
        });
    }

    /**
     * Limpia el caché del cronograma activo
     */
    public static function clearCache(): void
    {
        Cache::forget('cronograma_active');
    }

    /**
     * Obtiene el código del proceso actual
     */
    public static function getCurrentCode(): string
    {
        $active = static::getActive();
        return $active ? $active->code : '2026-I';
    }
}
