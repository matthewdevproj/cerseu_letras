<?php

namespace Database\Seeders;

use App\Models\AdmisionSetting;
use App\Models\TipoOferta;
use Illuminate\Database\Seeder;

class AdmisionSettingSeeder extends Seeder
{
    /**
     * Contenido oficial tomado de "Ajustes para la página web de Diplomados.pdf"
     * y "Mejoras_admisión.pdf" (este último prevalece para la página de Admisión).
     *
     * Siembra el módulo de talleres con ese contenido y el de cursos con la
     * misma estructura vacía, para que el panel tenga dónde escribir desde el
     * primer arranque en vez de crear la fila al primer guardado.
     */
    public function run(): void
    {
        $settings = AdmisionSetting::create([
            'tipo' => TipoOferta::Taller->value,
            'hero_titulo' => 'Convocatoria 2026-I',
            'hero_subtitulo' => 'Sección Talleres · CERSEU',
            'pasos' => [
                [
                    'numero' => 1,
                    'titulo' => 'Cronograma académico',
                    'descripcion' => 'Visualizar el cronograma académico con la finalidad de conocer la programación del proceso de admisión.',
                    'icono' => 'fa-calendar-days',
                ],
                [
                    'numero' => 2,
                    'titulo' => 'Requisitos para postular',
                    'descripcion' => 'Debe cumplir los requisitos establecidos por la Dirección General de Estudios de Posgrado para postular al taller correspondiente.',
                    'icono' => 'fa-list-check',
                ],
                [
                    'numero' => 3,
                    'titulo' => 'Pago por derecho de inscripción',
                    'descripcion' => 'Los pagos se realizan mediante San Market UNMSM utilizando Banco BCP o Yape.',
                    'icono' => 'fa-money-check-dollar',
                ],
                [
                    'numero' => 4,
                    'titulo' => 'Envío de expediente',
                    'descripcion' => 'El postulante deberá enviar su expediente en archivo digital (formato PDF).',
                    'icono' => 'fa-file-arrow-up',
                ],
                [
                    'numero' => 5,
                    'titulo' => 'Evaluación de postulantes',
                    'descripcion' => 'Evaluación de expediente y entrevista personal.',
                    'icono' => 'fa-user-check',
                ],
                [
                    'numero' => 6,
                    'titulo' => 'Resultados',
                    'descripcion' => 'La relación de postulantes admitidos será publicada en el portal web del CERSEU.',
                    'icono' => 'fa-clipboard-check',
                ],
            ],

            'requisitos_email' => 'cerseu.letras@unmsm.edu.pe',
            'requisitos_lista' => [
                'Ficha de datos del postulante.',
                'Título universitario y/o grado de bachiller (*).',
                'Copia del documento de identidad (DNI, carné de extranjería o pasaporte).',
                'CV documentado y foliado en un solo archivo.',
                'Recibo de pago por derecho de inscripción.',
            ],
            'requisitos_observaciones' => '(*) Los postulantes que obtuvieron el grado de bachiller en la Universidad Nacional Mayor de San Marcos solo presentarán copia simple.',
            'requisitos_notas' => 'Envíe el correo indicando en el asunto: APELLIDOS NOMBRES_NOMBRE DEL DIPLOMADO AL CUAL POSTULA. Fecha límite de entrega: 11:59 p.m. hasta el último día de inscripción, según corresponda.',

            'pago_costo' => 'S/ 200 (Bachiller UNMSM) · S/ 280 (otras universidades)',
            'pago_descripcion' => 'Recuerde que, antes de realizar el pago por derecho de inscripción, debe verificar que el programa de su interés participe en el proceso de admisión actual y que esté dentro del cronograma establecido en el presente proceso.',
            'pago_instrucciones' => [
                [
                    'titulo' => 'Generar ticket en San Market-UNMSM',
                    'descripcion' => 'Regístrate con tu correo de dominio Gmail para generar el ticket de pago.',
                    'video_url' => 'https://www.youtube.com/embed/wDpbuHt1xg4',
                ],
                [
                    'titulo' => 'Realizar el pago',
                    'descripcion' => 'Puedes pagar a través de la App BCP, en un agente BCP o mediante Yape.',
                    'video_url' => 'https://www.youtube.com/embed/feg7DN0pSLM',
                ],
            ],
            'pago_link_sanmarket' => 'https://sanmarket.unmsm.edu.pe/#/',
            'pago_observaciones' => 'De no realizar el pago en los plazos establecidos, perderá automáticamente su vacante.',

            'resultados_texto' => 'La relación de postulantes admitidos será publicada en el portal web del CERSEU.',
            'resultados_enlace' => null,
            'resultados_pdf_url' => null,

            'contacto_telefono' => '914 033 129',
            'contacto_correo' => 'cerseu.letras@unmsm.edu.pe',
            'contacto_direccion' => 'Ciudad Universitaria, Av. Venezuela s/n, Lima',
            'contacto_sitio_web' => 'https://cerseuletras.unmsm.edu.pe',
            'contacto_qr_path' => null,
            'contacto_whatsapp' => 'https://wa.me/51914033129',
        ]);

        $settings->cronogramaItems()->createMany([
            [
                'programa' => 'Diplomado en Gestión Cultural y Desarrollo de Públicos',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 25 de septiembre',
                'estado' => 'Activo',
                'orden' => 1,
            ],
            [
                'programa' => 'Diplomado en Curaduría con Énfasis en Arte Peruano y Latinoamericano Moderno y Contemporáneo',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 25 de septiembre',
                'estado' => 'Activo',
                'orden' => 2,
            ],
            [
                'programa' => 'Diplomado Internacional de Lingüística Forense',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 25 de septiembre',
                'estado' => 'Activo',
                'orden' => 3,
            ],
            [
                'programa' => 'Diplomado en Proyectos de Innovación Social con Inteligencia Artificial en Educación y Comunicaciones',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 25 de septiembre',
                'estado' => 'Activo',
                'orden' => 4,
            ],
            [
                'programa' => 'Diplomado Internacional en Corrección Lingüística',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 28 de septiembre',
                'estado' => 'Activo',
                'orden' => 5,
            ],
            [
                'programa' => 'Diplomado en Filosofía de la Educación, Ética y Epistemología de las Ciencias Sociales',
                'convocatoria' => '2026',
                'fecha_inscripcion' => 'Desde el 01 de agosto',
                'fecha_limite' => 'Hasta el 28 de septiembre',
                'estado' => 'Activo',
                'orden' => 6,
            ],
        ]);

        AdmisionSetting::firstOrCreate(
            ['tipo' => TipoOferta::Curso->value],
            [
                'hero_titulo' => 'Convocatoria 2026-I',
                'hero_subtitulo' => 'Sección Cursos · CERSEU',
            ]
        );
    }
}
