import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite'
import { fileURLToPath } from 'node:url'

// https://vite.dev/config/
export default defineConfig({
  base: '/wpFatima/',
  plugins: [svelte(), tailwindcss()],
  resolve: {
    alias: {
      '$lib': fileURLToPath(new URL('./src/lib', import.meta.url)),
      '$lib/': fileURLToPath(new URL('./src/lib/', import.meta.url)),
    },
  },
  server: {
    proxy: {
      '/api': 'http://localhost/wpfatima/public',
      '/webhook': 'http://localhost/wpfatima/public',
    },
  },
})
