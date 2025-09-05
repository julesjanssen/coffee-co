<template>
  <header>
    <h2>{{ $t('projects') }}</h2>
  </header>

  <div>
    <ul class="projects-list">
      <li v-for="project in projects" :key="project.sqid" class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
        <span class="status">
          <strong :class="project.status.value">{{ project.status.label }}</strong>
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
// import { ink } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: Project[]
}>()
</script>
