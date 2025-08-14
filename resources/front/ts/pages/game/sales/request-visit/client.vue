<template>
  <div class="center">
    <header class="client">
      <h2>{{ client.title }}</h2>
    </header>

    <p>{{ $t('Do you want to apply lab-consulting?') }}</p>

    <div class="actions">
      <button type="button" @click.prevent="labConsulting(true)">{{ $t('apply') }}</button>
      <button type="button" @click.prevent="labConsulting(false)">{{ $t('do not apply') }}</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  client: ScenarioClient
  requests: any[]
}>()

const form = useForm<{ apply: boolean }>({
  apply: false,
})

const labConsulting = (apply: boolean) => {
  form.apply = apply

  form.post(location.pathname)
}
</script>
