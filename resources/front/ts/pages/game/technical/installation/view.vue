<template>
  <div v-if="projects.length">
    <ul class="projects-list">
      <li v-for="project in projects" :key="project.href">
        <button type="button" class="project" @click.prevent="installProject(project)">
          <span class="title">{{ project.title }}</span>
          <span class="client">{{ project.client.title }}</span>

          <span class="status">
            <strong :class="project.status.value">{{ project.status.label }}</strong>
          </span>
        </button>
      </li>
    </ul>
  </div>

  <div v-else class="empty">
    <article>
      <p>{{ $t('Currently, there are no projects to install.') }}</p>
    </article>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import { $t } from '/@front:shared/i18n'
import { error, success } from '/@front:shared/notifications'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: (Project & { href: string })[]
}>()

const installProject = (project: any) => {
  router.post(
    project.href,
    {},
    {
      onSuccess: () => {
        success(
          $t('Successfully installed ":project" at ":client".', {
            project: project.title,
            client: project.client.title,
          }),
        )
      },
      onError: () => {
        error(`Failed to install "${project.title}".`)
        router.reload()
      },
    },
  )
}
</script>
