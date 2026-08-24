<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sección de contenido dentro de una página editable.
 */
class ContentSection extends Model
{
    protected $fillable = [
        'content_page_id',
        'grupo',
        'numeral',
        'titulo',
        'cuerpo',
        'orden',
        'is_visible',
    ];


    protected $casts = [
        'orden' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function pagina()
    {
        return $this->belongsTo(ContentPage::class, 'content_page_id');
    }

    /**
     * Cuerpo listo para pintar, con los tokens de contacto resueltos.
     *
     * Se usan tokens en lugar de escribir el correo o el teléfono dentro del
     * HTML para que sigan saliendo de Configuración: si el contenido los
     * congelara, volveríamos al problema de datos de contacto duplicados.
     */
    public function getCuerpoRenderizadoAttribute(): string
    {
        $sustituciones = [
            '{{email_tramites}}' => SiteSetting::contacto('tramites'),
            '{{email_admision}}' => SiteSetting::contacto('admision'),
            '{{email_general}}' => SiteSetting::contacto('general'),
            '{{telefono}}' => SiteSetting::contacto('telefono'),
            '{{whatsapp}}' => SiteSetting::contacto('whatsapp'),
        ];

        $html = strtr((string) $this->cuerpo, array_map(
            fn ($v) => e((string) $v),
            $sustituciones
        ));

        return $this->diferirVideos($this->resolverIconos($html));
    }

    /**
     * Convierte las etiquetas de componente de icono (`<x-fas-check-circle />`)
     * en el SVG correspondiente.
     *
     * El cuerpo se imprime con `{!! !!}` y por tanto NO pasa por Blade: esas
     * etiquetas llegaban al navegador como HTML desconocido y no pintaban nada,
     * de ahí que desaparecieran los checks verdes y los avisos con icono.
     * Aquí se resuelven con el helper de blade-icons, que devuelve SVG inline.
     */
    private function resolverIconos(string $html): string
    {
        return preg_replace_callback(
            '~<x-(fa[srb]-[\w-]+)((?:\s+[\w:@.-]+="[^"]*")*)\s*/?>~',
            function ($m) {
                $clases = '';
                if (preg_match('~\bclass="([^"]*)"~', $m[2], $c)) {
                    $clases = $c[1];
                }

                try {
                    return svg($m[1], $clases)->toHtml();
                } catch (\Throwable $e) {
                    // Un icono inexistente no debe tumbar la página: se omite.
                    return '';
                }
            },
            $html
        );
    }

    /**
     * Convierte los iframes de YouTube en una portada que carga el reproductor
     * al pulsarla.
     *
     * El contenido es HTML plano (no pasa por Blade), así que no puede usar el
     * componente `<x-video-embed>`; se genera el mismo marcado aquí. Sin esto,
     * cada vídeo incrustado descarga el reproductor de YouTube y sus cookies
     * aunque nadie le dé al play — y aplica también a los vídeos que se peguen
     * en el futuro desde el panel.
     */
    private function diferirVideos(string $html): string
    {
        return preg_replace_callback(
            // `\s*` antes del cierre: la etiqueta suele venir repartida en
            // varias líneas y el `</iframe>` no queda pegado al `>`.
            '~<iframe[^>]*src="https?://(?:www\.)?youtube(?:-nocookie)?\.com/embed/([\w-]+)[^"]*"[^>]*>\s*</iframe>~i',
            function ($m) {
                $id = $m[1];
                $titulo = 'Vídeo';
                if (preg_match('~title="([^"]*)"~i', $m[0], $t)) {
                    $titulo = $t[1];
                }

                return view('components.video-embed-raw', [
                    'id' => $id,
                    'title' => $titulo,
                ])->render();
            },
            $html
        );
    }

    /**
     * Tokens admitidos, para mostrarlos como ayuda en el panel.
     */
    public const TOKENS = [
        '{{email_tramites}}' => 'Correo de trámites',
        '{{email_admision}}' => 'Correo de admisión',
        '{{email_general}}' => 'Correo general',
        '{{telefono}}' => 'Teléfono',
        '{{whatsapp}}' => 'Enlace de WhatsApp',
    ];
}
