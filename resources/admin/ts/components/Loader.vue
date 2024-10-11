<template>
  <div class="v-loader">
    <ProgressIndeterminate :size="14" />
    <span class="label">{{ label }}</span>
  </div>
</template>

<script lang="ts" setup>
import ProgressIndeterminate from '/@admin:components/ProgressIndeterminate.vue'

withDefaults(
  defineProps<{
    label?: string
  }>(),
  {
    label: 'loading…',
  },
)
</script>

<style scoped>
.v-loader {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: var(--gray-400);
  gap: 0.5em;

  & .label {
    /*    color: var(--red-500); */
  }
}
</style>
