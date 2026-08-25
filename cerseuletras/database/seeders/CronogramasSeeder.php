<?php

namespace Database\Seeders;

use App\Models\Cronograma;
use Illuminate\Database\Seeder;

/**
 * Crea el cronograma de /cronograma vacío, sin etapas.
 *
 * Hasta agosto de 2026 sembraba trece filas con fechas concretas —«Examen de
 * admisión: 8 de marzo de 2026», «Entrevista personal: 10 al 12 de marzo»,
 * «Matrícula de ingresantes», «Presentación de expedientes (documentos
 * físicos)»— heredadas del proceso de maestrías y doctorados de la Unidad de
 * Posgrado. El CERSEU no toma examen de admisión ni hace entrevistas: son
 * cursos y talleres abiertos a la comunidad.
 *
 * Publicar fechas inventadas es peor que no publicar ninguna: alguien puede
 * organizar su año alrededor de un examen que no existe. La vista ya trae su
 * estado vacío («El cronograma será publicado próximamente»), así que la
 * página se sostiene sin etapas hasta que la Unidad cargue las suyas desde
 * el panel.
 *
 * Las convocatorias reales, con sus fechas de inicio, viven en
 * `admision_cronograma_items` y salen en la admisión de cada tipo de oferta.
 */
class CronogramasSeeder extends Seeder
{
    public function run(): void
    {
        if (Cronograma::query()->exists()) {
            return;
        }

        Cronograma::create([
            'code' => '2026-I',
            'title' => 'Cronograma académico',
            'description' => 'Fechas de las convocatorias del CERSEU. Cada taller, curso y especialización publica además las suyas en su propia página de admisión.',
            'effective_date' => '2026-01-01',
            'is_active' => true,
        ]);
    }
}
