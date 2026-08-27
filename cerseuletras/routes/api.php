<?php

use App\Http\Controllers\Api\ActualidadApiController;
use App\Http\Controllers\Api\BuscadorApiController;
use App\Http\Controllers\Api\DocenteApiController;
use App\Http\Controllers\Api\OfertaApiController;
use App\Http\Controllers\Api\PaginaApiController;
use App\Http\Controllers\Api\SitioApiController;
use App\Http\Controllers\Api\SolicitudApiController;
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

    // Anuncios de la portada, con los ajustes del popup que los acompanan.
    Route::get('/anuncios', [ActualidadApiController::class, 'anuncios']);

    // Secciones de actualidad y recursos.
    Route::get('/eventos', [ActualidadApiController::class, 'eventos']);
    Route::get('/informativos', [ActualidadApiController::class, 'informativos']);
    Route::get('/cronograma', [ActualidadApiController::class, 'cronograma']);
    Route::get('/testimonios', [ActualidadApiController::class, 'testimonios']);

    // Plana docente.
    Route::get('/docentes', [DocenteApiController::class, 'index']);
    Route::get('/docentes/{slug}', [DocenteApiController::class, 'show']);

    // Indice del buscador: la lista entera, para buscar en el navegador.
    Route::get('/buscador', [BuscadorApiController::class, 'index']);

    // Oferta formativa.
    Route::get('/tipos-oferta', [OfertaApiController::class, 'tipos']);
    Route::get('/programas', [OfertaApiController::class, 'index']);
    Route::get('/programas/{slug}', [OfertaApiController::class, 'show']);
    Route::get('/admision/{slug}', [OfertaApiController::class, 'admision']);

    // Unico endpoint que escribe. El limite por IP es lo que sustituye a la
    // autenticacion: un formulario publico no puede exigir credenciales, pero
    // tampoco puede quedar abierto a que lo inunden.
    Route::post('/solicitudes/{tipo}', [SolicitudApiController::class, 'store'])
        ->middleware('throttle:5,1');
});
