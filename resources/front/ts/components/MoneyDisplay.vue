<template>
  <NumberDisplay v-bind="$props" :prefix="prefix" class="money-display" />
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import NumberDisplay from '/@front/ts/components/NumberDisplay.vue'

const props = withDefaults(
  defineProps<{
    value: number
    suffix?: string
    displaySymbol?: boolean
    animateOnInit?: boolean
  }>(),
  {
    suffix: undefined,
    digits: 0,
    displaySymbol: true,
    animateOnInit: false,
  },
)

const prefix = computed(() => (props.displaySymbol ? '¢' : undefined))
</script>
