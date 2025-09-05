<template>
  <Head :title="session.title" />

  <main class="game-sessions view">
    <section>
      <header>
        <div>
          <h2>{{ $t('Game sessions') }}</h2>
          <h1>{{ session.title }}</h1>
        </div>

        <div class="actions">
          <button v-if="can.update && (isPending || isPlaying)" type="button" @click.prevent="closeSession">
            <Icon name="x" />
            close session
          </button>
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

        <template v-if="isClosed">
          <div>
            <dt>status</dt>
            <dd>closed</dd>
          </div>
        </template>

        <template v-if="isPlaying">
          <div>
            <dt>current round</dt>
            <dd>{{ session.currentRound.displayFull }} <small v-if="isPaused">(paused)</small></dd>
          </div>
        </template>

        <div v-if="isPending || isPlaying" class="facilitator-code">
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
import { Head, router } from '@inertiajs/vue3'
import { computed } from 'vue'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { GameSessionStatusType } from '/@admin:shared/constants'
import { http } from '/@admin:shared/http'
import { $t } from '/@admin:shared/i18n'

type GameSession = any

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  session: GameSession
  can: Record<string, string>
  links: Record<string, string>
}>()

const sessionStatus = computed<GameSessionStatusType>(() => props.session.status.value)
const isPending = computed(() => sessionStatus.value === 'pending')
const isPlaying = computed(() => sessionStatus.value === 'playing')
const isClosed = computed(() => sessionStatus.value === 'closed')
const isPaused = computed(() => props.session.roundStatus.value === 'paused')

const closeSession = () => {
  http
    .post(props.links['status-update'], {
      status: 'closed',
    })
    .then(() => {
      router.reload()
    })
}
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
