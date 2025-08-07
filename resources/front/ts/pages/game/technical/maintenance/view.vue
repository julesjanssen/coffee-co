<template>
  <div>
    <div class="select-project">
      <ul>
        <li v-for="project in projects" :key="project.href">
          <button
            v-if="canMaintenanceBeAppliedToProject(project)"
            type="button"
            @click.prevent="maintainProject(project)"
          >
            {{ project.title }} ({{ project.client.title }}) / {{ project.failureChance }}
          </button>
          <span v-else>{{ project.title }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game-client-actions.vue'
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
