<template>
  <span class="money-display tabular-nums">
    {{ displayValue }}
  </span>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

// import NumberDisplay from '/@front/ts/components/NumberDisplay.vue'

const props = withDefaults(
  defineProps<{
    value: number
    suffix?: string
    displaySymbol?: boolean
    animateOnInit?: boolean
  }>(),
  {
    suffix: 'M',
    digits: 0,
    displaySymbol: true,
    animateOnInit: false,
  },
)

const locale = document.querySelector('html')!.getAttribute('lang') ?? 'en-US'

const formatter = new Intl.NumberFormat(locale, {
  minimumFractionDigits: 0,
  style: 'currency',
  currency: 'EUR',
})

const displayValue = computed(() => {
  let value = props.value * 1e6
  let suffix = props.suffix ?? ''

  switch (suffix) {
    case 'k':
    case 'K':
      value /= 1e3
      suffix = 'k'
      break
    case 'm':
    case 'M':
      value /= 1e6
      suffix = 'M'
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

  return formatter.format(value) + (suffix.length ? ` ${suffix}` : '')
})
</script>
