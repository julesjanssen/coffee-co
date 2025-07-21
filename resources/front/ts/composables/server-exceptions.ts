import { router } from '@inertiajs/vue3'

import { $t } from '../shared/i18n'

export function useServerExceptions() {
  // const notify = useNotification()

  const init = () => {
    router.on('invalid', (event) => {
      if (event.detail.response.status === 403) {
        event.preventDefault()
        router.reload()

        alert($t('You are no longer authorized to perform this action.'))
        // toast.error($t('You are no longer authorized to perform this action.'))
      }
    })
  }

  init()
}
