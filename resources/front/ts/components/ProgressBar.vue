<template>
  <ProgressRoot v-model="progressValue" class="v-progress-bar" style="transform: translateZ(0)">
    <ProgressIndicator class="indicator" :style="`transform: translateX(-${100 - progressValue}%)`" />
  </ProgressRoot>
</template>

<script setup lang="ts">
import { ProgressIndicator, ProgressRoot } from 'reka-ui'
import { nextTick, ref, watch } from 'vue'

const props = defineProps<{
  value: number
}>()

const progressValue = ref(0)

watch(
  () => props.value,
  async (value) => {
    await nextTick()
    progressValue.value = value
  },
  { immediate: true },
)
</script>
