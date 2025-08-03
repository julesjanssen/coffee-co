<template>
  <header class="client">
    <h2>{{ client.title }}</h2>
  </header>

  <article>
    <p>{{ $t('No requests available.') }}</p>
    <p v-if="hasMaxProjectsForCurrentYear">{{ $t('We have no more requests for you this year.') }}</p>
    <p v-else>{{ $t('This client is not satisfied with your performance.') }}</p>

    <div class="actions">
      <Link :href="links.back" class="button">
        {{ $t('back') }}
      </Link>
    </div>
  </article>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  client: ScenarioClient
  hasMaxProjectsForCurrentYear: boolean
  links: Record<string, string>
}>()
</script>
