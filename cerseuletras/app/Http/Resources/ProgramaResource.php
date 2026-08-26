<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Contrato público de un programa hacia el sitio en Astro.
 *
 * Se declara campo a campo a propósito, sin `$this->resource->toArray()`: la
 * tabla `programas` arrastra columnas de la etapa de Posgrado —`creditos`,
 * `costo_por_credito`, `semestres_inversion`, `perfil_graduado`— que no
 * pintan nada en la oferta del CERSEU. Volcar el modelo entero las publicaría
 * en la API y ataría el frontend a un esquema que queremos poder limpiar.
 *
 * `medidas` sale ya formateada («12 horas académicas») porque la regla de qué
 * unidad corresponde a cada tipo vive en TipoOferta, no en la plantilla: si
 * Astro las recompusiera, esa regla acabaría duplicada en dos lenguajes.
 */
class ProgramaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tipo = $this->resource->tipoOferta();

        return [
            'slug' => $this->resource->slug,
            'nombre' => $this->resource->nombre,
            'tipo' => $tipo?->slug(),
            'tipo_label' => $tipo?->singular(),
            'mencion' => $this->resource->mencion ?: null,
            'modalidad' => $this->resource->modalidad ?: null,
            'sumilla' => $this->resource->sumilla ?: null,
            'medidas' => $this->resource->medidasFormateadas(),
            'inversion' => $this->resource->inversion_economica ?: null,
            'estado' => $this->resource->estado,
            'imagen' => $this->resource->imagen ?: null,

            // Sin URL: la API entrega identidad (`tipo` + `slug`), no rutas.
            // Devolver la URL del sitio en Blade ataba al consumidor a la
            // estructura de enlaces de OTRO sitio, y el de Astro acababa
            // expulsando al visitante en cada «Mas informacion». Cada
            // frontend compone sus propias rutas.

            // Solo cuando se han cargado: el listado no paga la consulta.
            'docentes' => $this->whenLoaded('docentes', fn () => $this->resource->docentes
                ->sortBy('pivot.orden')
                ->map(fn ($docente) => [
                    'nombre' => trim($docente->nombres . ' ' . $docente->apellidos),
                    'grado' => $docente->grado ?: null,
                    'rol' => $docente->pivot->rol ?: null,
                    'es_coordinador' => (bool) $docente->pivot->es_coordinador,
                ])->values()->all()),
        ];
    }
}
