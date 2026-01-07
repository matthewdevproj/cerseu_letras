@extends('layouts.admin')

@section('title', 'Gestión de Informativos')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Documentos y Recursos Informativos</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Formulario de Nuevo/Editar -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Agregar Nuevo Documento</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.informativos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <input type="text" name="categoria" class="form-control" list="categorias-list" required>
                            <datalist id="categorias-list">
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            <small class="text-muted">Ej: Reglamento, Directiva, Información</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tipo</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-pdf" value="0" checked
                                        onclick="toggleUploadType(0)">
                                    <label class="form-check-label" for="tipo-pdf">
                                        <i class="fas fa-file-pdf"></i> Subir PDF / URL
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-link" value="1"
                                        onclick="toggleUploadType(1)">
                                    <label class="form-check-label" for="tipo-link">
                                        <i class="fas fa-external-link-alt"></i> Enlace Externo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="pdf-input-group">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subir Archivo PDF</label>
                            <input type="file" name="file" class="form-control" accept=".pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">O URL directa</label>
                            <input type="url" name="url" class="form-control" placeholder="https://">
                        </div>
                    </div>
                    <div class="row" id="link-input-group" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">URL Externa</label>
                            <input type="url" name="url" class="form-control" placeholder="https://">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </form>
            </div>
        </div>

        <!-- Listado por Categorías -->
        @foreach($informativos as $categoria => $items)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="{{ $items->first()->icono }}"></i> {{ $categoria }} ({{ $items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Título</th>
                                    <th width="10%">Tipo</th>
                                    <th width="10%">Orden</th>
                                    <th width="15%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <i
                                                class="{{ $item->es_pdf ? 'fas fa-file-pdf text-danger' : 'fas fa-external-link-alt text-primary' }}"></i>
                                            {{ $item->titulo }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->es_pdf ? 'bg-danger' : 'bg-info' }}">
                                                {{ $item->es_pdf ? 'PDF' : 'Link' }}
                                            </span>
                                        </td>
                                        <td>{{ $item->orden }}</td>
                                        <td>
                                            @if($item->url)
                                                <a href="{{ $item->url }}" target="_blank" class="btn btn-sm btn-info" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('admin.informativos.destroy', $item) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('¿Eliminar este documento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <script>
        function toggleUploadType(tipo) {
            const pdfGroup = document.getElementById('pdf-input-group');
            const linkGroup = document.getElementById('link-input-group');

            if (tipo === 0) {
                pdfGroup.style.display = 'flex';
                linkGroup.style.display = 'none';
            } else {
                pdfGroup.style.display = 'none';
                linkGroup.style.display = 'flex';
            }
        }
    </script>
@endsection