<template>
  <header>
    <h2>{{ $t('results') }}</h2>
  </header>

  <dl>
    <div>
      <dt>{{ $t('requests picked up') }}</dt>
      <dd>{{ projectsPickedup }}</dd>
    </div>
    <div>
      <dt>{{ $t('deals won') }}</dt>
      <dd>{{ projectsWon }}</dd>
    </div>
    <div>
      <dt>{{ $t('investment costs') }}</dt>
      <dd><MoneyDisplay :value="investmentCost" /></dd>
    </div>
  </dl>

  <div>
    <ul class="clients-nps">
      <li v-for="client in clients" :key="client.sqid">
        <p>{{ client.title }}</p>
        <strong>{{ client.nps }}</strong>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
// import { Link } from '@inertiajs/vue3'

import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'
// import type { Project } from '/@front:types/shared'

type Client = {
  sqid: string
  title: string
  nps: number
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projectsPickedup: number
  projectsWon: number
  investmentCost: number
  clients: Client[]
}>()
</script>
