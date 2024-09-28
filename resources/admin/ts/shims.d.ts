import type { ReplacementsInterface } from 'laravel-vue-i18n/src/interfaces/replacements'

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<unknown, unknown, any>
  export default component
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $t: (key: string, replacements: ReplacementsInterface = {}) => string
    $tChoice: (key: string, number: number, replacements: ReplacementsInterface = {}) => string
  }
}

declare module '*.json' {
  const value: any
  export default value
}

declare module 'laravel-vite-plugin'
