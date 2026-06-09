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

    public function scopeDiplomados($query)
    {
        return $query->where('grado', 'Diplomado');
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
        if (!$this->duracion) return null;
        $unidad = $this->grado === 'Diplomado' ? 'módulos' : 'semestres';
        return "{$this->duracion} {$unidad}";
    }

    public function getTituloAttribute()
    {
        return $this->titulo_completo;
    }

    public function getTipoAttribute()
    {
        return match($this->grado) {
            'Maestría'  => 'maestria',
            'Doctorado' => 'doctorado',
            'Diplomado' => 'diplomado',
            default     => strtolower($this->grado),
        };
    }

    public function getGradoOtorgaAttribute($value)
    {
        $prefix = match($this->grado) {
            'Doctorado' => 'Doctor en ',
            'Diplomado' => 'Diplomado en ',
            default     => 'Magíster en ',
        };
        $texto = $prefix . $this->nombre;

        if ($this->mencion) {
            $texto .= ' con mención en ' . $this->mencion;
        }

        return $texto;
    }

    
    public function getImagenUrlAttribute()
    {
        // Si no hay imagen, usar por defecto
        if (!$this->imagen) {
            return match($this->grado) {
                'Maestría'  => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=800&q=80',
                'Diplomado' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
                default     => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=800&q=80',
            };
        }
        
        // Si ya es una URL completa (http:// o https://)
        // Esto cubre: URLs externas, Unsplash, etc.
        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://')) {
            return $this->imagen;
        }
        
        // Para rutas locales (ahora siempre serán relativas como 'documents/...')
        // asset('storage/' + ruta) generará: https://posgrado.../storage/documents/...
        return asset('storage/' . $this->imagen);
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
