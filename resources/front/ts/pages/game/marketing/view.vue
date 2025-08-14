<template>
  <div class="center">
    <ul class="actions">
      <li v-for="action in actions" :key="action.href">
        <Link :href="action.href" class="button">
          {{ action.label }}
        </Link>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  actions: any[]
}>()
</script>
