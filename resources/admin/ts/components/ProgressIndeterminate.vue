<template>
  <ProgressCircle :percentage="percentage" />
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import ProgressCircle from './ProgressCircle.vue'

const percentage = ref(0)
const delta = ref(2)

onMounted(() => {
  setInterval(() => {
    percentage.value = percentage.value + delta.value
    if (percentage.value > 100) {
      delta.value = -1
    } else if (percentage.value < 0) {
      delta.value = 2
    }
  }, 20)
})
</script>

<style scoped>
@keyframes spin {
  100% {
    rotate: 360deg;
  }
}

svg {
  animation: spin 0.75s linear infinite;
}
</style>
