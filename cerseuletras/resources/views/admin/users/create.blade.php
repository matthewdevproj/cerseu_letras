@extends('admin.layout.app')

@section('title', 'Nuevo usuario')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-xl font-bold text-gray-800 mb-1">Nuevo usuario</h1>
            <p class="text-sm text-gray-500 mb-6">Solo los administradores pueden entrar al panel.</p>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                @include('admin.users._form')

                <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-unmsm-azul">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark font-medium">
                        <x-fas-save class="mr-1" aria-hidden="true" /> Crear usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
