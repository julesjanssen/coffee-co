<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
  </header>

  <form @submit.prevent="submitOffer">
    <fieldset>
      <div class="field">
        <label>product</label>
        <div>
          <input v-model="form.product1" type="text" inputmode="numeric" />
        </div>
      </div>
    </fieldset>

    <fieldset class="actions">
      <button type="submit">{{ $t('submit offer') }}</button>
    </fieldset>
  </form>

  <template v-if="solutions && solutions.length">
    <h3>solutions</h3>
    <div v-for="solution in solutions" :key="solution.products">
      {{ solution.products }}
    </div>
  </template>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  project: any
  solutions?: any[]
}>()

const form = useForm({
  product1: '',
})

const submitOffer = () => {
  form.post(location.pathname)
}
</script>
