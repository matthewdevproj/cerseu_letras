<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Docente extends Model
{
    use \App\Models\Concerns\InvalidatesSearchIndex, SoftDeletes;

    protected $fillable = [
        'nombres',
        'apellidos',
        'grado',
        'email',
        'orcid',
        'cti_vitae',
        'linkedin',
        'biografia',
        'estado',
        'foto',
        'lineas_investigacion',
        'grupo_investigacion',
        'slug'
    ];

    protected $casts = [
        'estado' => 'integer',
        'lineas_investigacion' => 'array',
        'grupo_investigacion' => 'array',
    ];

    // Relaciones
    public function programas()
    {
        return $this->belongsToMany(Programa::class, 'docente_programa')
            ->withPivot('es_coordinador', 'rol', 'orden')
            ->withTimestamps()
            ->orderBy('docente_programa.orden');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $grado = $this->grado ? "{$this->grado} " : '';
        return "{$grado}{$this->nombres} {$this->apellidos}";
    }

    public function getNombreAttribute()
    {
        return $this->nombre_completo;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/profesor-default.jpg');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('apellidos')->orderBy('nombres');
    }

    // Boot method para generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($docente) {
            if (empty($docente->slug)) {
                $docente->slug = static::generateUniqueSlug($docente->nombres, $docente->apellidos);
            }
        });

        static::updating(function ($docente) {
            if (empty($docente->slug) || $docente->isDirty(['nombres', 'apellidos'])) {
                $docente->slug = static::generateUniqueSlug($docente->nombres, $docente->apellidos, $docente->id);
            }
        });
    }

    /**
     * Generar slug único basado en nombres y apellidos
     */
    protected static function generateUniqueSlug($nombres, $apellidos, $excludeId = null)
    {
        $baseSlug = Str::slug("{$nombres} {$apellidos}");
        $slug = $baseSlug;
        $counter = 1;

        while (static::slugExists($slug, $excludeId)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si el slug ya existe
     */
    protected static function slugExists($slug, $excludeId = null)
    {
        $query = static::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
