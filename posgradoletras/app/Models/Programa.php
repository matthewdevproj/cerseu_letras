<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Programa extends Model
{
    protected $fillable = [
        'codigo',
        'grado',
        'nombre',
        'mencion',
        'modalidad',
        'vacantes',
        'duracion',
        'creditos',
        'grado_otorga',
        'plan_url',
        'por_que_text',
        'presentacion',
        'perfil_egresado',
        'plan_estudios',
        'is_active',
        'slug',
        'sumilla',
        'descripcion',
        'imagen'
    ];

    protected $casts = [
        'vacantes' => 'integer',
        'duracion' => 'integer',
        'creditos' => 'integer',
        'is_active' => 'boolean',
        'plan_estudios' => 'array'
    ];

    // Relaciones
    public function docentes()
    {
        return $this->belongsToMany(Docente::class, 'docente_programa')
            ->withPivot('es_coordinador', 'rol', 'orden')
            ->withTimestamps()
            ->orderBy('docente_programa.orden');
    }

    public function testimonios()
    {
        return $this->hasMany(Testimonio::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeMaestrias($query)
    {
        return $query->where('grado', 'Maestría');
    }

    public function scopeDoctorados($query)
    {
        return $query->where('grado', 'Doctorado');
    }

    // Accessors
    public function getTituloCompletoAttribute()
    {
        if ($this->mencion) {
            return "{$this->nombre} con mención en {$this->mencion}";
        }
        return $this->nombre;
    }

    public function getDuracionFormateadaAttribute()
    {
        return $this->duracion ? "{$this->duracion} semestres" : null;
    }

    public function getTituloAttribute()
    {
        return $this->titulo_completo;
    }

    public function getTipoAttribute()
    {
        return $this->grado === 'Maestría' ? 'maestria' : 'doctorado';
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        // Fallback según el tipo de programa
        return $this->grado === 'Maestría'
            ? 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=600&q=80'
            : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=600&q=80';
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($programa) {
            if (empty($programa->slug)) {
                $programa->slug = Str::slug($programa->codigo);
            }
        });

        static::updating(function ($programa) {
            if (empty($programa->slug)) {
                $programa->slug = Str::slug($programa->codigo);
            }
        });
    }
}
