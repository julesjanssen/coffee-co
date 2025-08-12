<template>
  <main class="marketing hdma">
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
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

import Maze from '/@front:components/games/Maze.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  mazeLevel: number
  links: Record<string, string>
}>()

const shouldShowHints = ref(false)
const hints = ref<string[]>([])

const mazeCompleted = () => {
  http
    .post(location.pathname)
    .then((response) => {
      shouldShowHints.value = true
      hints.value = response.data.hints
    })
    .catch((err) => {
      const status = err.response?.status
      if (status === 422) {
        error(err.response.data?.message || 'Unknown error')
        router.visit(props.links.back)
        return
      }

      error('Failed to perform action.')
    })
}
</script>
