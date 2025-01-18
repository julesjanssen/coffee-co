import { router } from '@inertiajs/vue3'
import axios, { isAxiosError, isCancel } from 'axios'
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

export { instance as http }
