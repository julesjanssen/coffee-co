import '/@front:css/style.css'

import { createInertiaApp } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'
import type { DefineComponent } from 'vue'
import { createApp, h } from 'vue'

import { useServerExceptions } from './composables/server-exceptions'
import { registerPlugins } from './shared/plugins'
const appName = import.meta.env.VITE_APP_NAME

Chart.defaults.font = {
  family: "'Noto Sans', system-ui",
  size: 14,
}

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })

    return pages[`./pages/${name}.vue`] as DefineComponent
  },
  setup: ({ el, App, props, plugin }) => {
    const app = createApp({
      name: 'AppMain',
      render: () => h(App, props),
    })

    registerPlugins(app)

    app.use(plugin).mount(el)

    useServerExceptions()
  },
})
