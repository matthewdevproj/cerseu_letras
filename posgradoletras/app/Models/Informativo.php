<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informativo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'categoria',
        'titulo',
        'tipo',
        'url',
        'orden',
    ];

    protected $casts = [
        'tipo' => 'integer',
        'orden' => 'integer',
    ];

    /**
     * Scope para filtrar por categoría
     */
    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para ordenar por campo orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('id');
    }

    /**
     * Helper para determinar si es PDF/URL
     */
    public function getEsPdfAttribute(): bool
    {
        return $this->tipo === 0;
    }

    /**
     * Helper para determinar si es enlace externo
     */
    public function getEsEnlaceAttribute(): bool
    {
        return $this->tipo === 1;
    }

    /**
     * Helper para obtener el ícono según categoría
     */
    public function getIconoAttribute(): string
    {
        return match (strtolower($this->categoria)) {
            'reglamento' => 'fas fa-gavel',
            'directiva' => 'fas fa-file-signature',
            'información', 'informacion' => 'fas fa-info-circle',
            default => 'fas fa-file-alt'
        };
    }

    /**
     * Helper para obtener texto del botón
     */
    public function getTextoBotonAttribute(): string
    {
        return $this->es_pdf ? 'DESCARGAR PDF' : 'VER';
    }
}
