import { resolve } from 'node:path'

import vue from '@vitejs/plugin-vue'
import browserslist from 'browserslist'
import laravel from 'laravel-vite-plugin'
import { browserslistToTargets } from 'lightningcss'
import { defineConfig } from 'vite'
import { iconsSpritesheet } from 'vite-plugin-icons-spritesheet'

const customElementTags = ['relative-time']

export default defineConfig(({ command }) => {
  const base = command === 'serve' ? undefined : '/assets/front/'

  return {
    plugins: [
      laravel({
        hotFile: 'public/hot-front',
        buildDirectory: base,
        input: [resolve(__dirname, 'resources/front/ts/app.ts')],
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
        inputDir: resolve(__dirname, 'resources/front/icons/'),
        outputDir: resolve(__dirname, 'public/assets/front'),
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
      outDir: resolve(__dirname, 'public/assets/front/'),
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
          replacement: resolve(__dirname, 'public/assets/front/sprite.svg'),
        },
        {
          find: /^\/@front:css/,
          replacement: resolve(__dirname, 'resources/front/css/'),
        },
        {
          find: /^\/@front:img/,
          replacement: resolve(__dirname, 'resources/front/img/'),
        },
        {
          find: /^\/@front:icons/,
          replacement: resolve(__dirname, 'resources/front/icons/'),
        },
        {
          find: /^\/@front:components/,
          replacement: resolve(__dirname, 'resources/front/ts/components/'),
        },
        {
          find: /^\/@front:composables/,
          replacement: resolve(__dirname, 'resources/front/ts/composables/'),
        },
        {
          find: /^\/@front:layouts/,
          replacement: resolve(__dirname, 'resources/front/ts/layouts/'),
        },
        {
          find: /^\/@front:shared/,
          replacement: resolve(__dirname, 'resources/front/ts/shared/'),
        },
        {
          find: /^\/@front/,
          replacement: resolve(__dirname, 'resources/front/'),
        },
      ],
    },
  }
})
