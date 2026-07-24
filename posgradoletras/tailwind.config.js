import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // El sitio es light-only. Con 'class' (en vez del 'media' por defecto) las
    // variantes dark: del scaffolding de Breeze quedan inertes salvo que exista
    // un ancestro .dark (que nunca se agrega), evitando render oscuro accidental
    // en auth/perfil (importante para el recorte blanco de los floating labels).
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Merriweather', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                'unmsm-guinda': '#6B1E20',
                'unmsm-guinda-light': '#8B1114',
                'unmsm-dorado': '#B6A350',
                'unmsm-dorado-light': '#C9AA36',
            },
        },
    },

    plugins: [forms],
};
