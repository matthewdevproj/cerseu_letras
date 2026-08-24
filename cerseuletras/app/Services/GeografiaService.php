<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Intl\Countries;

/**
 * Países y regiones para el formulario de diplomados.
 *
 * **Países**: de `symfony/intl`, que empaqueta la norma ISO 3166-1 con los
 * nombres de CLDR (los datos de idioma de Unicode). Es la fuente canónica y se
 * actualiza con el paquete; antes venían de un JSON de terceros que solo se
 * refrescaba a mano.
 *
 * **Regiones**: de `resources/data/paises-regiones.json`, que viaja en el
 * repositorio. No hay un equivalente empaquetado y fiable de ISO 3166-2, y
 * esos datos traen fallos —para Perú faltaba Loreto entero—, así que los
 * países que importan llevan su lista fijada a mano.
 *
 * En ningún caso el navegador del visitante habla con un servicio externo.
 */
class GeografiaService
{
    /**
     * Dataset abierto de subdivisiones, leído directamente de su repositorio.
     *
     * Antes se pasaba por `countriesnow.space`, que no es una fuente propia
     * sino un envoltorio de este mismo dataset — y lo entregaba estropeado: a
     * Perú le faltaba Loreto y las tildes, y añadía sufijos en inglés
     * («A Coruña Province»). Aquí llega el dato original, con sus códigos
     * ISO 3166-2 y el tipo de cada subdivisión.
     */
    private const URL = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/json/states.json';

    private const TTL = 60 * 60 * 24 * 30;

    private const CLAVE = 'geografia_subdivisiones';

    private const CLAVE_PAISES = 'geografia_paises';

    /**
     * Cómo se llama la subdivisión en cada país.
     *
     * Decir «Región» en los 249 es impreciso: en Perú son departamentos, en
     * Japón prefecturas y en Alemania estados federados. El formulario cambia
     * la etiqueta según el país elegido.
     */
    private const ETIQUETA_SUBDIVISION = [
        'PE' => 'Departamento', 'BO' => 'Departamento', 'CO' => 'Departamento',
        'UY' => 'Departamento', 'PY' => 'Departamento', 'GT' => 'Departamento',
        'HN' => 'Departamento', 'NI' => 'Departamento', 'SV' => 'Departamento',
        'FR' => 'Departamento',

        'ES' => 'Provincia', 'AR' => 'Provincia', 'EC' => 'Provincia',
        'CA' => 'Provincia', 'IT' => 'Provincia', 'CN' => 'Provincia',
        'CR' => 'Provincia', 'PA' => 'Provincia', 'CU' => 'Provincia',
        'NL' => 'Provincia', 'BE' => 'Provincia',

        'BR' => 'Estado', 'MX' => 'Estado', 'US' => 'Estado',
        'VE' => 'Estado', 'IN' => 'Estado', 'AU' => 'Estado',
        'DE' => 'Estado federado', 'AT' => 'Estado federado',

        'JP' => 'Prefectura',
        'GB' => 'Condado', 'IE' => 'Condado',
        'PT' => 'Distrito', 'CH' => 'Cantón', 'PL' => 'Voivodato',
    ];

    /**
     * Regiones fijadas a mano por encima del archivo.
     *
     * Los datos de subdivisiones tienen fallos: para Perú faltaba Loreto y
     * «Huánuco» llegaba sin tilde. Es el país del que viene prácticamente todo
     * el alumnado, así que aquí manda la división oficial — 24 departamentos
     * más la Provincia Constitucional del Callao.
     */
    private const REGIONES_OFICIALES = [
        'PE' => [
            'Amazonas', 'Áncash', 'Apurímac', 'Arequipa', 'Ayacucho', 'Cajamarca',
            'Callao', 'Cusco', 'Huancavelica', 'Huánuco', 'Ica', 'Junín',
            'La Libertad', 'Lambayeque', 'Lima', 'Loreto', 'Madre de Dios',
            'Moquegua', 'Pasco', 'Piura', 'Puno', 'San Martín', 'Tacna',
            'Tumbes', 'Ucayali',
        ],
    ];

    /**
     * Países ISO 3166-1 en español: `[['nombre' => 'Perú', 'codigo' => 'PE'], …]`.
     *
     * Perú va primero por ser el caso mayoritario; el resto, alfabético.
     */
    public static function paises(): array
    {
        return Cache::remember(self::CLAVE_PAISES, self::TTL, function () {
            $nombres = Countries::getNames('es');

            $peru = ['nombre' => $nombres['PE'] ?? 'Perú', 'codigo' => 'PE'];
            unset($nombres['PE']);

            $porNombre = [];
            foreach ($nombres as $codigo => $nombre) {
                $porNombre[$nombre] = ['nombre' => $nombre, 'codigo' => $codigo];
            }

            $ordenados = self::ordenarEnEspanol(array_keys($porNombre));

            return array_merge([$peru], array_map(fn ($n) => $porNombre[$n], $ordenados));
        });
    }

    /**
     * Regiones de un país por su código ISO alpha-2.
     *
     * Vacío si no se conoce ninguna: hay países sin división administrativa, y
     * en ese caso el formulario deja escribirla a mano.
     */
    public static function regiones(string $codigo): array
    {
        $codigo = strtoupper($codigo);

        if (isset(self::REGIONES_OFICIALES[$codigo])) {
            return self::ordenarEnEspanol(self::REGIONES_OFICIALES[$codigo]);
        }

        return self::subdivisiones()[$codigo] ?? [];
    }

    /** Cómo llamar al campo de subdivisión en este país. */
    public static function etiquetaSubdivision(string $codigo): string
    {
        return self::ETIQUETA_SUBDIVISION[strtoupper($codigo)] ?? 'Región';
    }

    public static function limpiarCache(): void
    {
        Cache::forget(self::CLAVE);
        Cache::forget(self::CLAVE_PAISES);
    }

    /**
     * Descarga las subdivisiones y reescribe el archivo del repositorio.
     *
     * Devuelve cuántos países se guardaron, o null si falló y el archivo se
     * dejó como estaba. Los países ya no dependen de esto: vienen de
     * `symfony/intl`.
     */
    public static function actualizarArchivo(): ?int
    {
        try {
            $respuesta = Http::timeout(60)->acceptJson()->get(self::URL);

            if (! $respuesta->successful()) {
                return null;
            }

            $filas = $respuesta->json() ?? [];

            // Una respuesta corta indica que algo va mal: mejor conservar el
            // archivo que ya funciona.
            if (count($filas) < 4000) {
                return null;
            }

            $crudos = collect($filas)
                ->filter(fn ($f) => ! empty($f['country_code']) && ! empty($f['name']))
                ->groupBy('country_code')
                ->map(fn ($subdivisiones) => collect(self::unSoloNivel($subdivisiones->all()))
                    // Se usa `name`, no `translations.es`: esas traducciones son
                    // automáticas y meten errores — «Berlina» por Berlin, «Bath y
                    // el noreste de Somerset». Son topónimos; el original va bien.
                    ->pluck('name')->filter()->unique()->values()->all())
                ->filter(fn ($regiones) => count($regiones) > 0)
                ->all();

            if (count($crudos) < 150) {
                return null;
            }

            file_put_contents(
                resource_path('data/paises-regiones.json'),
                json_encode($crudos, JSON_UNESCAPED_UNICODE)
            );

            self::limpiarCache();

            return count($crudos);
        } catch (\Throwable $e) {
            Log::warning('No se pudieron actualizar las regiones: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Deja un solo nivel administrativo por país.
     *
     * El dataset mezcla niveles: España traía 50 provincias **y** 17
     * comunidades autónomas en la misma lista, así que el desplegable ofrecía
     * «Andalucía» junto a «Almería». Se conserva el tipo mayoritario y, además,
     * los tipos con dos entradas o menos — que es como sobreviven casos como el
     * Distrito Federal de Brasil o Ceuta y Melilla, sin los cuales faltarían.
     *
     * Es una heurística, no una regla exacta: para el Reino Unido (nueve tipos
     * distintos) el resultado sigue siendo imperfecto, pero mucho mejor que la
     * mezcla completa.
     */
    private static function unSoloNivel(array $subdivisiones): array
    {
        $tipos = array_count_values(array_filter(array_column($subdivisiones, 'type')));

        if (! $tipos) {
            return $subdivisiones;
        }

        arsort($tipos);
        $dominante = array_key_first($tipos);

        $aceptados = array_keys(array_filter(
            $tipos,
            fn ($n, $tipo) => $tipo === $dominante || $n <= 2,
            ARRAY_FILTER_USE_BOTH
        ));

        return array_values(array_filter(
            $subdivisiones,
            fn ($s) => in_array($s['type'] ?? null, $aceptados, true)
        ));
    }

    /** Subdivisiones por código ISO alpha-2, del archivo del repositorio. */
    private static function subdivisiones(): array
    {
        return Cache::remember(self::CLAVE, self::TTL, function () {
            $ruta = resource_path('data/paises-regiones.json');

            if (! is_file($ruta)) {
                Log::warning('Falta el archivo de regiones del repositorio.');

                return [];
            }

            $datos = json_decode((string) file_get_contents($ruta), true);

            if (! is_array($datos)) {
                Log::warning('El archivo de regiones está corrupto.');

                return [];
            }

            // Sin limpiar sufijos: el dato original no los trae, y hacerlo
            // rompía nombres oficiales — «Free State» (Sudáfrica) quedaba en
            // «Free», «Mountain Province» (Filipinas) en «Mountain». Era un
            // parche para lo que estropeaba el intermediario anterior.
            return array_map(
                fn (array $regiones) => self::ordenarEnEspanol(array_map('trim', $regiones)),
                $datos
            );
        });
    }

    /**
     * Ordena respetando el alfabeto español.
     *
     * `sort()` compara bytes: «Áncash» acababa detrás de «Lima», y lo mismo
     * con Ñuble o cualquier nombre acentuado. `Collator` sí conoce el orden
     * del idioma; si la extensión `intl` no está, se compara sobre una copia
     * sin acentos, que da el mismo resultado en la práctica.
     */
    private static function ordenarEnEspanol(array $textos): array
    {
        $textos = array_values($textos);

        if (class_exists(\Collator::class)) {
            $collator = new \Collator('es_ES');
            $collator->sort($textos);

            return $textos;
        }

        usort($textos, fn ($a, $b) => strcasecmp(self::sinAcentos($a), self::sinAcentos($b)));

        return $textos;
    }

    private static function sinAcentos(string $texto): string
    {
        return strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N',
        ]);
    }
}
