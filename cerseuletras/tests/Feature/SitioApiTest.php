<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La identidad y el menú son lo que hace que el sitio en Astro no tenga
 * contenido cableado. Si estos endpoints devuelven algo distinto de lo que se
 * editó en el panel, el frontend acaba con textos escritos a mano — el mismo
 * fallo que se corrigió en los seeders, en la migración y en el fallback de un
 * controlador, solo que en otro lenguaje.
 */
class SitioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_identidad_sale_de_lo_que_se_edita_en_el_panel(): void
    {
        SiteSetting::get()->update([
            'site_name' => 'CERSEU de Prueba',
            'email' => 'prueba@unmsm.edu.pe',
            'telefono' => '999 888 777',
            'anexo' => '1234',
        ]);
        SiteSetting::clearCache();

        $this->getJson('/api/v1/sitio')
            ->assertOk()
            ->assertJsonPath('data.nombre', 'CERSEU de Prueba')
            ->assertJsonPath('data.contacto.email', 'prueba@unmsm.edu.pe')
            ->assertJsonPath('data.contacto.anexo', '1234');
    }

    public function test_el_whatsapp_se_deriva_del_telefono(): void
    {
        SiteSetting::get()->update(['telefono' => '914 033 129']);
        SiteSetting::clearCache();

        // No es un campo aparte que pueda contradecir al teléfono: se calcula.
        $this->getJson('/api/v1/sitio')
            ->assertOk()
            ->assertJsonPath('data.contacto.whatsapp', 'https://wa.me/51914033129');
    }

    public function test_las_redes_vacias_no_se_publican(): void
    {
        // Se limpian todas y se deja una: la fila que crea la migracion ya
        // trae las redes del CERSEU, y sin vaciarlas la prueba no comprobaria
        // nada.
        SiteSetting::get()->update([
            'facebook' => 'https://facebook.test/cerseu',
            'instagram' => null,
            'tiktok' => null,
            'youtube' => '',
            'linkedin' => '',
        ]);
        SiteSetting::clearCache();

        $redes = $this->getJson('/api/v1/sitio')->assertOk()->json('data.redes');

        $this->assertSame(['facebook' => 'https://facebook.test/cerseu'], $redes);
    }

    public function test_el_hero_de_la_portada_sale_de_configuracion(): void
    {
        SiteSetting::get()->update([
            'home_hero_titulo' => 'Titular editado',
            'home_hero_cta1_texto' => 'Ver cursos',
            'home_hero_cta1_url' => '/cursos',
            'home_hero_cta2_texto' => null,
        ]);
        SiteSetting::clearCache();

        $portada = $this->getJson('/api/v1/sitio')->assertOk()->json('data.portada');

        $this->assertSame('Titular editado', $portada['titulo']);
        // Una accion sin texto no se publica como boton vacio.
        $this->assertCount(1, $portada['acciones']);
        $this->assertSame('/cursos', $portada['acciones'][0]['url']);
        $this->assertNotEmpty($portada['imagenes']);
    }

    public function test_el_menu_llega_anidado_y_con_las_urls_resueltas(): void
    {
        $padre = MenuItem::create([
            'etiqueta' => 'Oferta', 'orden' => 0, 'is_visible' => true,
        ]);
        MenuItem::create([
            'etiqueta' => 'Cursos', 'route_name' => 'cursos.index',
            'parent_id' => $padre->id, 'orden' => 0, 'is_visible' => true,
        ]);

        $menu = $this->getJson('/api/v1/menu')->assertOk()->json('data');

        $oferta = collect($menu)->firstWhere('etiqueta', 'Oferta');
        $this->assertNotNull($oferta);
        $this->assertCount(1, $oferta['hijos']);

        // Ruta RELATIVA, no la URL absoluta que devuelve route(): esa apunta a
        // esta aplicacion, y en un frontend desacoplado sacaba al visitante del
        // sitio que estaba viendo —pulsar «Cursos» en la cabecera del sitio en
        // Astro te llevaba al de Blade—. Una ruta interna es una ruta, no una
        // direccion: cada frontend la resuelve contra su propio origen.
        $this->assertSame('/cursos', $oferta['hijos'][0]['enlace']);
        $this->assertStringStartsNotWith('http', $oferta['hijos'][0]['enlace']);
    }

    public function test_los_enlaces_externos_del_menu_se_respetan_tal_cual(): void
    {
        MenuItem::create([
            'etiqueta' => 'Web Letras',
            'url' => 'https://letras.unmsm.edu.pe/',
            'nueva_pestana' => true,
            'orden' => 0,
            'is_visible' => true,
        ]);

        // Una URL escrita a mano en el panel apunta a donde quiso quien la
        // escribio: no se toca.
        $item = collect($this->getJson('/api/v1/menu')->json('data'))
            ->firstWhere('etiqueta', 'Web Letras');

        $this->assertSame('https://letras.unmsm.edu.pe/', $item['enlace']);
        $this->assertTrue($item['nueva_pestana']);
    }

    public function test_el_menu_omite_lo_oculto(): void
    {
        MenuItem::create(['etiqueta' => 'Visible', 'url' => '/a', 'orden' => 0, 'is_visible' => true]);
        MenuItem::create(['etiqueta' => 'Oculto', 'url' => '/b', 'orden' => 1, 'is_visible' => false]);

        $etiquetas = collect($this->getJson('/api/v1/menu')->json('data'))->pluck('etiqueta');

        $this->assertContains('Visible', $etiquetas);
        $this->assertNotContains('Oculto', $etiquetas);
    }
}
