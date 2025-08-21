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
          <dt>scenario</dt>
          <dd>
            {{ session.scenario.title }}
            <small>({{ session.scenario.locale.label }})</small>
          </dd>
        </div>

        <div>
          <dt>facilitator code</dt>
          <dd class="facilitator">
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

import AuthLayout from '/@admin:layouts/Auth.vue'
import { $t } from '/@admin:shared/i18n'

type GameSession = any

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  session: GameSession
}>()
</script>

<style scoped>
dd.facilitator {
  display: flex;
  align-items: center;
  gap: 0.5em;

  & code {
    font-size: 1.1em;
    letter-spacing: 0.2em;
  }
}
</style>
