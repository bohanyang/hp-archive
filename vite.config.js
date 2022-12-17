import { defineConfig, loadEnv } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import react from '@vitejs/plugin-react'
import inject from '@rollup/plugin-inject'
import viteCompression from 'vite-plugin-compression'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), 'VITE_')
  return {
    plugins: [
      inject({
        jQuery: 'jquery'
      }),
      react(),
      symfonyPlugin(),
      viteCompression({ algorithm: 'gzip' })
    ],
    resolve: {
      alias: {
        '@/': `${__dirname}/assets/`
      }
    },
    build: {
      rollupOptions: {
        input: {
          app: './assets/bootstrap.tsx',
          admin: './assets/admin/bootstrap.tsx'
        }
      }
    },
    server: {
      host: env.VITE_LISTEN_HOST,
      port: env.VITE_LISTEN_PORT,
      origin: env.VITE_ORIGIN
    },
    optimizeDeps: {
      include: ['jquery']
    }
  }
})
