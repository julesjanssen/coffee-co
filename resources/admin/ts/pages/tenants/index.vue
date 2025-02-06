<template>
  <Head :title="$t('Tenants')" />

  <main class="tenants index">
    <section>
      <header>
        <div>
          <h1>{{ $t('Tenants') }}</h1>
        </div>

        <div class="actions">
          <Link v-if="tenants.can.create" class="button success" :href="tenants.links.create">
            <Icon name="plus" />
            {{ $t('add tenant') }}
          </Link>
        </div>
      </header>

      <div class="table-wrapper">
        <div>
          <table class="fixed">
            <thead>
              <tr>
                <th>{{ $t('name') }}</th>
                <th class="align-right">{{ $t('created') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(tenant, index) in tenants.data" :key="index">
                <td>
                  <div class="name">
                    <Link v-if="tenant.can.view" :href="tenant.links.view">{{ tenant.name }}</Link>
                    <span v-else>{{ tenant.name }}</span>

                    <span v-if="tenant.isCurrent" class="current">(current)</span>
                  </div>
                </td>
                <td class="align-right">
                  <DateTime :datetime="tenant.createdAt" month="short" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Pagination :links="tenants.links" :only="['tenants']" />
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import Pagination from '/@admin:components/Pagination.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { Authorizable, PaginatedData, Tenant } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  tenants: PaginatedData<Authorizable & Tenant> & {
    links: Record<string, string>
    can: Record<string, string>
  }
}>()
</script>

<style scoped>
.name {
  display: flex;
  align-items: baseline;
  gap: 0.5em;

  & .current {
    color: var(--gray-500);
    font-size: 0.84em;
  }
}
</style>
