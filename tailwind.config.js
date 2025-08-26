/** @type {import('tailwindcss').Config} */
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import rtl from 'tailwindcss-rtl'

export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.php',
    './resources/**/*.js',
    './resources/**/*.ts',
    './resources/**/*.vue',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './vendor/livewire/**/*.blade.php',
    './storage/framework/views/*.php',
  ],
  safelist: [
    // Colores dinámicos (BD/JSON) para tu paleta
    { pattern: /(bg|text|border|ring|fill|stroke)-(primary|base)-(50|100|200|300|400|500|600|700|800|900)/ },
    // Utilidades dinámicas comunes
    { pattern: /(col|row)-span-(1|2|3|4|5|6|7|8|9|10|11|12)/ },
    { pattern: /(grid-cols|grid-rows)-(1|2|3|4|5|6|7|8|9|10|11|12)/ },
    { pattern: /(from|via|to)-(primary|base)-(100|200|300|400|500|600|700|800|900)/ },
  ],
  theme: {
    container: {
      center: true,
      padding: '1rem',
      screens: { sm: '640px', md: '768px', lg: '1024px', xl: '1280px', '2xl': '1536px' },
    },
    extend: {
      fontFamily: {
        // Ajusta si usas otra tipografía
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'],
      },
      colors: {
        // Paleta neutra moderna
        base: {
          50:  '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          300: '#d4d4d4',
          400: '#a3a3a3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#171717',
        },
        primary: {
          50:'#f5f7f8',
          100:'#eaeef0',
          200:'#cdd6db',
          300:'#aebdc6',
          400:'#7f97a6',
          500:'#5b788b',
          600:'#465f72',
          700:'#3a4f60',
          800:'#293a46',
          900:'#1b2832',
        },
      },
    },
  },
  plugins: [
    forms,
    typography,
    rtl,
  ],
}