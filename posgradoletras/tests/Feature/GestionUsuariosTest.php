<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Gestión de usuarios del panel, con foco en las salvaguardas: el sitio nunca
 * debe quedarse sin administradores ni permitir que alguien se autobloquee.
 */
class GestionUsuariosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $extra = []): User
    {
        return User::factory()->create($extra + ['role' => 'admin', 'is_active' => true]);
    }

    public function test_un_admin_puede_crear_otro_usuario(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), [
                'name' => 'Nueva Persona',
                'email' => 'nueva@unmsm.edu.pe',
                'password' => 'contrasena-larga',
                'password_confirmation' => 'contrasena-larga',
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $creado = User::where('email', 'nueva@unmsm.edu.pe')->first();
        $this->assertNotNull($creado);
        $this->assertSame('admin', $creado->role);
        // La contraseña se guarda hasheada, nunca en claro.
        $this->assertNotSame('contrasena-larga', $creado->password);
        $this->assertTrue(Hash::check('contrasena-larga', $creado->password));
    }

    public function test_la_contrasena_solo_cambia_si_se_escribe_una_nueva(): void
    {
        $admin = $this->admin();
        $otro = $this->admin(['password' => Hash::make('original-larga')]);
        $hashPrevio = $otro->password;

        $this->actingAs($admin)->put(route('admin.users.update', $otro), [
            'name' => 'Nombre Cambiado',
            'email' => $otro->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'admin',
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $otro->refresh();
        $this->assertSame('Nombre Cambiado', $otro->name);
        $this->assertSame($hashPrevio, $otro->password);
    }

    public function test_no_puedo_quitarme_a_mi_mismo_el_acceso(): void
    {
        $admin = $this->admin();
        $this->admin(); // otro admin, para que no sea el "último"

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
            'is_active' => '1',
        ]);

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_no_puedo_eliminar_ni_desactivar_mi_propia_cuenta(): void
    {
        $admin = $this->admin();
        $this->admin();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));
        $this->assertNotNull($admin->fresh());

        $this->actingAs($admin)->post(route('admin.users.toggle', $admin));
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_el_ultimo_admin_activo_no_puede_degradarse_ni_borrarse(): void
    {
        $unico = $this->admin();
        $otroAdmin = $this->admin();

        // Con dos admins, degradar al segundo sí se permite.
        $this->actingAs($unico)->put(route('admin.users.update', $otroAdmin), [
            'name' => $otroAdmin->name,
            'email' => $otroAdmin->email,
            'role' => 'user',
            'is_active' => '1',
        ]);
        $this->assertSame('user', $otroAdmin->fresh()->role);

        // Ahora `$unico` es el único admin activo: no se puede eliminar.
        $this->actingAs($otroAdmin->fresh())->delete(route('admin.users.destroy', $unico));
        $this->assertNotNull($unico->fresh(), 'No debe poder eliminarse al único administrador activo');
    }

    public function test_una_cuenta_desactivada_pierde_el_acceso_al_panel(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();

        $admin->update(['is_active' => false]);

        // Aun con la sesión abierta, el middleware la cierra.
        $this->actingAs($admin)->get(route('admin.users.index'))->assertRedirect('/');
    }

    public function test_un_usuario_sin_rol_admin_no_entra_al_panel(): void
    {
        $usuario = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($usuario)->get(route('admin.users.index'))->assertRedirect('/');
    }
}
