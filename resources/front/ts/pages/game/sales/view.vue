<template>
  <div class="sales actions">
    <ul class="actions-list center">
      <li v-for="action in actions" :key="action.href">
        <Link :href="action.href" class="primary big">
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

<style src="/@front:css/views/sales.css"></style>
