<template>
  <time :datetime="datetimeString" :title="title">{{ dateDisplay }}</time>
</template>

<script lang="ts" setup>
import { computed, type Ref } from 'vue'

import { useSetting } from '/@admin:composables/settings'

const props = withDefaults(
  defineProps<{
    datetime: Date | string
    date?: boolean
    time?: boolean
    weekday?: 'short' | 'long' | 'narrow' | undefined
    year?: 'numeric' | '2-digit' | false
    month?: 'numeric' | '2-digit' | 'long' | 'short' | 'narrow'
    day?: 'numeric' | '2-digit'
    useTitle?: boolean
  }>(),
  {
    date: true,
    time: false,
    weekday: undefined,
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    useTitle: true,
  },
)

const timezone: Ref<string | undefined> = useSetting('timezone')

const date = computed(() => (props.datetime === 'now' ? new Date() : new Date(props.datetime)))
const datetimeString = computed(() => date.value.toISOString())
const locale = document.querySelector('html')?.lang || 'en-US'

const options: Intl.DateTimeFormatOptions = props.date
  ? {
      weekday: props.weekday,
      year: props.year !== false ? props.year : undefined,
      month: props.month,
      day: props.day,
    }
  : {}

if (props.time) {
  options.hour = '2-digit'
  options.minute = '2-digit'
}

const dateDisplay = computed(() => {
  const formatter = new Intl.DateTimeFormat(locale, {
    ...options,
    timeZone: timezone.value ?? Intl.DateTimeFormat().resolvedOptions().timeZone,
  })

  return formatter.format(date.value)
})

const title = computed(() => {
  if (!props.useTitle) {
    return
  }

  return date.value.toLocaleString(locale, {
    timeZone: timezone.value ?? Intl.DateTimeFormat().resolvedOptions().timeZone,
  })
})
</script>
