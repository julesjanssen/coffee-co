<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
  </header>

  <article>
    <p v-if="appliedFix">{{ $t('Project :title was fixed.', { title: project.title }) }}</p>
    <p v-else>{{ $t('Project :title was successfully serviced.', { title: project.title }) }}</p>

    <div class="client-question">
      <blockquote>
        <cite>{{ $t('Client :name asks', { name: project.client.title }) }}</cite>
        <p>{{ $t('Would you like to stay for a cup of coffee?') }}</p>
      </blockquote>

      <ul class="actions">
        <li>
          <Link :href="links.back">{{ $t('no, thanks') }}</Link>
        </li>
        <li>
          <Link href="/">{{ $t('yes, please') }}</Link>
        </li>
      </ul>
    </div>
  </article>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  project: any
  appliedFix: boolean
  links: Record<string, string>
}>()
</script>
