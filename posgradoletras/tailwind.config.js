import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
