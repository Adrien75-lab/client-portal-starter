import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
  plugins: [
    vue(),
    vuetify({ autoImport: true })
  ],
  server: {
    host: true,
    port: 5173,
    proxy: {
      '/auth': { target: 'http://backend:8080', changeOrigin: true },
      '/api':  { target: 'http://backend:8080', changeOrigin: true },
    }
  }
})
