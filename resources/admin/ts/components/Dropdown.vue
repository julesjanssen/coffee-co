<template>
  <VDropdown v-model:shown="isDropdownShown">
    <button v-if="label" type="button" aria-haspopup="true" :aria-expanded="isDropdownShown">
      {{ label }} <Icon name="chevron-down" />
    </button>
    <button v-else type="button" class="icon-only" aria-haspopup="true" :aria-expanded="isDropdownShown">
      <Icon name="more-vertical" />
    </button>

    <template #popper>
      <slot />
    </template>
  </VDropdown>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'

defineProps<{
  label?: string
}>()

const isDropdownShown = ref(false)
</script>
