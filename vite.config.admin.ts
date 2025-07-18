import { resolve } from 'node:path'

import vue from '@vitejs/plugin-vue'
import browserslist from 'browserslist'
import laravel from 'laravel-vite-plugin'
import { browserslistToTargets } from 'lightningcss'
import { defineConfig } from 'vite'
import { iconsSpritesheet } from 'vite-plugin-icons-spritesheet'

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
      iconsSpritesheet({
        inputDir: resolve(__dirname, 'resources/admin/icons/'),
        outputDir: resolve(__dirname, 'public/assets/admin'),
        fileName: 'sprite.svg',
        iconNameTransformer: (iconName) => iconName,
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
      rollupOptions: {
        output: {
          advancedChunks: {
            groups: [
              {
                name: 'lodash',
                test: /lodash/,
              },
              {
                name: 'floating',
                test: /floating/,
              },
              {
                name: 'reka',
                test: /reka/,
              },
              {
                name: 'inertia',
                test: /nprogress|inertia/,
              },
              {
                name: 'tus',
                test: /tus|querystringify|base64|url-parse/,
              },
              {
                name: 'vue',
                test: /vue/,
              },
            ],
          },
        },
      },
    },
    resolve: {
      alias: [
        {
          find: '/@sprite.svg',
          replacement: resolve(__dirname, 'public/assets/admin/sprite.svg'),
        },
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
          find: /^\/@admin:composables/,
          replacement: resolve(__dirname, 'resources/admin/ts/composables/'),
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
