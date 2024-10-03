<template>
  <svg width="40" height="40" fill="none">
    <circle class="track" cx="50%" cy="50%" :r="radius" :stroke-width="strokeWidth" stroke="currentcolor" />
    <circle
      cx="50%"
      cy="50%"
      :r="radius"
      :stroke-width="strokeWidth"
      stroke="currentcolor"
      :pathLength="100"
      stroke-dasharray="100 200"
      :stroke-dashoffset="strokeDashoffset"
      stroke-linecap="round"
      transform="rotate(-90)"
      transform-origin="center"
    />
  </svg>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    percentage: number
    strokeWidth?: number
  }>(),
  {
    strokeWidth: 4,
  },
)

const radius = computed(() => `calc(50% - ${props.strokeWidth / 2}px)`)
const strokeDashoffset = computed(() => 100 - props.percentage)
</script>

<style scoped>
.track {
  opacity: 0.22;
}
</style>
