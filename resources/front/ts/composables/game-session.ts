import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import type { PageProps } from '/@front:types/shared'

export function useGameSession() {
  const props = computed(() => usePage<PageProps>().props)
  const session = computed(() => props.value?.gameSession)
  const participant = computed(() => props.value?.participant)

  return {
    participant,
    currentRound: computed(() => session.value?.currentRound),
  }
}
