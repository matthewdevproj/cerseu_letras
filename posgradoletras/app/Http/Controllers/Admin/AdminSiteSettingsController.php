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
                'site_name' => 'Unidad de Posgrado - Facultad de Letras y Ciencias Humanas',
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
            'email' => 'nullable|email|max:255',
            'email_admision' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'horario_atencion' => 'nullable|string|max:255',
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
        $settings->telefono = $validated['telefono'] ?? null;
        $settings->direccion = $validated['direccion'] ?? null;
        $settings->horario_atencion = $validated['horario_atencion'] ?? null;
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
            $logoPath = $request->file('logo')->store('settings', 'public');
            $settings->logo_path = $logoPath;
        } elseif ($request->has('remove_logo') && $request->remove_logo) {
            // Eliminar logo si se marcó checkbox
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = null;
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
