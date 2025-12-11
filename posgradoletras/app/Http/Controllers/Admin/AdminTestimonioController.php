<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTestimonioController extends Controller
{
    /**
     * Display a listing of testimonios.
     */
    public function index(Request $request)
    {
        $query = Testimonio::with('programa');

        // Search by name or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('contenido', 'like', '%' . $search . '%');
            });
        }

        // Filter by estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $testimonios = $query->latest()->paginate(15)->withQueryString();

        return view('admin.testimonios.index', compact('testimonios'));
    }

    /**
     * Show the form for creating a new testimonio.
     */
    public function create()
    {
        $programas = Programa::activos()->orderBy('nombre')->get();
        return view('admin.testimonios.create', compact('programas'));
    }

    /**
     * Store a newly created testimonio in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'programa_id' => 'required|exists:programas,id',
            'contenido' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'estado' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonios', 'public');
        }

        Testimonio::create($validated);

        return redirect()->route('admin.testimonios.index')
            ->with('success', 'Testimonio creado exitosamente.');
    }

    /**
     * Show the form for editing the specified testimonio.
     */
    public function edit(Testimonio $testimonio)
    {
        $programas = Programa::activos()->orderBy('nombre')->get();
        return view('admin.testimonios.edit', compact('testimonio', 'programas'));
    }

    /**
     * Update the specified testimonio in storage.
     */
    public function update(Request $request, Testimonio $testimonio)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'programa_id' => 'required|exists:programas,id',
            'contenido' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'estado' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($testimonio->photo) {
                Storage::disk('public')->delete($testimonio->photo);
            }
            $validated['photo'] = $request->file('photo')->store('testimonios', 'public');
        }

        $testimonio->update($validated);

        return redirect()->route('admin.testimonios.index')
            ->with('success', 'Testimonio actualizado exitosamente.');
    }

    /**
     * Remove the specified testimonio from storage.
     */
    public function destroy(Testimonio $testimonio)
    {
        // Delete photo if exists
        if ($testimonio->photo) {
            Storage::disk('public')->delete($testimonio->photo);
        }

        $testimonio->delete();

        return redirect()->route('admin.testimonios.index')
            ->with('success', 'Testimonio eliminado exitosamente.');
    }

    /**
     * Toggle the published status of a testimonio.
     */
    public function togglePublished(Testimonio $testimonio)
    {
        $testimonio->update(['estado' => !$testimonio->estado]);

        $status = $testimonio->estado ? 'publicado' : 'ocultado';
        return redirect()->route('admin.testimonios.index')
            ->with('success', "Testimonio {$status} exitosamente.");
    }
}
