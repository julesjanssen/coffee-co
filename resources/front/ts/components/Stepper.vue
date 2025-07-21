<template>
  <NumberFieldRoot class="v-stepper" v-bind="{ ...$attrs, ...props }">
    <slot />
    <div>
      <NumberFieldDecrement class="link">
        <Icon name="circle-minus" />
      </NumberFieldDecrement>
      <NumberFieldInput />
      <NumberFieldIncrement class="link">
        <Icon name="circle-plus" />
      </NumberFieldIncrement>
    </div>
  </NumberFieldRoot>
</template>
<script setup lang="ts">
import {
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
  NumberFieldRoot,
  type NumberFieldRootProps,
} from 'reka-ui'

import Icon from '/@front:components/Icon.vue'

const props = withDefaults(defineProps<NumberFieldRootProps>(), {
  min: 0,
  defaultValue: 0,
})
</script>
