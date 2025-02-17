<template>
  <Head :title="$t('Forgot password')" />

  <main>
    <article style="margin-bottom: 3em">
      <p>{{ $t('Choose a password to enable your account.') }}</p>
    </article>

    <article>
      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" readonly name="email" autocomplete="username" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('password') }}</label>
            <div>
              <div class="password">
                <input
                  v-model="form.password"
                  type="password"
                  name="password"
                  autocomplete="new-password"
                  :minlength="minPassLength"
                  :passwordrules="`minlength: ${minPassLength}; maxlength: 72;`"
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
            <button type="submit" class="primary full-width">{{ $t('activate account') }}</button>
          </fieldset>
        </footer>
      </form>
    </article>
  </main>
</template>

<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import GuestLayout from '/@admin:layouts/Guest.vue'
import { $t } from '/@admin:shared/i18n'

const props = defineProps<{
  token: string
  email: string
  minPassLength: number
  suggestion: string
}>()

const form = useForm({
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submitForm = () => {
  form.post(location.href, {
    only: ['errors'],
    onSuccess: () => {
      toast($t('Account activated.'))
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
