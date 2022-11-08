import { defineConfig, loadEnv } from "vite"
import symfonyPlugin from "vite-plugin-symfony"
import react from '@vitejs/plugin-react'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), 'VITE_')
    return {
        plugins: [
            react(),
            symfonyPlugin(),
        ],
        build: {
            rollupOptions: {
                input: {
                    app: "./assets/bootstrap.tsx"
                },
            },
        },
        server: {
            host: env.VITE_LISTEN_HOST,
            port: env.VITE_LISTEN_PORT,
            origin: env.VITE_ORIGIN,
        },
    }
})
