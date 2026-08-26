<?php

use App\Http\Controllers\Api\OfertaApiController;
use App\Http\Controllers\Api\PaginaApiController;
use App\Http\Controllers\Api\SitioApiController;
use Illuminate\Support\Facades\Route;

/*
 * API de contenido, versionada desde el primer día.
 *
 * El sitio público en Astro se construye contra este contrato y no contra la
 * base de datos. El prefijo /v1 no es ceremonia: cuando haya que cambiar la
 * forma de una respuesta, un sitio ya desplegado tiene que poder seguir
 * pidiendo la anterior mientras se reconstruye.
 */
Route::prefix('v1')->group(function () {
    // Identidad y navegacion: lo que cambia entre unidades.
    Route::get('/sitio', [SitioApiController::class, 'configuracion']);
    Route::get('/menu', [SitioApiController::class, 'menu']);

    // Contenido editable de las paginas largas.
    Route::get('/paginas/{slug}', [PaginaApiController::class, 'mostrar']);

    // Oferta formativa.
    Route::get('/tipos-oferta', [OfertaApiController::class, 'tipos']);
    Route::get('/programas', [OfertaApiController::class, 'index']);
    Route::get('/programas/{slug}', [OfertaApiController::class, 'show']);
});
