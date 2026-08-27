<?php

namespace Database\Seeders;

use App\Models\AdmisionSetting;
use App\Models\TipoOferta;
use Illuminate\Database\Seeder;

/**
 * Crea la fila de ajustes de admisión de cada tipo de oferta. Solo la fila.
 *
 * Antes sembraba el proceso de admisión entero de la Unidad de Posgrado, y en
 * un CERSEU recién instalado eso se publicaba tal cual: seis pasos que
 * terminaban en «evaluación de expediente y entrevista personal», el requisito
 * de «título universitario y/o grado de bachiller», tarifas de S/ 200 y S/ 280
 * según el bachiller fuera o no de San Marcos, un asunto de correo que pedía
 * escribir «NOMBRE DEL DIPLOMADO AL CUAL POSTULA», y seis convocatorias de
 * diplomados de Posgrado presentadas como convocatorias de talleres del CERSEU.
 * Uno de los pasos citaba por su nombre a la «Dirección General de Estudios de
 * Posgrado».
 *
 * Nada de eso corresponde al CERSEU, que no toma examen ni entrevista y cuya
 * oferta está abierta a toda la comunidad sin exigir un grado previo. Se retira
 * sin esperar a que la Unidad decida qué va en su lugar: la página muestra su
 * estado vacío, que es honesto, en vez de un proceso de otra unidad, que no lo
 * es. La Unidad lo escribe desde /admin/admision/{tipo} cuando lo tenga.
 *
 * Queda la fila porque el panel necesita dónde escribir desde el primer
 * arranque, en vez de crearla al primer guardado. El titular tampoco se siembra:
 * sin él la API cae a «Admisión · Talleres», generado desde el enum.
 */
class AdmisionSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (TipoOferta::cases() as $tipo) {
            AdmisionSetting::firstOrCreate(['tipo' => $tipo->value]);
        }
    }
}
