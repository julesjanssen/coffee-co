<template>
  <div>
    <div v-if="projects.length === 0" class="empty">
      <article>
        <p>{{ $t('No pending projects at this moment.') }}</p>
      </article>
    </div>

    <ul class="projects">
      <li v-for="project in projects" :key="project.href">
        <Link :href="project.href">
          <span class="title">{{ project.title }}</span>
          <span class="client">{{ project.client.title }}</span>
          <span class="deadline">{{
            $t('submit no later than :date', { date: project.shouldBeQuotedBy.display })
          }}</span>

          <MoneyDisplay :value="project.price" />
        </Link>
      </li>
    </ul>
  </div>
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
