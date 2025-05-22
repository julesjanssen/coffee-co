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
  </main>
</template>

<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { cachedHttp } from '/@admin:shared/http'
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
</script>
