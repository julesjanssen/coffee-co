<template>
  <header>
    <h2>{{ $t('projects') }}</h2>
  </header>

  <div>
    <ul class="projects">
      <li v-for="project in projects" :key="project.sqid">
        <p>{{ project.title }} ({{ project.client.title }})</p>
        <strong>{{ project.status.label }}</strong>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import GameLayout from '/@front:layouts/game.vue'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: Project[]
}>()
</script>
