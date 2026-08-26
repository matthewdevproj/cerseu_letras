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
            'url' => $this->resource->url,
        ];
    }
}
