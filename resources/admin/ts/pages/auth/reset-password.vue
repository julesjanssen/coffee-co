<template>
  <Head :title="$t('Forgot password')" />

  <main>
    <article>
      <form @submit.prevent="submitForm">
        <input v-model="form.token" type="hidden" name="token" />

        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" readonly name="email" autocomplete="username" />
              <FormError :error="form.errors.email" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('reset password') }}</label>
            <div>
              <div class="password">
                <input
                  v-model="form.password"
                  type="password"
                  name="password"
                  autocomplete="new-password"
                  :minlength="minPassLength"
                  :maxlength="maxPassLength"
                  :passwordrules="`minlength: ${minPassLength}; maxlength: ${maxPassLength};`"
                  required
                />
              </div>
              <FormError :error="form.errors.password" />

              <p class="help">
                {{ $t('Use at least :t characters.', { t: String(minPassLength) }) }}
                <br />
                {{ $t('suggestion') }}
                <code style="cursor: pointer" @click="useSuggestion">{{ suggestion }}</code>
              </p>
            </div>
          </div>

          <div class="field">
            <label>{{ $t('confirm password') }}</label>
            <div>
              <input v-model="form.password_confirmation" type="password" autocomplete="new-password" required />
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="primary full-width">{{ $t('submit') }}</button>
          </fieldset>
        </footer>
      </form>
    </article>
  </main>
</template>

<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import GuestLayout from '/@admin:layouts/Guest.vue'
import { $t } from '/@admin:shared/i18n'

const props = defineProps<{
  token: string
  email: string
  passwordRules: Record<string, number | boolean>
  suggestion: string
}>()

const minPassLength = computed(() => props.passwordRules.min as number)
const maxPassLength = computed(() => (props.passwordRules.max || 50) as number)

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submitForm = () => {
  form.post(location.pathname, {
    only: ['errors'],
    onSuccess: () => {
      toast($t('Password reset successfully.'))
    },
  })
}

const useSuggestion = () => {
  form.password = props.suggestion
  form.password_confirmation = props.suggestion
}

defineOptions({
  layout: [GuestLayout],
})
</script>
