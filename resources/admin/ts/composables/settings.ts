import { computed, onMounted, reactive } from 'vue'

import { cachedHttp } from '/@admin:shared/http'

const settings = reactive<Record<string, any>>({})

const getSetting = (name: string) => {
  return computed(() => settings[name])
}

export function useSetting(name: string) {
  useSettings()

  return getSetting(name)
}

export function useSettings() {
  onMounted(() => {
    cachedHttp.get('/admin/config/settings').then((response) => {
      const data = response.data
      Object.keys(data).forEach((key) => {
        settings[key] = data[key]
      })
    })
  })

  return {
    settings,
    getSetting,
  }
}
