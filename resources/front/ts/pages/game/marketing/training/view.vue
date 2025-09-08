<template>
  <main class="marketing training">
    <div v-if="shouldShowHints" class="hints">
      <article>
        <p v-for="hint in hints" :key="hint">{{ hint }}</p>
      </article>

      <ul class="actions center">
        <li>
          <Link :href="mainRoute">{{ $t('done') }}</Link>
        </li>
      </ul>
    </div>

    <div v-else>
      <Maze :level="mazeLevel" @ready="mazeCompleted" />
    </div>
  </main>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

import Maze from '/@front:components/games/Maze.vue'
import { useGameSession } from '/@front:composables/game-session'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  mazeLevel: number
}>()

const { mainRoute } = useGameSession()

const shouldShowHints = ref(false)
const hints = ref<string[]>([])

const mazeCompleted = () => {
  http.post(location.pathname).then((response) => {
    shouldShowHints.value = true
    hints.value = response.data.hints
  })
}
</script>
