<template>
  <main class="marketing campaign">
    <article>
      <p>
        {{
          $t(
            'Complete a jigsaw puzzle for either a normal or extraordinary campaign. Fill out the code from the jigsaw puzzle below to do the campaign.',
          )
        }}
      </p>
    </article>

    <CodePuzzle v-model="form.code" @complete="handleComplete" />
    <FormError :error="form.errors.code" />
  </main>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import FormError from '/@front:components/FormError.vue'
import CodePuzzle from '/@front:components/games/CodePuzzle.vue'
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  actions: any[]
}>()

const form = useForm<{
  code: string[]
}>({
  code: [],
})

function handleComplete() {
  form.post(location.pathname)
}
</script>
