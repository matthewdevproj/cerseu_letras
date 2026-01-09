<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of documents.
     */
    public function index(Request $request)
    {
        $query = Document::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        // Get distinct types for filter
        $types = Document::select('type')->distinct()->pluck('type');

        return view('admin.documents.index', compact('documents', 'types'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        return view('admin.documents.create');
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'file' => 'required_without:url|file|max:10240',
            'url' => 'required_without:file|nullable|url',
            'published' => 'boolean',
        ]);

        $data = [
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'published' => $request->boolean('published', true),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['original_name'] = $file->getClientOriginalName();
            $data['url'] = asset('storage/' . $file->store('documents', 'public'));
        } else {
            $data['original_name'] = basename(parse_url($validated['url'], PHP_URL_PATH)) ?: 'external_link';
            $data['url'] = $validated['url'];
        }

        Document::create($data);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento creado exitosamente.');
    }

    /**
     * Show the form for editing a document.
     */
    public function edit(Document $document)
    {
        return view('admin.documents.edit', compact('document'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'original_name' => 'required|string|max:255',
            'url' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'published' => 'boolean',
        ]);

        $data = [
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'original_name' => $validated['original_name'],
            'published' => $request->boolean('published', true),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['original_name'] = $file->getClientOriginalName();
            $data['url'] = asset('storage/' . $file->store('documents', 'public'));
        } elseif ($request->filled('url')) {
            $data['url'] = $validated['url'];
        }

        $document->update($data);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document)
    {
        // Try to delete file if it's local storage
        if (str_contains($document->url, '/storage/')) {
            $path = str_replace(asset('storage/'), '', $document->url);
            Storage::disk('public')->delete($path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento eliminado exitosamente.');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublished(Document $document)
    {
        $document->update(['published' => !$document->published]);

        $status = $document->published ? 'publicado' : 'despublicado';
        return redirect()->route('admin.documents.index')
            ->with('success', "Documento {$status} exitosamente.");
    }

    /**
     * Upload file via AJAX (used by programa forms).
     */
    public function uploadAjax(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
                'type' => 'required|string|max:50',
                'program_name' => 'nullable|string|max:255',
            ]);

            $file = $request->file('file');
            $type = $request->input('type');
            $programName = $request->input('program_name', 'programa');

            // Get original extension
            $extension = $file->getClientOriginalExtension();

            // Clean original name (without extension)
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Construct new filename with timestamp for uniqueness
            $timestamp = now()->format('Ymd_His');
            $newFilename = \Illuminate\Support\Str::slug($programName) . '-' .
                $type . '-' . $timestamp . '.' . $extension;

            // Store with custom name
            $path = $file->storeAs('documents', $newFilename, 'public');
            $url = asset('storage/' . $path);

            // Register in documents table
            $document = Document::create([
                'type' => $type,
                'original_name' => $file->getClientOriginalName(),
                'url' => $url,
                'published' => true,
            ]);

            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $file->getClientOriginalName(),
                'document_id' => $document->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->errors()['file'][0] ?? 'Error de validación',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al subir archivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
