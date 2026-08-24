<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\User;
use App\Support\OptimizadorImagen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Optimización de las imágenes subidas desde el panel.
 */
class OptimizacionImagenesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_una_imagen_enorme_se_reduce_al_ancho_maximo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/anuncios', [
            'titulo' => 'Cartel sin optimizar',
            'imagen' => UploadedFile::fake()->image('cartel.jpg', 2000, 2500),
            'is_visible' => '1',
        ])->assertRedirect();

        $anuncio = Anuncio::firstOrFail();

        // El personal no tiene por qué saber de compresión: se hace al subir.
        $this->assertSame(OptimizadorImagen::ANCHO_MAXIMO, $anuncio->imagen_ancho);
        $this->assertSame(2000, $anuncio->imagen_alto);   // proporción intacta
        $this->assertStringEndsWith('.webp', $anuncio->imagen);
    }

    public function test_una_imagen_ya_pequena_no_se_agranda(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/anuncios', [
            'titulo' => 'Cartel pequeño',
            'imagen' => UploadedFile::fake()->image('mini.jpg', 500, 625),
            'is_visible' => '1',
        ])->assertRedirect();

        $anuncio = Anuncio::firstOrFail();
        $this->assertSame(500, $anuncio->imagen_ancho);
        $this->assertSame(625, $anuncio->imagen_alto);
    }

    public function test_las_medidas_guardadas_son_las_del_archivo_final(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/anuncios', [
            'titulo' => 'Con medidas',
            'imagen' => UploadedFile::fake()->image('c.jpg', 1800, 2250),
            'is_visible' => '1',
        ]);

        $anuncio = Anuncio::firstOrFail();
        [$ancho, $alto] = OptimizadorImagen::medidas($anuncio->imagen);

        // Si se guardaran las del original, el navegador reservaría un hueco
        // que no corresponde y la ventana daría un salto.
        $this->assertSame($ancho, $anuncio->imagen_ancho);
        $this->assertSame($alto, $anuncio->imagen_alto);
    }

    public function test_el_archivo_optimizado_existe_en_el_disco(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/anuncios', [
            'titulo' => 'Guardado',
            'imagen' => UploadedFile::fake()->image('c.jpg', 2000, 2500),
            'is_visible' => '1',
        ]);

        Storage::disk('public')->assertExists(Anuncio::firstOrFail()->imagen);
    }

    public function test_rechaza_optimizar_lo_que_no_cabe_en_memoria(): void
    {
        // GD descomprime la imagen entera: sin este guardián, un cartel muy
        // grande provocaba un error fatal por memoria agotada —no capturable—
        // y el administrador veía una página en blanco al guardar. En ese caso
        // el archivo se guarda tal cual: mejor pesado que perdido.
        //
        // Se comprueba con números y no con un archivo real porque fabricar
        // una imagen de 6000×8000 agota la memoria del propio test.
        // Un cartel corriente sí cabe.
        $this->assertTrue(OptimizadorImagen::cabeEnMemoria(1200, 1500));

        // ~48 y ~144 megapíxeles: a 4 bytes por píxel se van muy por encima de
        // cualquier límite razonable, así que se rechazan siempre.
        $this->assertFalse(OptimizadorImagen::cabeEnMemoria(6000, 8000));
        $this->assertFalse(OptimizadorImagen::cabeEnMemoria(12000, 12000));
    }
}
