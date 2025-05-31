<template>
  <Head :title="$t('Log in')" />

  <main>
    <article>
      <form class="login" @submit.prevent="form.post('')">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" name="email" autofocus autocomplete="username webauthn" />
              <FormError :error="form.errors.email" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('password') }}</label>
            <div>
              <input v-model="form.password" type="password" name="password" autocomplete="current-password webauthn" />
              <FormError :error="form.errors.password" />
            </div>
          </div>
        </fieldset>

        <fieldset v-if="passKeysSupported" class="passkey">
          <div class="divider">
            <span>{{ $t('or') }}</span>
          </div>

          <button type="button" @click.prevent="usePasskey">
            <Icon name="fingerprint" /> {{ $t('Log in using a passkey') }}
          </button>
        </fieldset>

        <fieldset class="remember-me">
          <div class="field">
            <label class="checkbox">
              <input id="remember" v-model="form.remember" type="checkbox" />
              <span class="label" for="remember">{{ $t('remember password') }}</span>
            </label>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="primary full-width">{{ $t('sign in') }}</button>
          </fieldset>
        </footer>
      </form>

      <footer>
        <p class="forgot-password">
          <Link href="/auth/forgot-password">
            {{ $t('forgot password?') }}
          </Link>
        </p>
      </footer>
    </article>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { browserSupportsWebAuthn, startAuthentication } from '@simplewebauthn/browser'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import GuestLayout from '/@admin:layouts/Guest.vue'
import { http } from '/@admin:shared/http'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

defineOptions({
  layout: [GuestLayout],
})

const passKeysSupported = computed(() => browserSupportsWebAuthn())

const usePasskey = async () => {
  const { data: options } = await http.get('/auth/passkeys/options/auth')
  let response

  try {
    response = await startAuthentication({ optionsJSON: options })
  } catch {
    toast.error('Passkey authentication failed.')
    return
  }

  http
    .post('/auth/passkeys/login', {
      start_authentication_response: JSON.stringify(response),
      remember: form.remember,
    })
    .then((response) => {
      router.visit(response.data.url)
    })
    .catch((error) => {
      const message = error.response?.data?.error ?? 'Could not login using the given passkey.'
      toast.error(message)
    })
}
</script>
