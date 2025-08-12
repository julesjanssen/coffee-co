<template>
  <main class="facilitator hdma">
    <template v-if="activeRounds > 0">
      <p>
        {{ $t('HDMA has been active for :rounds rounds.', { rounds: String(activeRounds) }) }}<br />
        <span v-if="isEffective">{{ $t('HDMA is in effect.') }}</span>
        <span v-else>{{
          $t('HDMA will be in effect after :rounds active rounds.', { rounds: String(effectiveRounds) })
        }}</span>
      </p>
    </template>
    <template v-else>
      <p>{{ $t('HDMA is not active.') }}</p>
    </template>

    <div v-if="!isEffective" class="actions">
      <button type="button" @click.prevent="makeHdmaEffective">
        {{ $t('enable HDMA') }}
      </button>
    </div>
  </main>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  activeRounds: number
  effectiveRounds: number
}>()

const isEffective = computed(() => props.activeRounds >= props.effectiveRounds)

const makeHdmaEffective = () => {
  http
    .post(location.pathname, {})
    .then(() => router.reload())
    .catch(() => {
      error('HDMA enabling failed.')
    })
}
</script>
