/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './node_modules/tw-elements/dist/js/**/*.js'
  ],
  theme: {
    extend: {
      lineHeight: {
        'extra-loose': '2.5',
        '12': '3.125rem',
      },
      colors: {
        'theme': {
          '50': '#f0f9ff',
          '100': '#e0f1fe',
          '200': '#bae4fd',
          '300': '#7ccffd',
          '400': '#37b7f9',
          '500': '#0090F5',
          '600': '#017ac2',
          '700': '#0264a2',
          '800': '#065586',
          '900': '#0c476e',
          '950': '#082d49',
        },
        'text-color': '--text-color',
        'white': '#FFFFFF',
        'light': '#6A7981'
      },
    },
  },
  plugins: [
    require('tw-elements/dist/plugin')
  ],
  darkMode: 'media',
}