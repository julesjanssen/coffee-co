<template>
  <span class="number-display tabular-num">
    <span v-if="prefix" class="prefix">{{ prefix }}</span>
    {{ displayValue }}
  </span>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    value: number
    digits?: number
    prefix?: string
    suffix?: string
  }>(),
  {
    prefix: '',
    suffix: '',
  },
)

const digits = computed(() => props.digits ?? (props.prefix ? 2 : 0))

const locale = document.querySelector('html')!.getAttribute('lang') ?? 'en-US'

const formatter = new Intl.NumberFormat(locale, {
  minimumFractionDigits: digits.value,
  maximumFractionDigits: digits.value,
})

const displayValue = computed(() => {
  let value = props.value
  let suffix = props.suffix

  switch (props.suffix) {
    case 'k':
      value /= 1e3
      break
    case 'm':
      value /= 1e6
      break
    case 'x':
      if (Math.abs(value) > 1e6) {
        value /= 1e6
        suffix = 'M'
      } else if (Math.abs(value) > 10e3) {
        value /= 1e3
        suffix = 'K'
      } else {
        suffix = ''
      }
  }

  // eslint-disable-next-line no-irregular-whitespace
  return formatter.format(value) + (suffix.length ? ` ${suffix}` : '')
})
</script>
