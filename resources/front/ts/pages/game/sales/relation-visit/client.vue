<template>
  <div class="center">
    <header class="client">
      <h2>{{ client.title }}</h2>
    </header>

    <div v-if="!canContinue">
      <Maze :level="mazeLevel" @ready="mazeCompleted" />
    </div>

    <div v-else>
      <p>{{ hint }}</p>

      <div class="actions">
        <Link :href="links.back" class="button">next</Link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

import Maze from '/@front:components/games/Maze.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  client: ScenarioClient
  mazeLevel: number
  links: Record<string, string>
}>()

const canContinue = ref(false)
const hint = ref('')

const mazeCompleted = () => {
  http.post(location.pathname).then((response) => {
    canContinue.value = true
    hint.value = response.data.tip
  })
}
</script>
