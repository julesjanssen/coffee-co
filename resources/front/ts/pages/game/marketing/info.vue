<template>
  <div class="center">
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

<style scoped>
.hints {
  display: flex;
  max-width: 60ch;
  flex-direction: column;
  margin: 1.5em auto;
  font-size: 1rem;
  gap: 1em;

  & li {
    text-align: center;
  }
}
</style>
