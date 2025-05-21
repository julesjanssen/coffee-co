<template>
  <Head :title="account.name" />

  <main class="accounts update me">
    <section>
      <header>
        <div>
          <h2>{{ $t('my account') }}</h2>
          <h1>{{ account.name }}</h1>
        </div>
      </header>

      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" name="email" readonly />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('name') }}</label>
            <div>
              <input v-model="form.name" type="text" name="name" />
              <FormError :error="form.errors.name" />
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="success" :disabled="form.processing">
              <Icon name="check" /> {{ $t('save') }}
            </button>
          </fieldset>
        </footer>
      </form>
    </section>

    <button type="button" @click.prevent="addKey">voeg sleutel toe</button>
  </main>
</template>

<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3'
import {
  // browserSupportsWebAuthn,
  startRegistration,
} from '@simplewebauthn/browser'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { cachedHttp, http } from '/@admin:shared/http'
import { $t } from '/@admin:shared/i18n'
import type { User } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  account: User
}>()

const form = useForm({
  name: props.account.name,
  email: props.account.email,
})

const submitForm = () => {
  form.post(location.pathname, {
    onSuccess: async () => {
      // todo: remove ! after axios-cache-adapter fix
      await cachedHttp.storage.clear!()
      toast.success($t('Account saved'))
    },
  })
}

const addKey = async () => {
  const { data: options } = await http.get('/admin/account/passkeys/options/create')
  let response

  try {
    response = await startRegistration({ optionsJSON: options })
  } catch (error: any) {
    if (error.name === 'NotAllowedError') {
      toast.error('Passkey creation cancelled')
      return
    }

    toast.error('Passkey registration failed.')

    throw error
  }

  http
    .post('/admin/account/passkeys/create', {
      options: JSON.stringify(options),
      passkey: JSON.stringify(response),
    })
    .then(() => {
      toast.success('Passkey added succesfully.')
    })
}
</script>
