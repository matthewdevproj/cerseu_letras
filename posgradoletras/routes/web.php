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

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Directorio de Posgrado
Route::get('/directorio', [DirectorioController::class, 'index'])->name('directorio');



// Programas (Maestrías y Doctorados)
Route::get('/programas', [ProgramaController::class, 'index'])->name('programas.index');
Route::get('/programas/{slug}', [ProgramaController::class, 'show'])->name('programas.show');

// Profesores
Route::get('/profesores', [ProfesorController::class, 'index'])->name('profesores.index');
Route::get('/profesores/programa/{slug}', [ProfesorController::class, 'byPrograma'])->name('profesores.programa');
Route::get('/profesores/{id}', [ProfesorController::class, 'show'])->name('profesores.show');

// Admisión
Route::get('/admision', [AdmisionController::class, 'index'])->name('admision');

// Trámites
Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites');

// Nosotros
Route::get('/nosotros', [NosotrosController::class, 'index'])->name('nosotros');

// Rutas de Testimonios
Route::prefix('testimonios')->group(function () {
    Route::get('/', [TestimonioController::class, 'index'])->name('testimonios.index');
    Route::get('/recientes/{limit?}', [TestimonioController::class, 'recientes'])->name('testimonios.recientes');
});

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
});

// Breeze default routes (Profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication Routes
require __DIR__ . '/auth.php';
