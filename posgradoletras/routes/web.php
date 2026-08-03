<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\NosotrosController;
use App\Http\Controllers\TestimonioController;
use App\Http\Controllers\InstitucionalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DirectorioController;
use App\Http\Controllers\DiplomadoController;
use App\Http\Controllers\DiplomadoLeadController;

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Buscador global
Route::get('/buscar', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/buscar/sugerencias', [App\Http\Controllers\SearchController::class, 'suggest'])->name('search.suggest');

// Directorio de Posgrado
Route::get('/directorio', [DirectorioController::class, 'index'])->name('directorio');



// Programas (Maestrías y Doctorados)
Route::get('/programas', [ProgramaController::class, 'index'])->name('programas.index');
Route::get('/programas/{slug}', [ProgramaController::class, 'show'])->name('programas.show');

// Diplomados (sección exclusiva, separada de Maestrías y Doctorados)
Route::get('/diplomados', [DiplomadoController::class, 'index'])->name('diplomados.index');
Route::get('/diplomados/admision', [DiplomadoController::class, 'admision'])->name('diplomados.admision');
Route::post('/diplomados/solicitud', [DiplomadoLeadController::class, 'store'])->name('diplomados.solicitud');

// Profesores
Route::get('/profesores', [ProfesorController::class, 'index'])->name('profesores.index');
Route::get('/profesores/programa/{slug}', [ProfesorController::class, 'byPrograma'])->name('profesores.programa');
Route::get('/profesores/{slug}', [ProfesorController::class, 'show'])->name('profesores.show');

// Admisión
Route::get('/admision', [AdmisionController::class, 'index'])->name('admision');

// Trámites
Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites');

// Cronograma Académico
Route::get('/cronograma', function () {
    $cronograma = \App\Models\Cronograma::getActive();
    return view('cronograma.index', compact('cronograma'));
})->name('cronograma');

// Nosotros
// Países y regiones del formulario de diplomados. El sitio hace de
// intermediario con el servicio externo para no añadir peticiones a terceros
// desde el navegador del visitante (ver App\Services\GeografiaService).
// La ruta lleva versión: al cambiar el formato de los datos —como el paso de
// códigos ISO3 a ISO2— las respuestas ya cacheadas en los navegadores no
// colisionan con las nuevas. Subir el número invalida todo de golpe.
Route::get('/geografia/v2/paises', [App\Http\Controllers\GeografiaController::class, 'paises'])->name('geografia.paises');
Route::get('/geografia/v2/paises/{codigo}/regiones', [App\Http\Controllers\GeografiaController::class, 'regiones'])->name('geografia.regiones');

Route::get('/nosotros', [NosotrosController::class, 'index'])->name('nosotros');

// Rutas de Testimonios
Route::prefix('testimonios')->group(function () {
    Route::get('/', [TestimonioController::class, 'index'])->name('testimonios.index');
    Route::get('/recientes/{limit?}', [TestimonioController::class, 'recientes'])->name('testimonios.recientes');
});

// Informativos (Documentos y Recursos)
Route::get('/informativos', [\App\Http\Controllers\InformativoController::class, 'index'])->name('informativos.index');

// Eventos
Route::get('/eventos', [\App\Http\Controllers\EventoController::class, 'index'])->name('eventos.index');

// Rutas Institucionales (adicionales)
Route::prefix('institucional')->group(function () {
    Route::get('/', [InstitucionalController::class, 'index'])->name('institucional.index');
    Route::get('/autoridades', [InstitucionalController::class, 'autoridades'])->name('institucional.autoridades');
    Route::get('/profesores', [InstitucionalController::class, 'profesores'])->name('institucional.profesores');
    Route::get('/profesores/{id}', [InstitucionalController::class, 'showProfesor'])->name('institucional.profesor');
});

// Admin Routes - Protected by auth and isAdmin middleware
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Programas Management
    Route::resource('programas', App\Http\Controllers\Admin\AdminProgramaController::class);
    Route::post('programas/{programa}/toggle', [App\Http\Controllers\Admin\AdminProgramaController::class, 'toggleActive'])->name('programas.toggle');

    // Docentes Management
    Route::resource('docentes', App\Http\Controllers\Admin\AdminDocenteController::class);
    Route::post('docentes/{docente}/toggle', [App\Http\Controllers\Admin\AdminDocenteController::class, 'toggleActive'])->name('docentes.toggle');

    // Testimonios Management
    Route::resource('testimonios', App\Http\Controllers\Admin\AdminTestimonioController::class);
    Route::post('testimonios/{testimonio}/toggle', [App\Http\Controllers\Admin\AdminTestimonioController::class, 'togglePublished'])->name('testimonios.toggle');

    // Directorio Management
    Route::resource('directorio', App\Http\Controllers\Admin\AdminDirectorioController::class);
    Route::post('directorio/{directorio}/toggle', [App\Http\Controllers\Admin\AdminDirectorioController::class, 'toggleActive'])->name('directorio.toggle');
    Route::post('directorio/{directorio}/move-up', [App\Http\Controllers\Admin\AdminDirectorioController::class, 'moveUp'])->name('directorio.moveUp');
    Route::post('directorio/{directorio}/move-down', [App\Http\Controllers\Admin\AdminDirectorioController::class, 'moveDown'])->name('directorio.moveDown');

    // Site Settings
    Route::get('settings', [App\Http\Controllers\Admin\AdminSiteSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Admin\AdminSiteSettingsController::class, 'update'])->name('settings.update');

    // Documents Management
    Route::post('documents/upload-ajax', [App\Http\Controllers\Admin\AdminDocumentController::class, 'uploadAjax'])->name('documents.uploadAjax');
    Route::resource('documents', App\Http\Controllers\Admin\AdminDocumentController::class);
    Route::post('documents/{document}/toggle', [App\Http\Controllers\Admin\AdminDocumentController::class, 'togglePublished'])->name('documents.toggle');

    // Cronograma Management
    Route::get('cronograma', [App\Http\Controllers\Admin\AdminCronogramaController::class, 'index'])->name('cronograma.index');
    Route::put('cronograma', [App\Http\Controllers\Admin\AdminCronogramaController::class, 'update'])->name('cronograma.update');

    // Contenido de páginas largas (/tramites, /admision)
    Route::get('contenido', [App\Http\Controllers\Admin\AdminContentPageController::class, 'index'])->name('contenido.index');
    Route::get('contenido/{slug}', [App\Http\Controllers\Admin\AdminContentPageController::class, 'edit'])->name('contenido.edit');
    Route::put('contenido/{slug}', [App\Http\Controllers\Admin\AdminContentPageController::class, 'update'])->name('contenido.update');

    // Usuarios del panel
    Route::resource('users', App\Http\Controllers\Admin\AdminUserController::class)->except(['show']);
    Route::post('users/{user}/toggle', [App\Http\Controllers\Admin\AdminUserController::class, 'toggleActive'])->name('users.toggle');

    // Solicitudes de información de diplomados (leads del formulario público)
    Route::get('leads', [App\Http\Controllers\Admin\AdminDiplomadoLeadController::class, 'index'])->name('leads.index');
    Route::get('leads/export', [App\Http\Controllers\Admin\AdminDiplomadoLeadController::class, 'export'])->name('leads.export');
    Route::post('leads/{lead}/reenviar-aviso', [App\Http\Controllers\Admin\AdminDiplomadoLeadController::class, 'reenviarAviso'])->name('leads.reenviar');
    Route::delete('leads/{lead}', [App\Http\Controllers\Admin\AdminDiplomadoLeadController::class, 'destroy'])->name('leads.destroy');

    // Cronograma de Admisión (sección de la portada)
    // Papelera única: lo borrado en cualquier sección se recupera desde aquí.
    Route::get('papelera', [App\Http\Controllers\Admin\AdminPapeleraController::class, 'index'])->name('papelera.index');
    Route::post('papelera/{tipo}/{id}/restaurar', [App\Http\Controllers\Admin\AdminPapeleraController::class, 'restaurar'])->name('papelera.restaurar');

    // Anuncios del popup de la portada.
    Route::post('anuncios/ajustes', [App\Http\Controllers\Admin\AdminAnuncioController::class, 'ajustes'])->name('anuncios.ajustes');
    Route::get('anuncios/papelera', [App\Http\Controllers\Admin\AdminAnuncioController::class, 'papelera'])->name('anuncios.papelera');
    Route::post('anuncios/{id}/restaurar', [App\Http\Controllers\Admin\AdminAnuncioController::class, 'restaurar'])->name('anuncios.restaurar');
    Route::post('anuncios/{anuncio}/toggle', [App\Http\Controllers\Admin\AdminAnuncioController::class, 'toggle'])->name('anuncios.toggle');
    Route::resource('anuncios', App\Http\Controllers\Admin\AdminAnuncioController::class)->except(['show']);

    // Menú de navegación (antes escrito a mano en navbar.blade.php).
    Route::get('menu', [App\Http\Controllers\Admin\AdminMenuController::class, 'index'])->name('menu.index');
    Route::put('menu', [App\Http\Controllers\Admin\AdminMenuController::class, 'update'])->name('menu.update');

    Route::get('cronograma-admision', [App\Http\Controllers\Admin\AdminCronogramaAdmisionController::class, 'index'])->name('cronograma-admision.index');
    Route::put('cronograma-admision', [App\Http\Controllers\Admin\AdminCronogramaAdmisionController::class, 'update'])->name('cronograma-admision.update');

    // Admisión Diplomados Management
    Route::get('admision-diplomados', [App\Http\Controllers\Admin\AdminAdmisionDiplomadoController::class, 'index'])->name('admision-diplomados.index');
    Route::put('admision-diplomados', [App\Http\Controllers\Admin\AdminAdmisionDiplomadoController::class, 'update'])->name('admision-diplomados.update');

    // Informativos Management
    Route::resource('informativos', App\Http\Controllers\Admin\AdminInformativoController::class);
    Route::post('informativos/reorder', [App\Http\Controllers\Admin\AdminInformativoController::class, 'reorder'])->name('informativos.reorder');

    // Eventos Management
    Route::resource('eventos', App\Http\Controllers\Admin\AdminEventoController::class);
});

// Breeze default routes (Profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication Routes
require __DIR__ . '/auth.php';
