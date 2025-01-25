<template>
  <Head :title="$t('Accounts')" />

  <main class="accounts index">
    <section>
      <header>
        <div>
          <h1>{{ $t('Accounts') }}</h1>
        </div>

        <div class="actions">
          <!-- <Link v-if="accounts.can.logins" class="button" :href="accounts.links.logins">
            {{ $t('Recent logins') }}
          </Link> -->
          <Link v-if="accounts.can.create" class="button success" :href="accounts.links.create">
            <Icon name="plus" />
            {{ $t('Add Account') }}
          </Link>
        </div>
      </header>

      <div class="table-wrapper">
        <div>
          <table class="fixed">
            <thead>
              <tr>
                <th class="avatar">&nbsp;</th>
                <th>{{ $t('name') }}</th>
                <th>{{ $t('email') }}</th>
                <th style="width: 50%">{{ $t('roles') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(account, index) in accounts.data" :key="index" :class="{ trashed: account.trashed }">
                <td class="avatar">
                  <figure v-if="account.avatar" :style="{ backgroundImage: `url(${account.avatar.url})` }"></figure>
                </td>
                <td>
                  <Link v-if="account.can.view" :href="account.links.view">{{ account.name }}</Link>
                  <span v-else>{{ account.name }}</span>
                </td>
                <td>{{ account.email }}</td>
                <td>
                  <div class="badges">
                    <span v-for="role in account.roles" :key="role.name" class="badge">{{ role.title }}</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Pagination :links="accounts.links" :only="['accounts']" />
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'

import Icon from '/@admin:components/Icon.vue'
import Pagination from '/@admin:components/Pagination.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { Authorizable, PaginatedData, SoftDeletable, User } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  accounts: PaginatedData<Authorizable & SoftDeletable & User> & {
    links: Record<string, string>
    can: Record<string, string>
  }
}>()
</script>

<style src="/@admin:css/views/accounts.index.css"></style>
