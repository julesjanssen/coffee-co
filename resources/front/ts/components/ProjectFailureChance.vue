<template>
  <span class="v-failure-chance" :class="valueClass">
    <ProgressBar :value="value" />
    <span class="label">
      {{ $t('chance of failure') }}
      {{ value }}%
    </span>
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

import ProgressBar from './ProgressBar.vue'

const props = defineProps<{
  value: number
}>()

const valueClass = computed(() => {
  if (props.value > 75) {
    return 'danger'
  }

  if (props.value > 40) {
    return 'warning'
  }

  return ''
})
</script>

<style scoped>
.v-failure-chance {
  display: block;
  max-width: 30ch;

  & .label {
    display: block;
    margin-block-start: 0.25em;
  }

  & .v-progress-bar,
  & :deep(.indicator) {
    overflow: hidden;
    height: 6px;
    border-radius: 10px;
    background: var(--gray-200);
  }

  & :deep(.indicator) {
    background: var(--green-600);
  }

  &.warning :deep(.indicator) {
    background: var(--yellow-500);
  }

  &.danger :deep(.indicator) {
    background: var(--red-700);
  }
}
</style>
