<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
    <h3>{{ project.client.title }}</h3>
  </header>

  <article>
    <p>
      {{ $t('status') }}: <strong>{{ project.status.label }}</strong>
    </p>

    <ul class="reasons">
      <li v-for="reason in wonLostReasons" :key="reason">
        {{ reason }}
      </li>
    </ul>
  </article>
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
