<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * No siembra nada, a propósito.
 *
 * Hasta agosto de 2026 creaba diez documentos heredados de la Unidad de
 * Posgrado —«Reglamento de Estudios de Posgrado», «Formato de Proyecto de
 * Tesis», «Manual de Estilo para Tesis»…—, todos con `published => true` y
 * apuntando a `/documents/*.pdf`. Ninguno de esos ficheros existe ni ha
 * existido: no hay carpeta `public/documents/`. El resultado era que
 * `/admision` publicaba cuatro enlaces de descarga que devolvían 404, con
 * títulos que además hablaban de tesis y grados académicos, que no es lo que
 * hace el CERSEU.
 *
 * Se quitan en lugar de sustituirse porque no hay documentos reales que poner:
 * inventarlos repetiría el problema con otro texto. Los sube la Unidad desde
 * `/admin/documentos` cuando los tenga, y mientras tanto las secciones que los
 * listan muestran su estado vacío, que ya está resuelto en las plantillas.
 *
 * De paso, el seeder anterior llamaba a `Document::create()` en bucle sin
 * comprobar nada, así que cada `db:seed` duplicaba los diez.
 */
class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        //
    }
}
