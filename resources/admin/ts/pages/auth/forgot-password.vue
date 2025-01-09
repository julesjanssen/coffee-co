<template>
  <Head :title="$t('Forgot password')" />

  <main>
    <article>
      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" name="email" />
              <FormError :error="form.errors.email" />
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="primary full-width">{{ $t('submit') }}</button>
          </fieldset>
        </footer>
      </form>

      <Link href="/auth/login"> <Icon name="chevron-left" /> {{ $t('back to login') }} </Link>
    </article>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import GuestLayout from '/@admin:layouts/Guest.vue'
import { $t } from '/@admin:shared/i18n'

const form = useForm({
  email: '',
})

const submitForm = () => {
  form.post(location.pathname, {
    only: ['errors'],
    onSuccess: () => {
      toast.success($t('We sent you an email to reset your password.'))

      router.visit('/auth/login')
    },
  })
}

defineOptions({
  layout: [GuestLayout],
})
</script>
