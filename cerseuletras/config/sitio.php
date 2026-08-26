<?php

/**
 * Enlace con el sitio público en Astro.
 *
 * Hoy apunta al servicio de build del propio Docker Compose. Cuando el build
 * pase a CI, cambia esta URL —y el token— sin tocar una línea de código: el
 * contrato es el mismo, una petición autenticada que encarga la reconstrucción.
 */
return [
    'reconstruccion' => [
        // Vacío desactiva el aviso: útil en las pruebas y en una instalación
        // que todavía sirva el sitio con Blade.
        'url' => env('CERSEU_BUILD_URL', 'http://build:4322/reconstruir'),
        'token' => env('CERSEU_BUILD_TOKEN', ''),
        // Margen para agrupar ráfagas: quien edita guarda varias veces seguidas.
        'espera_segundos' => (int) env('CERSEU_BUILD_ESPERA', 60),
    ],
];
