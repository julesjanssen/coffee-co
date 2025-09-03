<template>
  <Head :title="session.title" />

  <main class="game-sessions view">
    <section>
      <header>
        <div>
          <h2>{{ $t('Game sessions') }}</h2>
          <h1>{{ session.title }}</h1>
        </div>
      </header>

      <dl>
        <div>
          <dt>created</dt>
          <dd>
            <DateTime :datetime="session.createdAt" />
          </dd>
        </div>

        <div>
          <dt>scenario</dt>
          <dd>
            {{ session.scenario.title }}
            <small>({{ session.scenario.locale.label }})</small>
          </dd>
        </div>

        <template v-if="isPlaying">
          <div>
            <dt>current round</dt>
            <dd>{{ session.currentRound.displayFull }} <small v-if="isPaused">(paused)</small></dd>
          </div>
        </template>

        <div class="facilitator-code">
          <dt>facilitator code</dt>
          <dd>
            <code>{{ session.facilitator.code }}</code>
            <a class="button small" target="_blank" rel="noopener noreferrer" :href="session.links.facilitatorLogin"
              >log in</a
            >
          </dd>
        </div>
      </dl>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

import DateTime from '/@admin:components/DateTime.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { GameSessionStatusType } from '/@admin:shared/constants'
import { $t } from '/@admin:shared/i18n'

type GameSession = any

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  session: GameSession
}>()

const sessionStatus = computed<GameSessionStatusType>(() => props.session.status.value)
const isPlaying = computed(() => sessionStatus.value === 'playing')
const isPaused = computed(() => props.session.roundStatus.value === 'paused')
</script>

<style scoped>
.facilitator-code {
  & dd {
    display: flex;
    align-items: center;
    gap: 0.5em;

    & code {
      font-size: 1.1em;
      letter-spacing: 0.2em;
    }
  }
}
</style>
