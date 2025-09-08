<template>
  <div v-if="projects.length === 0" class="empty">
    <article>
      <p>{{ $t('No pending projects at this moment.') }}</p>
    </article>
  </div>

  <ul class="projects-list">
    <li v-for="project in projects" :key="project.href">
      <Link :href="project.href" class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
        <span class="deadline">{{
          $t('submit no later than :date', { date: project.shouldBeQuotedBy.displayFull })
        }}</span>

        <span class="price">
          <MoneyDisplay :value="project.price" />
        </span>
      </Link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: any[]
}>()
</script>
