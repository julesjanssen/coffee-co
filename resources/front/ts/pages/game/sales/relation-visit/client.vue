<template>
  <header class="client">
    <h2>{{ client.title }}</h2>
  </header>

  <p>{{ $t('Please complete maze :mazeID.', { mazeID: String(mazeID) }) }}</p>

  <p>{{ tip }}</p>

  <div v-if="canContinue" class="actions">
    <Link :href="links.back">next</Link>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useTimeoutFn } from '@vueuse/core'
import { ref } from 'vue'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import { http } from '/@front:shared/http'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  client: ScenarioClient
  mazeID: number
  links: Record<string, string>
}>()

const canContinue = ref(false)
const tip = ref('')

useTimeoutFn(() => {
  http.post(location.pathname).then((response) => {
    canContinue.value = true
    tip.value = response.data.tip
  })
}, 3_000)
</script>
