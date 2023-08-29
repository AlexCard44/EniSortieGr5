/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    fontFamily: {
      'hepta': ['Hepta Slab', 'serif'],
  },
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

