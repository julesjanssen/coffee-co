import { router } from '@inertiajs/vue3'
import axios, { isAxiosError, isCancel } from 'axios'
import { buildWebStorage, setupCache } from 'axios-cache-interceptor'

import { $t } from '/@front:shared/i18n'
import { error as errorNotify } from '/@front:shared/notifications'

const instance = axios.create()

instance.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (isCancel(error)) {
      return null
    }

    if (isAxiosError(error) && error.response?.status === 419) {
      errorNotify($t('Page expired. Reloading…'), {
        description: $t('Please wait while we reload the page...'),
      })

      router.reload()
    }

    return Promise.reject(error)
  },
)

const cachedInstance = setupCache(instance, {
  storage: buildWebStorage(window.localStorage, 'axios-cache:'),
})

// todo: remove ! after axios-cache-adapter fix
const clearCache = async () => await cachedInstance.storage.clear!()

export { cachedInstance as cachedHttp, clearCache, instance as http }
