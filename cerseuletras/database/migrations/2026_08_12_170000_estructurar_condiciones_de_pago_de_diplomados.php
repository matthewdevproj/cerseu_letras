<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Condiciones de pago como lista administrable.
     *
     * Se armaban a partir de tres campos sueltos dentro de
     * `inversion_economica` —`modalidades_pago` en texto libre, `descuentos` y
     * `observaciones`—, de modo que el bloque solo podía tener tres puntos y
     * cada uno con un significado fijo. Se pasan a la clave `condiciones`, una
     * lista que se edita entera desde el panel.
     *
     * No se toca ninguna columna: `inversion_economica` ya es JSON. Los tres
     * campos anteriores se conservan además de convertirse, igual que se hizo
     * con las cuotas, para que una reversión no pierda texto.
     */
    public function up(): void
    {
        $this->recorrerDiplomados(function (array $inv) {
            if (!empty($inv['condiciones'])) {
                return null;   // ya estructurado
            }

            $condiciones = [];

            $modalidades = array_values(array_filter(array_map(
                fn ($m) => trim((string) $m),
                (array) ($inv['modalidades_pago'] ?? [])
            )));

            if ($modalidades !== []) {
                $condiciones[] = 'Modalidades de pago: ' . implode(', ', $modalidades) . '.';
            }

            foreach (['descuentos', 'observaciones'] as $clave) {
                $texto = trim((string) ($inv[$clave] ?? ''));

                if ($texto !== '') {
                    $condiciones[] = $texto;
                }
            }

            if ($condiciones === []) {
                return null;
            }

            $inv['condiciones'] = $condiciones;

            return $inv;
        });
    }

    public function down(): void
    {
        $this->recorrerDiplomados(function (array $inv) {
            if (!array_key_exists('condiciones', $inv)) {
                return null;
            }

            unset($inv['condiciones']);

            return $inv;
        });
    }

    /**
     * Aplica una transformación a `inversion_economica` de cada diplomado.
     * El callback devuelve el array modificado o `null` para dejarlo intacto.
     */
    private function recorrerDiplomados(callable $transformar): void
    {
        DB::table('programas')
            ->select('id', 'inversion_economica')
            ->where('grado', 'Diplomado')
            ->whereNotNull('inversion_economica')
            ->orderBy('id')
            ->chunkById(100, function ($programas) use ($transformar) {
                foreach ($programas as $programa) {
                    $inv = json_decode((string) $programa->inversion_economica, true);

                    if (!is_array($inv)) {
                        continue;
                    }

                    $nuevo = $transformar($inv);

                    if ($nuevo === null) {
                        continue;
                    }

                    DB::table('programas')->where('id', $programa->id)->update([
                        'inversion_economica' => json_encode($nuevo, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });
    }
};
