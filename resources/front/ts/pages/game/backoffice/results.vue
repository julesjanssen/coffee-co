<template>
  <header>
    <h2>{{ $t('results') }}</h2>
  </header>

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
import GameLayout from '/@front:layouts/game.vue'

type Client = {
  sqid: string
  title: string
  nps: number
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: Client[]
}>()
</script>
