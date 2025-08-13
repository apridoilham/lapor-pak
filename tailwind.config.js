/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        fontFamily: {
            sans: [
                'Poppins', // Menjadikan Poppins sebagai font utama
                'ui-sans-serif',
                'system-ui',
            ],
        },
        colors: {
            'primary': '#16752B',
            'primary-hover': '#045d17',
        }
    },
  },
  plugins: [],
}