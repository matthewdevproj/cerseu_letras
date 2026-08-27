<?php

namespace App\Support;

use App\Models\TipoOferta;

/**
 * Las direcciones del sitio público, que ya no son rutas de Laravel.
 *
 * El menú y el buscador guardan un nombre lógico —«nosotros», «cursos.index»—
 * y hasta ahora se resolvía con `route()`. Al pasar el sitio a Astro esas rutas
 * dejaron de existir en Laravel: `route()` no las encuentra, el menú devolvía
 * null en todos sus elementos y la navegación se quedaba sin destinos.
 *
 * El nombre lógico se conserva —es lo que hay guardado en la base y lo que
 * ofrece el panel, y sobrevive a un cambio de URL igual que antes—, pero quien
 * lo traduce es esta tabla. Es el reparto que ya hace Nginx, escrito una vez:
 * Laravel administra el contenido y el sitio decide dónde vive cada página.
 *
 * Un nombre desconocido devuelve null, como hacía `Route::has()`: un elemento
 * de menú que apunta a algo retirado se omite, no revienta la barra entera.
 */
class DestinosPublicos
{
    /**
     * @return array<string, string>
     */
    public static function mapa(): array
    {
        $destinos = [
            'home' => '/',
            'search' => '/buscar',
            'nosotros' => '/nosotros',
            'admision' => '/admision',
            'tramites' => '/tramites',
            'cronograma' => '/cronograma',
            'directorio' => '/directorio',
            'eventos.index' => '/eventos',
            'informativos.index' => '/informativos',
            // El listado de docentes cambió de dirección al migrar: se llama
            // por lo que es y no por quiénes son.
            'profesores.index' => '/plana-docente',
        ];

        // Los tres módulos de oferta, generados desde el enum: añadir un tipo
        // no debe obligar a acordarse de este archivo.
        foreach (TipoOferta::cases() as $tipo) {
            $destinos[$tipo->slug() . '.index'] = '/' . $tipo->slug();
            $destinos[$tipo->slug() . '.admision'] = '/' . $tipo->slug() . '/admision';
        }

        return $destinos;
    }

    /** La ruta de un nombre lógico, o null si ya no existe. */
    public static function ruta(?string $nombre): ?string
    {
        if (! $nombre) {
            return null;
        }

        return self::mapa()[$nombre] ?? null;
    }

    /** La ficha de un programa. */
    public static function programa(TipoOferta $tipo, string $slug): string
    {
        return '/' . $tipo->slug() . '/' . $slug;
    }

    /** La ficha de un docente. */
    public static function docente(string $slug): string
    {
        return '/profesores/' . $slug;
    }
}
