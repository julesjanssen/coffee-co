<template>
  <Head :title="account.email ? $t('update :t', { t: account.name }) : $t('create account')" />

  <main class="accounts update">
    <section>
      <header>
        <div>
          <h2>{{ $t('Accounts') }}</h2>
          <h1>{{ account.email ? account.name : $t('Create account') }}</h1>
        </div>
      </header>

      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>{{ $t('email') }}</label>
            <div>
              <input v-model="form.email" type="email" name="email" @change="fetchDetails" />
              <FormError :error="form.errors.email" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('name') }}</label>
            <div>
              <input v-model="form.name" type="text" name="title" />
              <FormError :error="form.errors.name" />
            </div>
          </div>

          <div class="field">
            <label>{{ $t('roles') }}</label>
            <div>
              <label v-for="(role, index) in roles" :key="index" class="checkbox role">
                <input v-model="form.roles" type="checkbox" name="roles" :value="role.name" />
                <div class="label">
                  {{ role.title }}
                  <template v-if="role.description">
                    <div class="help">{{ role.description }}</div>
                  </template>
                </div>
              </label>

              <FormError :error="form.errors.roles" />
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="success" :disabled="form.processing">
              <Icon name="plus" /> {{ $t('save') }}
            </button>
          </fieldset>
        </footer>
      </form>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { $t } from '/@admin:shared/i18n'
import type { User, UserRole } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  account: User
  roles: UserRole[]
  details?: { name: string }
}>()

const form = useForm({
  name: props.account.name,
  email: props.account.email,
  roles: (props.account.roles ?? []).map((r) => r.name),
})

const submitForm = () => {
  const exists = !!props.account.sqid

  form.post(location.pathname, {
    only: ['account', 'roles', 'errors'],
    onSuccess: () => {
      if (!exists) {
        toast.success($t('Account added & invited.'))
      } else {
        toast.success($t('Account saved'))
      }
    },
  })
}

const fetchDetails = () => {
  if (form.name?.length > 0) {
    return
  }

  const email = form.email?.trim() || ''
  if (email.length === 0 || !email.includes('@')) {
    return
  }

  router.reload({
    data: {
      email: email,
    },
    only: ['details'],
    onSuccess: (page) => {
      if (form.name?.length > 0) {
        return
      }

      const details = page.props.details
      if (details) {
        form.name = (details as { name: string }).name
      }
    },
  })
}
</script>

<!-- <style src="/@admin:css/views/accounts.update.css"></style> -->
