import { router } from '@inertiajs/vue3'
import axios, { isAxiosError, isCancel } from 'axios'
import { buildWebStorage, setupCache } from 'axios-cache-interceptor'
import { toast } from 'vue-sonner'

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
      toast.error('Page expired. Reloading…')
      router.reload()
    }

    return Promise.reject(error)
  },
)

const cachedInstance = setupCache(instance, {
  storage: buildWebStorage(window.localStorage, 'axios-cache:'),
})

export { cachedInstance as cachedHttp, instance as http }
