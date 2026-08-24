<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Devuelve a los campos nuevos el texto que se guardó antes de borrarlo.
 *
 * Va justo después de `modify_programas_table`, que es donde se crean
 * `perfil_graduado` y compañía. Antes de esa migración no existían, así que no
 * se podía escribir en ellos.
 *
 * Traslado:
 *   descripcion     → sumilla          (si sumilla está vacía)
 *   presentacion    → por_que_text     (si por_que_text está vacío)
 *   perfil_egresado → perfil_graduado  (texto suelto → lista JSON)
 *
 * **Nunca pisa contenido existente**: si el campo destino ya tiene algo, se
 * deja como está. Y la tabla de respaldo se conserva, por si el traslado no
 * encaja con cómo se usaban esos campos en la instalación de origen.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('programas_textos_previos')) {
            return;   // Base nueva: no hay nada que restaurar.
        }

        $columnas = Schema::getColumnListing('programas_textos_previos');

        DB::table('programas_textos_previos')->orderBy('id')->chunk(200, function ($filas) use ($columnas) {
            foreach ($filas as $fila) {
                $programa = DB::table('programas')->where('id', $fila->id)->first();

                if (! $programa) {
                    continue;   // Se borró entre medias.
                }

                $cambios = [];

                if (in_array('descripcion', $columnas, true) && ! empty($fila->descripcion)
                    && empty($programa->sumilla)) {
                    $cambios['sumilla'] = $fila->descripcion;
                }

                if (in_array('presentacion', $columnas, true) && ! empty($fila->presentacion)
                    && empty($programa->por_que_text)) {
                    $cambios['por_que_text'] = $fila->presentacion;
                }

                if (in_array('perfil_egresado', $columnas, true) && ! empty($fila->perfil_egresado)
                    && empty($programa->perfil_graduado)) {
                    $cambios['perfil_graduado'] = json_encode(
                        self::aLista($fila->perfil_egresado),
                        JSON_UNESCAPED_UNICODE
                    );
                }

                if ($cambios) {
                    DB::table('programas')->where('id', $fila->id)->update($cambios);
                }
            }
        });
    }

    public function down(): void
    {
        // No se revierte: vaciar los campos destino podría borrar contenido
        // que alguien haya escrito después a mano. El respaldo sigue ahí.
    }

    /**
     * Convierte un texto corrido en la lista que espera el campo JSON.
     *
     * El perfil del egresado solía escribirse como párrafo o como lista con
     * viñetas. Se parte por saltos de línea y viñetas; si no hay ninguna, queda
     * un único elemento con el texto entero — que es correcto y se puede
     * separar luego desde el panel.
     */
    private static function aLista(string $texto): array
    {
        $limpio = strip_tags(str_replace(['</li>', '</p>', '<br>', '<br/>', '<br />'], "\n", $texto));

        $partes = preg_split('~[\r\n]+|(?:^|\s)[•·▪‣-]\s+~u', $limpio) ?: [];

        $partes = array_values(array_filter(array_map('trim', $partes), fn ($p) => $p !== ''));

        return $partes ?: [trim($limpio)];
    }
};
