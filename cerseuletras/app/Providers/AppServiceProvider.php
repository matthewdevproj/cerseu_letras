<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    /**
     * Modelos cuyo contenido acaba en el HTML del sitio publico.
     *
     * La lista es explicita a proposito: engancharlo a todos los modelos
     * haria que recibir una solicitud de informacion reconstruyera el sitio,
     * y un Lead no se publica en ninguna parte.
     */
    private const MODELOS_PUBLICADOS = [
        \App\Models\Programa::class,
        \App\Models\Docente::class,
        \App\Models\ContentPage::class,
        \App\Models\ContentSection::class,
        \App\Models\SiteSetting::class,
        \App\Models\MenuItem::class,
        \App\Models\CronogramaAdmision::class,
        \App\Models\CronogramaAdmisionPaso::class,
        \App\Models\AdmisionSetting::class,
        \App\Models\Document::class,
    ];

    public function boot(): void
    {
        foreach (self::MODELOS_PUBLICADOS as $modelo) {
            if (class_exists($modelo)) {
                $modelo::observe(\App\Observers\PublicacionObserver::class);
            }
        }
    }
}
