<template>
  <div class="sales request-visit client">
    <header class="client">
      <h2>{{ client.title }}</h2>
    </header>

    <p>{{ $t('Do you want to apply lab-consulting?') }}</p>

    <ul class="actions">
      <li>
        <button type="button" class="primary" @click.prevent="labConsulting(true)">{{ $t('apply') }}</button>
      </li>
      <li>
        <button type="button" @click.prevent="labConsulting(false)">{{ $t('do not apply') }}</button>
      </li>
    </ul>
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
