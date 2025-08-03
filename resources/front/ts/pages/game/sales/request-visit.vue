<template>
  <div v-if="noClientSelected" class="select-client">
    <ul>
      <li v-for="client in clients" :key="client.sqid" v-on:click.prevent="selectClient(client)">
        {{ client.title }}
      </li>
    </ul>
  </div>

  <div v-else-if="true">
    <pre>{{ client }}</pre>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import { http } from '/@front:shared/http'

type Client = {
  sqid: string
  title: string
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: Client[]
}>()

const client = ref<Client | undefined>()
const noClientSelected = computed(() => ! client.value)

const selectClient = (c: Client) => {
  client.value = c
}
</script>
