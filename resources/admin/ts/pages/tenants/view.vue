<template>
  <Head :title="tenant.name" />

  <main class="tenants view">
    <section>
      <header>
        <div>
          <h2>{{ $t('Tenants') }}</h2>
          <h1>{{ tenant.name }}</h1>
        </div>

        <div class="actions">
          <Link v-if="tenant.can.update" class="button" :href="tenant.links.update">
            <Icon name="edit" />
            {{ $t('update') }}
          </Link>
        </div>
      </header>

      <dl>
        <div>
          <dt>{{ $t('name') }}</dt>
          <dd>{{ tenant.name }}</dd>
        </div>
      </dl>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'

import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { Authorizable, Tenant } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  tenant: Authorizable & Tenant
}>()
</script>
