<template>
  <div>
    <ul class="hints">
      <li v-for="hint in hints" :key="hint">
        {{ hint }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  hints: string[]
}>()
</script>
