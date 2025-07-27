<template>
  <aside class="v-notification" :class="[type]" role="alert" aria-live="assertive">
    <div>
      <header>{{ title }}</header>
      <p v-if="description">{{ description }}</p>
    </div>

    <div class="actions">
      <template v-if="actions.length">
        <button
          v-for="(action, index) in actions"
          :key="index"
          class="small primary"
          type="button"
          @click="close(action.onClick)"
        >
          <Icon v-if="action.icon" :name="action.icon" />
          {{ action.label }}
        </button>
      </template>
    </div>

    <button type="button" class="dismiss" @click="close()">
      <Icon name="x" />
      <span class="visually-hidden">{{ $t('Dismiss') }}</span>
    </button>
  </aside>
</template>

<script setup lang="ts">
import type { ToastT } from 'vue-sonner'

import Icon from '/@front:components/Icon.vue'
import { $t } from '/@front:shared/i18n'

type Action = {
  label: string
  icon?: string
  onClick: () => void
}

withDefaults(
  defineProps<{
    title: ToastT['title']
    description?: string
    actions?: Action[]
    type?: ToastT['type']
  }>(),
  {
    description: undefined,
    actions: () => [],
    type: 'default',
  },
)

const emit = defineEmits(['closeToast'])

const close = (cb?: () => void) => {
  cb?.()
  emit('closeToast')
}
</script>
