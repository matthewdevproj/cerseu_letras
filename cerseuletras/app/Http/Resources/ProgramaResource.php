<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Contrato público de un programa hacia el sitio en Astro.
 *
 * Se declara campo a campo a propósito, sin `$this->resource->toArray()`: la
 * tabla `programas` arrastra columnas de la etapa de Posgrado —`creditos`,
 * `costo_por_credito`, `semestres_inversion`— que no pintan nada en la oferta
 * del CERSEU. Volcar el modelo entero las publicaría en la API y ataría el
 * frontend a un esquema que queremos poder limpiar.
 *
 * El resto del contenido largo —objetivos, plan de estudios, perfiles, los
 * documentos— sí va, aunque hoy los 39 programas lo tengan vacío: son campos
 * que el panel edita, y no exponerlos significaría que el día que la Unidad
 * escriba los objetivos de un curso no aparecerían en ninguna parte. La ficha
 * oculta cada bloque que llegue vacío, así que no cuesta nada mientras no se
 * usen.
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

            // Contenido largo de la ficha. Se envía siempre —también en el
            // listado— porque son unos pocos kB y el sitio se genera una vez,
            // no una petición por visita.
            'objetivos' => $this->resource->objetivos_academicos ?: null,
            'plan_estudios' => $this->resource->plan_estudios ?: null,
            'perfil_ingresante' => $this->resource->perfil_ingresante ?: null,
            'perfil_graduado' => $this->resource->perfil_graduado ?: null,
            'por_que' => $this->resource->por_que_text ?: null,
            'vacantes' => $this->resource->vacantes ?: null,
            'duracion' => $this->resource->duracion ?: null,
            'grado_otorga' => $this->resource->grado_otorga_label ?: null,
            'fecha_limite' => $this->resource->fecha_limite_inscripcion
                ? $this->resource->fecha_limite_inscripcion->toDateString()
                : null,

            // Documentos descargables, tal como los sube el panel.
            'documentos' => array_values(array_filter([
                $this->resource->plan_url ? ['titulo' => 'Plan de estudios', 'url' => $this->resource->plan_url] : null,
                $this->resource->horario_url ? ['titulo' => 'Horario', 'url' => $this->resource->horario_url] : null,
                $this->resource->brochure_url ? ['titulo' => 'Brochure', 'url' => $this->resource->brochure_url] : null,
                $this->resource->admision_pdf_url ? ['titulo' => 'Admisión', 'url' => $this->resource->admision_pdf_url] : null,
            ])),
            // URL absoluta y ya resuelta: el accesor aplica el mismo respaldo
            // que el sitio en Blade (una foto del campus segun el tipo) cuando
            // el programa no tiene imagen propia. Enviar `imagen` en crudo
            // obligaria a reimplementar ese respaldo en cada consumidor.
            'imagen' => $this->resource->imagen_url,

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
