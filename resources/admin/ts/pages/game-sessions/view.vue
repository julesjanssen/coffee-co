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
          <dt>facilitator code</dt>
          <dd>{{ session.facilitator.code }}</dd>
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
