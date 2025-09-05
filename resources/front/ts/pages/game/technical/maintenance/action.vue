<template>
  <div class="technical maintenance project">
    <header class="project-display">
      <div class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
      </div>
    </header>

    <article>
      <p v-if="appliedFix">{{ $t('Project :title was fixed.', { title: project.title }) }}</p>
      <p v-else>{{ $t('":title" was successfully serviced.', { title: project.title }) }}</p>
    </article>

    <div v-if="shouldShowHint" class="hint">
      <p>{{ hint }}</p>

      <ul v-if="isExtraServiceCompleted" class="actions">
        <li>
          <Link :href="links.back">{{ $t('done') }}</Link>
        </li>
      </ul>
    </div>

    <div v-else-if="isPerformingExtraService" class="extra-service">
      <div v-if="!isExtraServiceCompleted">
        <Maze :level="mazeLevel" @ready="mazeCompleted" />
      </div>
    </div>

    <div v-else class="client-question">
      <blockquote>
        <cite>{{ $t(':name asks', { name: project.client.title }) }}:</cite>
        <p>{{ $t('Would you like to stay for a cup of coffee?') }}</p>
      </blockquote>

      <ul class="actions">
        <li>
          <Link :href="links.back" class="action">{{ $t('no, thanks') }}</Link>
        </li>
        <li>
          <button type="button" class="primary" @click.prevent="initExtraService">{{ $t('yes, please') }}</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import Maze from '/@front:components/games/Maze.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  project: Project
  projectAction: any
  mazeLevel: number
  links: Record<string, string>
}>()

const isPerformingExtraService = ref(false)
const isExtraServiceCompleted = ref(false)
const hint = ref('')

const appliedFix = computed(() => props.projectAction.type.value === 'fix')
const shouldShowHint = computed(() => isExtraServiceCompleted.value === true && hint.value.length > 0)

const initExtraService = () => {
  isPerformingExtraService.value = true
}

const mazeCompleted = () => {
  isExtraServiceCompleted.value = true
  registerExtraService()
}

const registerExtraService = () => {
  http
    .post(props.links.extraService, {})
    .then((response) => {
      const data = response.data
      hint.value = data.details.hint
    })
    .catch(() => {
      error('Failed to perform action.')
    })
}
</script>

<style src="/@front:css/views/technical.css"></style>
