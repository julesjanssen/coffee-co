<template>
  <Head :title="account.name" />

  <main class="accounts view">
    <section>
      <header>
        <div>
          <h2>{{ $t('Accounts') }}</h2>
          <h1>{{ account.name }}</h1>
        </div>

        <div class="actions">
          <!-- <v-delete-confirm v-if="account.can.delete" :url="account.links.delete">
            <button type="button" class="danger">
              <Icon name="trash" />
              {{ $t('archive') }}
            </button>
          </v-delete-confirm> -->
          <Link v-if="account.can.invite" as="button" type="button" method="post" :href="account.links.invite">
            {{ $t('invite') }}
          </Link>
          <Link v-if="account.can.update" class="button" :href="account.links.update">
            <Icon name="edit" />
            {{ $t('update') }}
          </Link>
        </div>
      </header>

      <dl>
        <div>
          <dt>{{ $t('name') }}</dt>
          <dd>{{ account.name }}</dd>
        </div>

        <div>
          <dt>{{ $t('email') }}</dt>
          <dd>
            <a :href="`mailto:${account.email}`">{{ account.email }}</a>
          </dd>
        </div>

        <div>
          <dt>{{ $t('roles') }}</dt>
          <dd>
            <div class="badges">
              <span v-for="role in account.roles" :key="role.name" class="badge">{{ role.title }}</span>
            </div>
          </dd>
        </div>
      </dl>
    </section>

    <section v-if="logins.length">
      <header>
        <div>
          <h2>{{ $t('most recent') }}</h2>
          <h1>{{ $t('logins') }}</h1>
        </div>
      </header>

      <table>
        <thead>
          <tr>
            <th>date / time</th>
            <th>ip</th>
            <th>user-agent</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="login in logins" :key="login.sqid">
            <td>
              <relative-time :datetime="login.createdAt" prefix="" />
            </td>
            <td>
              <code>{{ login.ip }}</code>
            </td>
            <td>
              <small class="truncate">{{ login.userAgent }}</small>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'

import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { Authorizable, Login, User } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  account: Authorizable & User
  logins: Login[]
}>()
</script>
