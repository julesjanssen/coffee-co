<template>
  <Head :title="$t('login')" />

  <div>
    <form @submit.prevent="submitForm">
      <ul>
        <li v-for="participant in participants" :key="participant.sqid">
          <label class="radio">
            <input v-model="form.role" type="radio" :value="participant.role.value" />
            <span class="label">{{ participant.role.label }}</span>
          </label>
        </li>
        <li>
          <label class="radio">
            <input v-model="form.role" type="radio" value="facilitator" />
            <span class="label">{{ $t('facilitator') }}</span>
          </label>
        </li>
      </ul>

      <fieldset v-if="form.role === 'facilitator'">
        <div class="field">
          <label>{{ $t('code') }}</label>
          <div>
            <input v-model="form.code" type="text" />
            <FormError :error="form.errors.code" />
          </div>
        </div>
      </fieldset>

      <footer>
        <button type="submit">{{ $t('log in') }}</button>
      </footer>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'

import FormError from '/@front:components/FormError.vue'
// import GameLayout from '/@front:layouts/game.vue'
import { $t } from '/@front:shared/i18n'

// defineOptions({
//   layout: [GameLayout],
// })

defineProps<{
  participants: any[]
}>()

const form = useForm({
  role: '',
  code: '',
})

const submitForm = () => {
  form.post(location.pathname)
}
</script>
