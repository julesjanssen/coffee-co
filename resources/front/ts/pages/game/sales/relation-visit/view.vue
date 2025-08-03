<template>
  <div class="select-client">
    <ul>
      <li v-for="client in clients" :key="client.href">
        <Link :href="client.href">
          {{ client.title }}
        </Link>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import type { ScenarioClient } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: ScenarioClient[]
}>()
</script>
