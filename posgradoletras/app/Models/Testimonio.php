<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
    protected $fillable = [
        'nombre',
        'photo',
        'programa_id',
        'contenido',
        'estado'
    ];

    protected $casts = [
        'programa_id' => 'integer',
        'estado' => 'integer'
    ];

    // Relaciones
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    // Scopes
    public function scopePublicados($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeRecientes($query)
    {
        return $query->latest();
    }

    // Accessors
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-avatar.jpg');
    }
}
