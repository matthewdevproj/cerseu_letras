<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSiteSettingsController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function index()
    {
        $settings = SiteSetting::first();

        // Si no existe, crear uno vacío
        if (!$settings) {
            $settings = SiteSetting::create([
                'site_name' => 'CERSEU - Facultad de Letras y Ciencias Humanas',
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,gif,svg|max:512',
            'talleres_hero_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'cursos_hero_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'email' => 'nullable|email|max:255',
            'email_admision' => 'nullable|email|max:255',
            'email_tramites' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'anexo' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'horario_atencion' => 'nullable|string|max:255',
            'talleres_hero_titulo' => 'nullable|string|max:255',
            'talleres_hero_texto' => 'nullable|string',
            'talleres_hero_claim' => 'nullable|string|max:255',
            'cursos_hero_titulo' => 'nullable|string|max:255',
            'cursos_hero_texto' => 'nullable|string',
            'cursos_hero_claim' => 'nullable|string|max:255',
            'home_hero_kicker' => 'nullable|string|max:255',
            'home_hero_titulo' => 'nullable|string|max:255',
            'home_hero_texto' => 'nullable|string',
            'home_hero_cta1_texto' => 'nullable|string|max:60',
            'home_hero_cta1_url' => 'nullable|string|max:500',
            'home_hero_cta2_texto' => 'nullable|string|max:60',
            'home_hero_cta2_url' => 'nullable|string|max:500',
            'home_stat_docentes' => 'nullable|integer|min:0|max:9999',
            'facebook' => 'nullable|url|max:500',
            'instagram' => 'nullable|url|max:500',
            'twitter' => 'nullable|url|max:500',
            'linkedin' => 'nullable|url|max:500',
            'youtube' => 'nullable|url|max:500',
            'tiktok' => 'nullable|url|max:500',
            'web_facultad' => 'nullable|url|max:500',
            'directorio_facultad' => 'nullable|url|max:500',
        ]);

        $settings = SiteSetting::first();

        if (!$settings) {
            $settings = new SiteSetting();
        }

        // Campos de texto
        $settings->site_name = $validated['site_name'];
        $settings->site_description = $validated['site_description'] ?? null;
        $settings->email = $validated['email'] ?? null;
        $settings->email_admision = $validated['email_admision'] ?? null;
        $settings->email_tramites = $validated['email_tramites'] ?? null;
        $settings->telefono = $validated['telefono'] ?? null;
        $settings->anexo = $validated['anexo'] ?? null;
        $settings->direccion = $validated['direccion'] ?? null;
        $settings->horario_atencion = $validated['horario_atencion'] ?? null;
        // Un hero por módulo (talleres y cursos), con los mismos tres campos.
        foreach (\App\Models\TipoOferta::cases() as $tipo) {
            foreach (['titulo', 'texto', 'claim'] as $campo) {
                $llave = $tipo->prefijoHero() . '_hero_' . $campo;
                $settings->{$llave} = $validated[$llave] ?? null;
            }
        }
        $settings->home_hero_kicker = $validated['home_hero_kicker'] ?? null;
        $settings->home_hero_titulo = $validated['home_hero_titulo'] ?? null;
        $settings->home_hero_texto = $validated['home_hero_texto'] ?? null;
        $settings->home_hero_cta1_texto = $validated['home_hero_cta1_texto'] ?? null;
        $settings->home_hero_cta1_url = $validated['home_hero_cta1_url'] ?? null;
        $settings->home_hero_cta2_texto = $validated['home_hero_cta2_texto'] ?? null;
        $settings->home_hero_cta2_url = $validated['home_hero_cta2_url'] ?? null;
        $settings->home_stat_docentes = $validated['home_stat_docentes'] ?? null;
        $settings->facebook = $validated['facebook'] ?? null;
        $settings->instagram = $validated['instagram'] ?? null;
        $settings->twitter = $validated['twitter'] ?? null;
        $settings->linkedin = $validated['linkedin'] ?? null;
        $settings->youtube = $validated['youtube'] ?? null;
        $settings->tiktok = $validated['tiktok'] ?? null;
        $settings->web_facultad = $validated['web_facultad'] ?? null;
        $settings->directorio_facultad = $validated['directorio_facultad'] ?? null;

        // Manejar subida de Logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            // Guardar nuevo logo
            $logoPath = \App\Support\OptimizadorImagen::guardar($request->file('logo'), 'settings', 'public', 700);
            $settings->logo_path = $logoPath;
        } elseif ($request->has('remove_logo') && $request->remove_logo) {
            // Eliminar logo si se marcó checkbox
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = null;
        }

        // Imagen del hero de cada módulo (talleres y cursos).
        foreach (\App\Models\TipoOferta::cases() as $tipo) {
            $campo = $tipo->prefijoHero() . '_hero_imagen';

            if ($request->hasFile($campo)) {
                if ($settings->{$campo} && Storage::disk('public')->exists($settings->{$campo})) {
                    Storage::disk('public')->delete($settings->{$campo});
                }
                $settings->{$campo} = \App\Support\OptimizadorImagen::guardar($request->file($campo), 'settings');
            } elseif ($request->boolean('remove_' . $campo)) {
                if ($settings->{$campo} && Storage::disk('public')->exists($settings->{$campo})) {
                    Storage::disk('public')->delete($settings->{$campo});
                }
                $settings->{$campo} = null;
            }
        }

        // Manejar subida de Favicon
        if ($request->hasFile('favicon')) {
            // Eliminar favicon anterior si existe
            if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            // Guardar nuevo favicon
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            $settings->favicon_path = $faviconPath;
        } elseif ($request->has('remove_favicon') && $request->remove_favicon) {
            // Eliminar favicon si se marcó checkbox
            if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $settings->favicon_path = null;
        }

        $settings->save();

        // Limpiar caché
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
