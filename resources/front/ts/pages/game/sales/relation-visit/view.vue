<template>
  <ul class="actions-list center">
    <li v-for="client in clients" :key="client.href">
      <Link :href="client.href" class="primary big">
        {{ client.title }}
      </Link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: ScenarioClient[]
}>()
</script>
