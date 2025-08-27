<template>
  <main class="facilitator status">
    <Status :session="session" :links="links" />

    <section class="settings">
      <Form :action="links.sessionSettings" method="post" @finish="finishForm">
        <fieldset>
          <div class="field">
            <label>flow</label>
            <div>
              <label v-for="(label, key) in GameSessionFlow" :key="key" class="radio">
                <input type="radio" :value="key" name="flow" />
                <span class="label">{{ label }}</span>
              </label>
            </div>
          </div>

          <div v-if="isSessionPending" class="field">
            <label>seconds per round</label>
            <div>
              <input type="text" inputmode="numeric" name="secondsPerRound" :value="settings.secondsPerRound" />
            </div>
          </div>

          <div v-if="isSessionPending" class="field">
            <label>HDMA will be effective after active for x rounds</label>
            <div>
              <input
                type="text"
                inputmode="numeric"
                name="hdmaEffectiveRoundCount"
                :value="settings.hdmaEffectiveRoundCount"
              />
            </div>
          </div>
        </fieldset>

        <fieldset class="actions">
          <button type="submit">
            <span v-if="isSessionPending">save settings</span>
            <span v-else>update settings</span>
          </button>
        </fieldset>
      </Form>
    </section>
  </main>
</template>

<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import { computed } from 'vue'

import GameLayout from '/@front:layouts/game.vue'
import { GameSessionFlow } from '/@front:shared/constants'
import { success } from '/@front:shared/notifications'
import type { GameSession } from '/@front:types/shared'

import Status from './status/Status.vue'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  settings: Record<string, number | boolean | string>
  session: GameSession
  links: Record<string, string>
}>()

const isSessionPending = computed(() => props.session.status.value === 'pending')

const finishForm = () => {
  success('Settings saved.')
}
</script>
