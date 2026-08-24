<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Gestión de usuarios del panel.
 *
 * Incluye salvaguardas para que nadie pueda dejar el sitio sin administradores
 * ni bloquearse a sí mismo por accidente.
 */
class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'usuarios' => User::orderByDesc('role')->orderBy('name')->get(),
            'adminsActivos' => $this->adminsActivos(),
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ], [], [
            'name' => 'nombre',
            'email' => 'correo',
            'password' => 'contraseña',
            'role' => 'rol',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // El cast 'hashed' del modelo se encarga del hash.
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'usuario' => $user,
            'esUltimoAdmin' => $this->esUltimoAdminActivo($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // La contraseña solo cambia si se escribe una nueva.
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ], [], [
            'name' => 'nombre',
            'email' => 'correo',
            'password' => 'contraseña',
            'role' => 'rol',
        ]);

        $activo = $request->boolean('is_active');
        $seguiriaSiendoAdmin = $validated['role'] === 'admin' && $activo;

        // Nadie debe poder quitarse a sí mismo el acceso al panel...
        if ($user->id === $request->user()->id && !$seguiriaSiendoAdmin) {
            return back()->withInput()
                ->with('error', 'No puedes quitarte a ti mismo el rol de administrador ni desactivar tu propia cuenta.');
        }

        // ...ni dejar el sitio sin ningún administrador activo.
        if (!$seguiriaSiendoAdmin && $this->esUltimoAdminActivo($user)) {
            return back()->withInput()
                ->with('error', 'Es el único administrador activo. Asigna otro antes de cambiar este usuario.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $activo;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($this->esUltimoAdminActivo($user)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Es el único administrador activo; el sitio quedaría sin acceso al panel.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado.');
    }

    /**
     * Activa o desactiva rápidamente desde el listado.
     */
    public function toggleActive(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if ($user->is_active && $this->esUltimoAdminActivo($user)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Es el único administrador activo.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario ' . ($user->is_active ? 'activado' : 'desactivado') . '.');
    }

    private function adminsActivos(): int
    {
        return User::admins()->active()->count();
    }

    /**
     * ¿Este usuario es el único administrador activo que queda?
     */
    private function esUltimoAdminActivo(User $user): bool
    {
        return $user->isAdmin()
            && $user->is_active
            && $this->adminsActivos() <= 1;
    }
}
