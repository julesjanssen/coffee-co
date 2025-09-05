<template>
  <div class="select-project">
    <ul class="projects-list">
      <li v-for="project in projects" :key="project.href">
        <component
          :is="canMaintenanceBeAppliedToProject(project) ? 'button' : 'div'"
          type="button"
          class="project"
          @click.prevent="maintainProject(project)"
        >
          <span class="title">{{ project.title }}</span>
          <span class="client">{{ project.client.title }}</span>
          <span class="status">
            <strong :class="project.status.value">{{ project.status.label }}</strong>
          </span>
          <span class="failure-chance">
            {{ $t('chance of failure') }}
            {{ project.failureChance }}%
          </span>
        </component>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import { error } from '/@front:shared/notifications'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: (Project & { href: string })[]
}>()

const canMaintenanceBeAppliedToProject = (project: Project) => {
  if (project.status.value === 'down') {
    return true
  }

  return project.failureChance > 0
}

const maintainProject = (project: any) => {
  if (!canMaintenanceBeAppliedToProject(project)) {
    return
  }

  router.post(
    project.href,
    {},
    {
      onError: () => {
        error(`Failed to apply maintenance to "${project.title}".`)
        router.reload()
      },
    },
  )
}
</script>
