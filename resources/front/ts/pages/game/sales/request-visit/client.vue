<template>
  <div class="client">
    <ul>
      <li>
        <strong>{{ client.title }}</strong>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import GameLayout from '/@front:layouts/game-client-actions.vue'
import { http } from '/@front:shared/http'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  client: Client
  requests: any[]
}>()
</script>
