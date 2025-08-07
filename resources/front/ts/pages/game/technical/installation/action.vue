<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
  </header>

  <article>
    <p v-if="appliedFix">{{ $t('Project :title was fixed.', { title: project.title }) }}</p>
    <p v-else>{{ $t('Project :title was successfully serviced.', { title: project.title }) }}</p>
  </article>

  <div v-if="shouldShowHint" class="hint">
    <p>{{ hint }}</p>

    <div v-if="isExtraServiceCompleted" class="actions">
      <Link :href="links.back">{{ $t('done') }}</Link>
    </div>
  </div>

  <div v-else-if="isPerformingExtraService">
    <p>{{ $t('Please complete maze :mazeID.', { mazeID: String(mazeID) }) }}</p>

    <div v-if="isExtraServiceCompleted" class="actions">
      <button type="button" @click.prevent="registerExtraService">
        {{ $t('done') }}
      </button>
    </div>
  </div>

  <div v-else class="client-question">
    <blockquote>
      <cite>{{ $t('Client :name asks', { name: project.client.title }) }}</cite>
      <p>{{ $t('Would you like to stay for a cup of coffee?') }}</p>
    </blockquote>

    <ul class="actions">
      <li>
        <Link :href="links.back">{{ $t('no, thanks') }}</Link>
      </li>
      <li>
        <button type="button" @click.prevent="initExtraService">{{ $t('yes, please') }}</button>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  project: Project
  projectAction: any
  mazeID: number
  links: Record<string, string>
}>()

const isPerformingExtraService = ref(false)
const isExtraServiceCompleted = ref(false)
const hint = ref('')

const appliedFix = computed(() => props.projectAction.type.value === 'fix')
const shouldShowHint = computed(() => isExtraServiceCompleted.value === true && hint.value.length > 0)

const initExtraService = () => {
  isPerformingExtraService.value = true

  setTimeout(() => {
    isExtraServiceCompleted.value = true
  }, 5_000)
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
