<template>
  <ul class="actions center">
    <li v-for="action in actions" :key="action.href">
      <Link :href="action.href" class="button action">
        {{ action.label }}
      </Link>
    </li>
  </ul>
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
