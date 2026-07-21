import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#EEF2FF',
                    100: '#E0E7FF',
                    500: '#6D5AFF',
                    600: '#4F39F6',
                    700: '#4028D6',
                },
            },
            borderRadius: {
                xl2: '16px',
            },
        },
    },
    plugins: [forms],
};
