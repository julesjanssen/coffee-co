<template>
  <Head :title="session?.title" />

  <main class="facilitator status">
    <div v-if="isSessionPending">
      <button type="button" @click.prevent="startSession">start session</button>
    </div>

    <div v-else-if="isSessionFinished">Finished</div>

    <div v-else-if="isRoundPaused">
      <button type="button" @click.prevent="endRoundPause">
        <span v-if="session.currentRound.isLastRoundOfYear">start next year</span>
        <span v-else>start next month</span>
      </button>
    </div>

    <div v-else-if="isRoundActive">
      <button v-if="session.pausesAfterRound" type="button" @click="unPauseAfterRound">
        do not pause after current month
      </button>
      <button v-else type="button" @click="pauseAfterRound">pause after current month</button>
    </div>
    <!-- <pre>{{ session }}</pre> -->
    <!-- <pre>{{ links }}</pre> -->
  </main>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { computed } from 'vue'

import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error, success } from '/@front:shared/notifications'
import type { GameSession } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  session: GameSession
  links: Record<string, string>
}>()

const isSessionPending = computed(() => props.session.status.value === 'pending')
const isSessionFinished = computed(() => props.session.status.value === 'finished')
const isRoundPaused = computed(() => props.session.roundStatus.value === 'paused')
const isRoundActive = computed(() => props.session.roundStatus.value === 'active')

const startSession = () => {
  http
    .post(props.links.sessionStatus, {
      status: 'playing',
    })
    .then(() => {
      success('yea!')
    })
    .catch(() => {
      error('Failed to start session')
    })
    .finally(() => {
      router.reload()
    })
}

const endRoundPause = () => {
  http
    .post(props.links.roundStatus, {
      status: 'active',
    })
    .then(() => {
      success('Starting next round.')
    })
    .catch(() => {
      error('Failed to start new round.')
    })
}

const pauseAfterRound = () => {
  http
    .post(props.links.sessionSettings, {
      shouldPauseAfterCurrentRound: true,
    })
    .then(() => router.reload())
}

const unPauseAfterRound = () => {
  http
    .post(props.links.sessionSettings, {
      shouldPauseAfterCurrentRound: false,
    })
    .then(() => router.reload())
}
</script>
