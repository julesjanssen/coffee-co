<template>
  <main class="marketing campaign center">
    <div v-if="shouldShowHints" class="hints">
      <article>
        <p v-for="hint in hints" :key="hint">{{ hint }}</p>
      </article>

      <ul class="actions center">
        <li>
          <Link :href="mainRoute">{{ $t('done') }}</Link>
        </li>
      </ul>
    </div>

    <div v-else class="game jigsaw">
      <article>
        <p>
          {{
            $t(
              'Complete a jigsaw puzzle for either a normal or extraordinary campaign. Fill out the code from the jigsaw puzzle below to do the campaign.',
            )
          }}
        </p>
      </article>

      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <div>
              <CodePuzzle v-model="form.code" />
              <FormError :error="form.errors.code" />
            </div>
          </div>
        </fieldset>

        <fieldset class="actions">
          <button type="submit" :disabled="!canSubmit">{{ $t('submit') }}</button>
        </fieldset>
      </form>
    </div>
  </main>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import FormError from '/@front:components/FormError.vue'
import CodePuzzle from '/@front:components/games/CodePuzzle.vue'
import { useGameSession } from '/@front:composables/game-session'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  actions: any[]
}>()

const form = useForm<{
  code: number[]
}>({
  code: [],
})

const { mainRoute } = useGameSession()

const hints = ref<string[]>([])

const codeWasAccepted = ref(false)
const canSubmit = computed(() => form.code.join('').length >= 4)
const shouldShowHints = computed(() => codeWasAccepted.value)

const submitForm = () => {
  if (!canSubmit.value) {
    return
  }

  http
    .post(location.pathname, form.data())
    .then((response) => {
      const data = response.data

      codeWasAccepted.value = true
      hints.value = data.hints
    })
    .catch((err) => {
      const status = err.response?.status
      if (status === 422) {
        form.setError('code', err.response.data?.message || 'Invalid code')
        return
      }

      error('Failed to perform action.')
    })
}
</script>
