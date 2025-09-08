<template>
  <li class="client-nps">
    <span class="title">{{ client.title }}</span>
    <span class="score" :style="`color: ${colorForScore};`">
      <ProgressCircle :percentage="client.nps" :size="48" :stroke="6" />
      <span class="value">{{ client.nps }}</span>
    </span>
  </li>
</template>

<script setup lang="ts">
import { computed } from 'vue'

import ProgressCircle from '/@front:components/ProgressCircle.vue'

type Client = {
  sqid: string
  title: string
  nps: number
}

const props = defineProps<{
  client: Client
}>()

const colorForScore = computed(() => {
  const value = props.client.nps

  if (value <= 60) {
    return '#dc2626'
  }

  if (value <= 80) {
    return '#f59e0b'
  }

  return '#008f51'
})
</script>
