<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Programa extends Model
{
    protected $fillable = [
        'grado',
        'nombre',
        'mencion',
        'modalidad',
        'vacantes',
        'duracion',
        'creditos',
        // 'grado_otorga', // Se genera automáticamente, ya no es necesario guardarlo aunque la columna exista
        'objetivos_academicos',
        'perfil_ingresante',
        'perfil_graduado',
        'plan_url',
        'horario_url',
        'por_que_text',
        'sumilla',
        'plan_estudios',
        'is_active',
        'slug',
        'imagen'
    ];

    protected $casts = [
        'vacantes' => 'integer',
        'duracion' => 'integer',
        'creditos' => 'integer',
        'is_active' => 'boolean',
        'plan_estudios' => 'array',
        'objetivos_academicos' => 'array',
        'perfil_ingresante' => 'array',
        'perfil_graduado' => 'array'
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

    public function getGradoOtorgaAttribute($value)
    {
        // Generación automática: Grado + Nombre + Mención
        $prefix = $this->grado === 'Doctorado' ? 'Doctor en ' : 'Magíster en ';
        $texto = $prefix . $this->nombre;

        if ($this->mencion) {
            $texto .= ' con mención en ' . $this->mencion;
        }

        return $texto;
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        // Fallback según el tipo de programa (optimizadas)
        return $this->grado === 'Maestría'
            ? 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=800&q=75&fm=webp'
            : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=800&q=75&fm=webp';
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($programa) {
            if (empty($programa->slug)) {
                $baseName = $programa->nombre;
                if ($programa->mencion) {
                    $baseName .= ' ' . $programa->mencion;
                }
                $programa->slug = Str::slug($baseName);
            }
        });

        static::updating(function ($programa) {
            if (empty($programa->slug)) {
                $baseName = $programa->nombre;
                if ($programa->mencion) {
                    $baseName .= ' ' . $programa->mencion;
                }
                $programa->slug = Str::slug($baseName);
            }
        });
    }
}
