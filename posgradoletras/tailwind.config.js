import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'unmsm-guinda': '#680D10',
                'unmsm-guinda-light': '#8B1114',
                'unmsm-dorado': '#B6A350',
                'unmsm-dorado-light': '#C9AA36',
            },
        },
    },

    plugins: [forms],
};
