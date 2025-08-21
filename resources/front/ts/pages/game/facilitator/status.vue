<template>
  <Head :title="session?.title" />

  <main class="facilitator status">
    <Status :session="session" :links="links" />
  </main>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import type { GameSession } from '/@front:types/shared'

import Status from './status/Status.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  session: GameSession
  links: Record<string, string>
}>()
</script>
