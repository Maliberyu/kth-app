/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                sage: {
                    50:  '#f4f7f5',
                    100: '#e6ede9',
                    200: '#d0ded6',
                    300: '#a8c4b7',
                    400: '#7ca798',
                    500: '#4f8b7a',
                    600: '#3a6d5f',   // Warna utama navbar & button
                    700: '#2c554a',
                    800: '#1f3c34',
                    900: '#13241f',
                },
            },
        },
    },
    plugins: [],
}