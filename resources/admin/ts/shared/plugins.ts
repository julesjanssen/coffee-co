import FloatingVue from 'floating-vue'
import type { App } from 'vue'

import { i18nVue, options } from './i18n'

export const registerPlugins = (app: App) => {
  app.use(i18nVue, options)

  app.use(FloatingVue, {
    themes: {
      dropdown: {
        placement: 'bottom-end',
        distance: 10,
      },
      ['tenant-switch']: {
        $extend: 'dropdown',
        distance: 8,
      },
    },
  })
}
