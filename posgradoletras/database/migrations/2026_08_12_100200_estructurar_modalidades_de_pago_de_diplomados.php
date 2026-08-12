<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Modalidades de pago con sus cuotas, montos y fechas (Obs. N.º 2).
     *
     * Hasta ahora `inversion_economica` guardaba dos cosas sueltas: una lista
     * plana de `cuotas` y un `modalidades_pago` de texto libre separado por
     * comas. Con eso no se puede expresar lo que pide el documento —«pago
     * único» y «pago fraccionado» como bloques distintos, cada uno con sus
     * cuotas, montos y fechas—, así que se introduce la clave `modalidades`:
     *
     *   modalidades: [
     *     {nombre: 'Pago único', cuotas: [{etiqueta, monto, fecha}]},
     *     {nombre: 'Pago fraccionado', cuotas: [{...}, {...}]},
     *   ]
     *
     * No se toca ninguna columna: `inversion_economica` ya es JSON. Las cuotas
     * planas anteriores se conservan tal cual además de convertirse, para que un
     * despliegue que haya que revertir no pierda importes.
     */
    public function up(): void
    {
        $this->recorrerDiplomados(function (array $inv) {
            // Si ya viene estructurado (re-ejecución), no se toca.
            if (!empty($inv['modalidades'])) {
                return null;
            }

            $cuotas = array_values(array_filter(
                (array) ($inv['cuotas'] ?? []),
                fn ($c) => is_array($c) && (!empty($c['monto']) || !empty($c['fecha']))
            ));

            if ($cuotas === []) {
                return null;
            }

            $total = count($cuotas);

            $inv['modalidades'] = [[
                'nombre' => $total === 1 ? 'Pago único' : 'Pago fraccionado',
                'cuotas' => array_map(fn ($c, $i) => [
                    'etiqueta' => $total === 1 ? 'Cuota única' : 'Cuota ' . ($i + 1),
                    'monto' => isset($c['monto']) && $c['monto'] !== '' ? (float) $c['monto'] : null,
                    'fecha' => $c['fecha'] ?? null,
                ], $cuotas, array_keys($cuotas)),
            ]];

            return $inv;
        });
    }

    public function down(): void
    {
        $this->recorrerDiplomados(function (array $inv) {
            if (!array_key_exists('modalidades', $inv)) {
                return null;
            }

            unset($inv['modalidades']);

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
