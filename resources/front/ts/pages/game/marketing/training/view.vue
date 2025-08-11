<template>
  <main class="marketing training">
    <div v-if="shouldShowHints">
      <article>
        <p v-for="hint in hints" :key="hint">{{ hint }}</p>
      </article>
    </div>

    <div v-else>
      <Maze :level="mazeLevel" @ready="mazeCompleted" />
    </div>
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'

import Maze from '/@front:components/games/Maze.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  mazeLevel: number
}>()

const shouldShowHints = ref(false)
const hints = ref<string[]>([])

const mazeCompleted = () => {
  http.post(location.pathname).then((response) => {
    shouldShowHints.value = true
    hints.value = response.data.hints
  })
}
</script>
