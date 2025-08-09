<template>
  <main class="marketing campaign">
    <div v-if="shouldShowHints">
      <article>
        <p v-for="hint in hints" :key="hint">{{ hint }}</p>
      </article>
    </div>

    <div v-else>
      <article>
        <p>
          {{
            $t(
              'Complete a jigsaw puzzle for either a normal or extraordinary campaign. Fill out the code from the jigsaw puzzle below to do the campaign.',
            )
          }}
        </p>
      </article>

      <CodePuzzle v-model="code" @complete="handleComplete" />
      <FormError :error="codeError" />
    </div>
  </main>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

import FormError from '/@front:components/FormError.vue'
import CodePuzzle from '/@front:components/games/CodePuzzle.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  actions: any[]
}>()

const code = ref<number[]>([])
const codeError = ref<string | undefined>()
const hints = ref<string[]>([])

const codeWasAccepted = ref(false)
const shouldShowHints = computed(() => codeWasAccepted.value)

function handleComplete() {
  codeError.value = undefined

  http
    .post(location.pathname, {
      code: code.value,
    })
    .then((response) => {
      const data = response.data

      codeWasAccepted.value = true
      hints.value = data.hints
    })
    .catch((err) => {
      const status = err.response?.status
      if (status === 422) {
        codeError.value = err.response.data?.message || 'Invalid code'
        return
      }

      error('Failed to perform action.')
    })
}
</script>
