<template>
  <div>
    <ul class="projects-list">
      <li v-for="project in projects" :key="project.sqid" class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
        <span class="status">
          <strong :class="project.status.value">{{ project.status.label }}</strong>
        </span>

        <span class="deadline">
          <template v-if="project.status.value === 'pending'">
            <p>{{ $t('submit quote before :round', { round: project.quoteBeforeRound!.displayFull }) }}</p>
          </template>

          <template v-if="project.status.value === 'won'">
            <p>
              {{
                $t('won in :round — deliver before :deliverRound', {
                  round: project.quoteRound!.displayFull,
                  deliverRound: project.deliverBeforeRound!.displayFull,
                })
              }}
            </p>
          </template>
        </span>
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
