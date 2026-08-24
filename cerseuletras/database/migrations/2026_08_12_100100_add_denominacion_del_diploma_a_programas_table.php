<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Denominación editable del título que otorga el programa (Obs. N.º 4).
     *
     * La columna `grado_otorga` existía desde el principio, pero un accesor del
     * modelo la ignoraba y componía siempre el texto («Magíster en …»), y el
     * hero lo rotulaba fijo como «Grado que otorga: …». En un diplomado eso es
     * incorrecto: no se trata de un grado académico.
     *
     * Se añade el rótulo como campo propio y se congela en la base de datos lo
     * que hasta ahora calculaba el accesor, de modo que maestrías y doctorados
     * sigan mostrando exactamente el mismo texto tras retirar la generación
     * automática. Los diplomados se dejan vacíos a propósito: el documento pide
     * que el campo pueda quedar en blanco mientras se confirma la denominación.
     */
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->string('grado_otorga_label', 100)->nullable()->after('grado_otorga');
        });

        DB::table('programas')
            ->select('id', 'grado', 'nombre', 'mencion', 'grado_otorga')
            ->orderBy('id')
            ->chunkById(100, function ($programas) {
                foreach ($programas as $programa) {
                    $esDiplomado = $programa->grado === 'Diplomado';
                    $actual = trim((string) $programa->grado_otorga);

                    // Diplomados: nunca se rellena solo. Si alguien ya había
                    // escrito un texto, se conserva con el rótulo del documento.
                    if ($esDiplomado) {
                        if ($actual !== '') {
                            DB::table('programas')->where('id', $programa->id)
                                ->update(['grado_otorga_label' => 'Otorga']);
                        }

                        continue;
                    }

                    DB::table('programas')->where('id', $programa->id)->update([
                        'grado_otorga' => $actual !== '' ? $actual : $this->textoAutomatico($programa),
                        'grado_otorga_label' => 'Grado que otorga',
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('grado_otorga_label');
        });
    }

    /**
     * Réplica exacta del accesor que se retira, para no alterar ni un texto ya
     * publicado al pasar de un valor calculado a uno almacenado.
     */
    private function textoAutomatico(object $programa): string
    {
        $prefijo = match ($programa->grado) {
            'Doctorado' => 'Doctor en ',
            'Diplomado' => 'Diplomado en ',
            default => 'Magíster en ',
        };

        $texto = Str::startsWith(Str::lower($programa->nombre), ['diplomado', 'doctorado', 'maestría', 'maestria'])
            ? $programa->nombre
            : $prefijo . $programa->nombre;

        if (!empty($programa->mencion)) {
            $texto .= ' con mención en ' . $programa->mencion;
        }

        return $texto;
    }
};
