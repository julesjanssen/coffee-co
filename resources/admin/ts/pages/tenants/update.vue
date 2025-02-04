<template>
  <Head :title="tenant.sqid ? $t('update :t', { t: tenant.name }) : $t('create tenant')" />

  <main class="tenants update">
    <section>
      <header>
        <div>
          <h2>{{ $t('Tenants') }}</h2>
          <h1>{{ tenant.sqid ? tenant.name : $t('create tenant') }}</h1>
        </div>
      </header>

      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>{{ $t('name') }}</label>
            <div>
              <input v-model="form.name" type="text" name="title" />
              <FormError :error="form.errors.name" />
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
import { Head, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { $t } from '/@admin:shared/i18n'
import type { Tenant } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  tenant: Tenant
}>()

const form = useForm({
  name: props.tenant.name,
})

const submitForm = () => {
  form.post(location.pathname, {
    only: ['tenant'],
    onSuccess: () => {
      toast.success($t('Tenant saved'))
    },
  })
}
</script>
