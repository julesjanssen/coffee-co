<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
  </header>

  <article>
    <p>
      <strong>{{ project.title }}</strong>
    </p>
    <p><MoneyDisplay :value="project.price" /></p>

    <template v-if="project.labConsultingApplied">
      <p v-if="project.labConsultingIncluded">
        {{ $t('You know your client well. You have now inluded lab-consulting in your visit.') }}
      </p>
      <p v-else>{{ $t('You do not know your client well enough to include lab-consulting in your visit.') }}</p>
    </template>

    <p>{{ $t('For further details, check your pending requests list.') }}</p>

    <div class="actions">
      <Link :href="links.back" class="button">
        {{ $t('back') }}
      </Link>
    </div>
  </article>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game-client-actions.vue'
import type { Project, ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  project: Project
  client: ScenarioClient
  links: Record<string, string>
}>()
</script>
