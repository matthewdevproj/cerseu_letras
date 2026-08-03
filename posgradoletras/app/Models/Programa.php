<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Programa extends Model
{
    use \App\Models\Concerns\InvalidatesSearchIndex, SoftDeletes;

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
        'brochure_url',
        'admision_pdf_url',
        'horas_academicas',
        'fecha_limite_inscripcion',
        'inversion_economica',
        'costo_por_credito',
        'semestres_inversion',
        'por_que_text',
        'sumilla',
        'plan_estudios',
        'is_active',
        'estado',
        'slug',
        'imagen'
    ];

    /** Publicado: visible en listados y con su ficha completa. */
    public const ESTADO_PUBLICADO = 'publicado';

    /** Próximamente: se anuncia en los listados, pero su ficha aún no tiene detalle. */
    public const ESTADO_PROXIMAMENTE = 'proximamente';

    /** Borrador: no existe de cara al público (404). */
    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADOS = [
        self::ESTADO_PUBLICADO => [
            'label' => 'Publicado',
            'ayuda' => 'Visible en los listados y con su ficha completa.',
            'badge' => 'bg-green-100 text-green-800',
            'punto' => 'bg-green-600',
        ],
        self::ESTADO_PROXIMAMENTE => [
            'label' => 'Próximamente',
            'ayuda' => 'Aparece en los listados con la etiqueta «Próximamente»; su página anuncia que pronto habrá información.',
            'badge' => 'bg-amber-100 text-amber-800',
            'punto' => 'bg-amber-500',
        ],
        self::ESTADO_BORRADOR => [
            'label' => 'Borrador',
            'ayuda' => 'Oculto por completo: no se lista y su URL responde 404.',
            'badge' => 'bg-gray-100 text-gray-800',
            'punto' => 'bg-gray-500',
        ],
    ];

    protected $casts = [
        'vacantes' => 'integer',
        'duracion' => 'integer',
        'creditos' => 'integer',
        'horas_academicas' => 'integer',
        'is_active' => 'boolean',
        'plan_estudios' => 'array',
        'objetivos_academicos' => 'array',
        'perfil_ingresante' => 'array',
        'perfil_graduado' => 'array',
        'inversion_economica' => 'array',
        'costo_por_credito' => 'integer',
        'semestres_inversion' => 'array',
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

    /**
     * Programas publicados. Es el scope que usan las páginas públicas: mantiene
     * el nombre histórico para no tocar sus llamadas, pero ahora se apoya en
     * `estado` en lugar del booleano.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_PUBLICADO);
    }

    /**
     * Programas alcanzables por el público: los publicados y los anunciados
     * como próxima oferta. Los borradores quedan fuera.
     */
    public function scopeVisibles($query)
    {
        return $query->whereIn('estado', [self::ESTADO_PUBLICADO, self::ESTADO_PROXIMAMENTE]);
    }

    public function scopeProximamente($query)
    {
        return $query->where('estado', self::ESTADO_PROXIMAMENTE);
    }

    /**
     * Orden de los listados públicos: primero la oferta vigente y al final la
     * que aún se anuncia, y dentro de cada grupo por nombre.
     */
    public function scopeOrdenPublicacion($query)
    {
        return $query
            ->orderByRaw("CASE WHEN estado = ? THEN 0 ELSE 1 END", [self::ESTADO_PUBLICADO])
            ->orderBy('nombre');
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

        // Los diplomados suelen tener su propio nombre completo (p. ej. "Diplomado Internacional de...")
        // y no deben llevar el prefijo duplicado.
        if (Str::startsWith(Str::lower($this->nombre), ['diplomado', 'doctorado', 'maestría', 'maestria'])) {
            $texto = $this->nombre;
        } else {
            $texto = $prefix . $this->nombre;
        }

        if ($this->mencion) {
            $texto .= ' con mención en ' . $this->mencion;
        }

        return $texto;
    }

    // Inversión económica

    /**
     * Semestres (o módulos) con sus importes ya calculados.
     *
     * Punto único del cálculo: antes vivía repetido en las tres plantillas de
     * inversión y otra vez en programas/show, con el riesgo de que dejaran de
     * coincidir entre sí.
     *
     * @return array<int, array{numero:int, matricula:int, creditos:int, costo_semestre:int, cuota_mensual:float}>
     */
    public function getSemestresCalculadosAttribute(): array
    {
        $costoCredito = (int) ($this->costo_por_credito ?? 0);
        $filas = [];

        foreach ((array) ($this->semestres_inversion ?? []) as $i => $sem) {
            $matricula = (int) ($sem['matricula'] ?? 0);
            $creditos = (int) ($sem['creditos'] ?? 0);
            $costoSemestre = $creditos * $costoCredito;

            $filas[] = [
                'numero' => $i + 1,
                'matricula' => $matricula,
                'creditos' => $creditos,
                'costo_semestre' => $costoSemestre,
                // Referencia orientativa: el semestre se fracciona en 4 pagos.
                // Se fuerza a float: en PHP la división exacta devuelve int, y
                // el tipo cambiaría según los importes de cada programa.
                'cuota_mensual' => (float) ($costoSemestre / 4),
            ];
        }

        return $filas;
    }

    /**
     * Costo total del programa.
     *
     * Prioriza el importe cerrado que se haya cargado en `inversion_economica`
     * (así lo tienen los diplomados) y, si no existe, lo calcula sumando
     * matrículas y créditos.
     */
    public function getCostoTotalAttribute(): ?int
    {
        $cerrado = $this->inversion_economica['costo_total'] ?? null;

        if (is_numeric($cerrado)) {
            return (int) $cerrado;
        }

        $filas = $this->semestres_calculados;

        if (empty($filas)) {
            return null;
        }

        return (int) array_sum(array_map(
            fn ($f) => $f['matricula'] + $f['costo_semestre'],
            $filas
        ));
    }

    /**
     * Etiqueta del periodo según el grado ("Primer semestre" / "Módulo 1").
     */
    public function etiquetaPeriodo(int $numero): string
    {
        if ($this->grado === 'Diplomado') {
            return 'Módulo ' . $numero;
        }

        $ordinales = [1 => 'Primer', 2 => 'Segundo', 3 => 'Tercer', 4 => 'Cuarto', 5 => 'Quinto', 6 => 'Sexto'];

        return ($ordinales[$numero] ?? $numero . '.º') . ' semestre';
    }

    // Estado de publicación

    public function getEsPublicadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_PUBLICADO;
    }

    public function getEsProximamenteAttribute(): bool
    {
        return $this->estado === self::ESTADO_PROXIMAMENTE;
    }

    public function getEsBorradorAttribute(): bool
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? self::ESTADOS[self::ESTADO_BORRADOR]['label'];
    }

    public function getEstadoBadgeAttribute(): string
    {
        return self::ESTADOS[$this->estado]['badge'] ?? self::ESTADOS[self::ESTADO_BORRADOR]['badge'];
    }

    public function getEstadoPuntoAttribute(): string
    {
        return self::ESTADOS[$this->estado]['punto'] ?? self::ESTADOS[self::ESTADO_BORRADOR]['punto'];
    }

    /**
     * URL utilizable del brochure: admite tanto una URL absoluta como una ruta
     * relativa guardada en el disco `public` (según cómo se haya subido).
     * Devuelve null cuando el programa aún no tiene brochure, para poder ocultar
     * el botón sin romper el diseño.
     */
    public function getBrochureLinkAttribute(): ?string
    {
        $valor = trim((string) $this->brochure_url);

        if ($valor === '') {
            return null;
        }

        if (str_starts_with($valor, 'http://') || str_starts_with($valor, 'https://')) {
            $ruta = parse_url($valor, PHP_URL_PATH) ?? '';

            // El panel guarda la subida como URL absoluta (`asset()`), lo que
            // congela el host del entorno donde se subió: un brochure cargado en
            // local apuntaría a localhost al publicar. Si el archivo es nuestro
            // (/storage/...), se reconstruye contra el host actual; los enlaces
            // externos (Drive, etc.) se respetan tal cual.
            if (str_starts_with($ruta, '/storage/')) {
                return asset(ltrim($ruta, '/'));
            }

            return $valor;
        }

        return asset('storage/' . ltrim($valor, '/'));
    }


    public function getImagenUrlAttribute()
    {
        // Sin imagen propia: se usa una del campus, auto-alojada y ya recortada
        // a tamaño de tarjeta (40–90 KB). Antes se pedían a Unsplash: media
        // pantalla de peso y una dependencia externa en la ruta crítica.
        if (!$this->imagen) {
            return match($this->grado) {
                'Maestría'  => asset('images/programa-maestria.webp'),
                'Diplomado' => asset('images/programa-diplomado.webp'),
                default     => asset('images/programa-doctorado.webp'),
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

        // `estado` es la única fuente de verdad; `is_active` se mantiene en
        // sincronía para no romper consultas o informes que aún lo lean.
        static::saving(function ($programa) {
            $atributos = $programa->getAttributes();

            // Compatibilidad hacia atrás: los seeders y el código anterior al
            // campo `estado` solo envían `is_active`. Sin esto, todo lo que se
            // creara por esa vía quedaría en borrador y desaparecería del sitio.
            if (!array_key_exists('estado', $atributos) || $programa->estado === null) {
                $programa->estado = !empty($atributos['is_active'])
                    ? self::ESTADO_PUBLICADO
                    : self::ESTADO_BORRADOR;
            }

            if (!array_key_exists($programa->estado, self::ESTADOS)) {
                $programa->estado = self::ESTADO_BORRADOR;
            }

            $programa->is_active = $programa->estado === self::ESTADO_PUBLICADO;
        });

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
