<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'imagen',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'url',
        'tipo_url',
        'orden',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeProximos($query)
    {
        return $query->where('fecha_inicio', '>=', now()->startOfDay())
            ->orWhere('fecha_fin', '>=', now()->startOfDay());
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('fecha_inicio', 'desc');
    }

    // Accessors
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return asset('images/evento-default.jpg');
    }

    public function getFechaFormateadaAttribute()
    {
        $inicio = $this->fecha_inicio->format('d M Y');

        if ($this->fecha_fin && $this->fecha_fin->ne($this->fecha_inicio)) {
            $fin = $this->fecha_fin->format('d M Y');
            return "{$inicio} - {$fin}";
        }

        return $inicio;
    }

    public function getEsPdfAttribute()
    {
        return $this->tipo_url === 'pdf';
    }

    public function getEsExternoAttribute()
    {
        return $this->tipo_url === 'externo';
    }

    public function getTieneUrlAttribute()
    {
        return !empty($this->url);
    }
}
