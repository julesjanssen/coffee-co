<template>
  <div class="backoffice project view">
    <header class="project-display">
      <div class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
        <span class="status">
          <strong :class="project.status.value">{{ project.status.label }}</strong>
        </span>
      </div>
    </header>

    <article>
      <ul class="reasons">
        <li v-for="reason in wonLostReasons" :key="reason">
          {{ reason }}
        </li>
      </ul>
    </article>
  </div>
</template>

<script setup lang="ts">
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  project: any
  links: Record<string, string>
  wonLostReasons: string[]
  solutions?: any[]
}>()
</script>

<style src="/@front:css/views/backoffice.project.css"></style>
