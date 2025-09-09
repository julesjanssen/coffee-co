<template>
  <template v-if="shouldDisplay">
    <Transition mode="out-in" name="round-fade">
      <div :key="transitionKey" class="round">
        <Icon v-if="isPaused" name="calendar-slash" />
        <Icon v-else name="calendar-blank" />

        <span class="label">
          {{ session.currentRound?.display }}
          <span v-if="isPaused">&mdash; {{ $t('paused') }}</span>
        </span>
      </div>
    </Transition>
  </template>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import Icon from '/@front:components/Icon.vue'
import type { PageProps } from '/@front:types/shared'

const page = usePage<PageProps>()

const session = computed(() => page.props.session)
const isPaused = computed(() => session.value.roundStatus.value === 'paused')
const shouldDisplay = computed(() => ['playing', 'finished'].includes(session.value.status.value))
const transitionKey = computed(() => `${isPaused.value ? 'y' : 'n'}:${session.value.currentRound?.id}`)
</script>

<style scoped>
.round-fade-enter-active,
.round-fade-leave-active {
  transition: all 0.3s ease;
}

.round-fade-enter-from,
.round-fade-leave-to {
  opacity: 0;
}

.round-fade-enter-from {
  translate: 0.25em 0;
}

.round-fade-leave-to {
  translate: -0.25em 0;
}
</style>
