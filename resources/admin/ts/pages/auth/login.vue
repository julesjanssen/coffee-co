<template>
  <Head :title="$t('Log in')" />

  <main>
    <article>
      <form @submit.prevent="form.post('')">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" name="email" />
              <FormError :error="form.errors.email" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('password') }}</label>
            <div>
              <input v-model="form.password" type="password" name="password" />
              <FormError :error="form.errors.password" />
            </div>
          </div>

          <div class="field remember-me">
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
import { Head, Link, useForm } from '@inertiajs/vue3'

import FormError from '/@admin:components/FormError.vue'
import GuestLayout from '/@admin:layouts/Guest.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

defineOptions({
  layout: [GuestLayout],
})
</script>
