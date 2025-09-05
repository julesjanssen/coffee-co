<template>
  <Head :title="$t('login')" />

  <div class="sessions view">
    <p>{{ $t('Select your role') }}:</p>

    <form @submit.prevent="submitForm">
      <ul class="actions wrap roles">
        <li v-for="participant in participants" :key="participant.sqid">
          <label class="radio">
            <input v-model="form.role" type="radio" :value="participant.role.value" class="visually-hidden" />
            <span class="label">{{ participant.role.label }}</span>
          </label>
        </li>
        <li>
          <label class="radio">
            <input v-model="form.role" type="radio" value="facilitator" class="visually-hidden" />
            <span class="label">{{ $t('facilitator') }}</span>
          </label>
        </li>
      </ul>

      <fieldset v-if="form.role === 'facilitator'">
        <div class="field">
          <label>{{ $t('code') }}</label>
          <div>
            <PinInputRoot v-model="form.code" type="number" class="pin-input" placeholder="○">
              <PinInputInput v-for="(id, index) in 4" :key="id" :index="index" inputmode="numeric" />
            </PinInputRoot>
            <FormError :error="form.errors.code" />
          </div>
        </div>
      </fieldset>

      <footer>
        <button type="submit" :disabled="form.processing || !form.role">{{ $t('log in') }}</button>
      </footer>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { PinInputInput, PinInputRoot } from 'reka-ui'

import FormError from '/@front:components/FormError.vue'
import GameLayout from '/@front:layouts/guest.vue'
import { $t } from '/@front:shared/i18n'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  participants: any[]
}>()

const form = useForm({
  role: '',
  code: [],
})

const submitForm = () => {
  form.post(location.pathname)
}
</script>
