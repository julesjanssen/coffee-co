import { resolve } from 'node:path'

import vue from '@vitejs/plugin-vue'
import browserslist from 'browserslist'
import laravel from 'laravel-vite-plugin'
import { browserslistToTargets } from 'lightningcss'
import { defineConfig } from 'vite'

const customElementTags = ['relative-time']

export default defineConfig(({ command }) => {
  const base = command === 'serve' ? undefined : '/assets/admin/'

  return {
    plugins: [
      laravel({
        hotFile: 'public/hot-admin',
        buildDirectory: base,
        input: [resolve(__dirname, 'resources/admin/ts/app.ts')],
      }),
      vue({
        template: {
          compilerOptions: {
            isCustomElement: (tag) => customElementTags.includes(tag),
          },
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
    ],
    css: {
      transformer: 'lightningcss',
      lightningcss: {
        targets: browserslistToTargets(browserslist('defaults')),
      },
    },
    build: {
      outDir: resolve(__dirname, 'public/assets/admin/'),
      assetsDir: 'v',
      manifest: 'manifest.json',
      sourcemap: true,
    },
    resolve: {
      alias: [
        {
          find: /^\/@admin:css/,
          replacement: resolve(__dirname, 'resources/admin/css/'),
        },
        {
          find: /^\/@admin:img/,
          replacement: resolve(__dirname, 'resources/admin/img/'),
        },
        {
          find: /^\/@admin:icons/,
          replacement: resolve(__dirname, 'resources/admin/icons/'),
        },
        {
          find: /^\/@admin:components/,
          replacement: resolve(__dirname, 'resources/admin/ts/components/'),
        },
        {
          find: /^\/@admin:layouts/,
          replacement: resolve(__dirname, 'resources/admin/ts/layouts/'),
        },
        {
          find: /^\/@admin:shared/,
          replacement: resolve(__dirname, 'resources/admin/ts/shared/'),
        },
        {
          find: /^\/@admin:types/,
          replacement: resolve(__dirname, 'resources/admin/ts/types.d.ts'),
        },
        {
          find: /^\/@admin/,
          replacement: resolve(__dirname, 'resources/admin/'),
        },
      ],
    },
  }
})
