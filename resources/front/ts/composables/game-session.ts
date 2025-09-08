import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import type { PageProps } from '/@front:types/shared'

export function useGameSession() {
  const props = computed(() => usePage<PageProps>().props)
  const session = computed(() => props.value?.session)
  const auth = computed(() => props.value?.app.auth)
  const mainRoute = computed(() => auth.value?.role.mainRoute)
  const currentRound = computed(() => session.value?.currentRound)

  return {
    auth,
    session,
    mainRoute,
    currentRound,
  }
}
