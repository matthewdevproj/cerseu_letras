@props([
    'usuario' => null,
    'esUltimoAdmin' => false,
])

@php $editando = $usuario !== null; @endphp

<div class="grid md:grid-cols-2 gap-5">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
        <input id="name" type="text" name="name" required
            value="{{ old('name', $usuario?->name) }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('name') border-red-500 @enderror">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo *</label>
        <input id="email" type="email" name="email" required
            value="{{ old('email', $usuario?->email) }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('email') border-red-500 @enderror">
        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Contraseña {{ $editando ? '' : '*' }}
        </label>
        <input id="password" type="password" name="password" autocomplete="new-password"
            {{ $editando ? '' : 'required' }}
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('password') border-red-500 @enderror">
        <p class="text-xs text-gray-400 mt-1">
            {{ $editando ? 'Déjala vacía para no cambiarla.' : 'Mínimo 8 caracteres.' }}
        </p>
        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Repetir contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
            {{ $editando ? '' : 'required' }}
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
    </div>

    <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
            <option value="admin" @selected(old('role', $usuario?->role) === 'admin')>Administrador — acceso completo al panel</option>
            <option value="user" @selected(old('role', $usuario?->role ?? 'admin') === 'user')>Usuario — sin acceso al panel</option>
        </select>
        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-end">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                @checked(old('is_active', $usuario?->is_active ?? true))
                class="h-5 w-5 text-unmsm-azul border-gray-300 rounded focus:ring-unmsm-azul">
            <span class="text-sm font-medium text-gray-700">Cuenta activa</span>
        </label>
    </div>
</div>

@if ($esUltimoAdmin)
    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg p-4 mt-5 text-sm text-amber-800">
        <x-fas-triangle-exclamation class="mt-0.5 flex-shrink-0" aria-hidden="true" />
        <span>
            Es el <strong>único administrador activo</strong>: no se puede cambiar su rol ni desactivarlo
            hasta que exista otro. El cambio se rechazará al guardar.
        </span>
    </div>
@endif
