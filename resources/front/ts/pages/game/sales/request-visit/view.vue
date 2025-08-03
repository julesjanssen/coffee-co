<template>
  <div class="select-client">
    <ul>
      <li v-for="client in clients" :key="client.href" >
        <Link :href="client.href">
          {{ client.title }}
        </Link>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import { http } from '/@front:shared/http'

type Client = {
  href: string
  title: string
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: Client[]
}>()

const client = ref<Client | undefined>()
// const noClientSelected = computed(() => ! client.value)

// const selectClient = (c: Client) => {
//   client.value = c
// }
</script>
