<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Informativo;
use App\Models\Document;
use Illuminate\Http\Request;

class AdminInformativoController extends Controller
{
    public function index()
    {
        $informativos = Informativo::ordenado()->get()->groupBy('categoria');
        $categorias = Informativo::distinct()->pluck('categoria');

        return view('admin.informativos.index', compact('informativos', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|max:100',
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:0,1',
            'url' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $url = $request->url;

        // Si se subió un archivo, crear documento primero
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $storedPath = $file->store('documents', 'public');
            $urlGenerated = asset('storage/' . $storedPath);

            // Crear documento
            Document::create([
                'type' => 'informativo',
                'title' => $request->titulo,
                'original_name' => $file->getClientOriginalName(),
                'url' => $urlGenerated,
                'published' => true,
            ]);

            $url = $urlGenerated;
        }

        // Calcular próximo orden
        $maxOrden = Informativo::where('categoria', $request->categoria)->max('orden') ?? 0;

        Informativo::create([
            'categoria' => $request->categoria,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'url' => $url,
            'orden' => $maxOrden + 1,
        ]);

        return redirect()->route('admin.informativos.index')
            ->with('success', 'Informativo creado correctamente.');
    }

    public function update(Request $request, Informativo $informativo)
    {
        $request->validate([
            'categoria' => 'required|string|max:100',
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:0,1',
            'url' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $url = $informativo->url;

        // Si se subió nuevo archivo
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $storedPath = $file->store('documents', 'public');
            $urlGenerated = asset('storage/' . $storedPath);

            Document::create([
                'type' => 'informativo',
                'title' => $request->titulo,
                'original_name' => $file->getClientOriginalName(),
                'url' => $urlGenerated,
                'published' => true,
            ]);

            $url = $urlGenerated;
        } elseif ($request->filled('url')) {
            $url = $request->url;
        }

        $informativo->update([
            'categoria' => $request->categoria,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'url' => $url,
        ]);

        return redirect()->route('admin.informativos.index')
            ->with('success', 'Informativo actualizado correctamente.');
    }

    public function destroy(Informativo $informativo)
    {
        $informativo->delete();

        return redirect()->route('admin.informativos.index')
            ->with('success', 'Informativo eliminado correctamente.');
    }

    public function reorder(Request $request)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            Informativo::where('id', $item['id'])->update(['orden' => $item['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
