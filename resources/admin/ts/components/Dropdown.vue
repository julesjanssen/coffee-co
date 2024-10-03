<template>
  <VDropdown>
    <button type="button" v-if="label">{{ label }} <Icon name="chevron-down" /></button>
    <button type="button" v-else class="icon-only">
      <Icon name="more-vertical" />
    </button>

    <template #popper>
      <slot />
    </template>
  </VDropdown>
</template>

<script lang="ts" setup>
import Icon from '/@admin:components/Icon.vue'

defineProps<{
  label?: string
}>()
</script>
