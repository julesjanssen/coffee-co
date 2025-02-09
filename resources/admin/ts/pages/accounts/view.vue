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
          <Link v-if="account.can.update" class="button" :href="account.links.update">
            <Icon name="edit" />
            {{ $t('update') }}
          </Link>

          <Dropdown v-if="account.can.invite || account.can.delete">
            <ul role="menu" aria-hidden="true">
              <li role="menuitem" v-if="account.can.invite">
                <button type="button" v-on:click.prevent="inviteAccount" v-close-popper>
                  <Icon name="send" />
                  {{ $t('invite') }}
                </button>
              </li>
              <li role="menuitem" v-if="account.can.delete">
                <button type="button" class="danger" v-on:click.prevent="deleteAccount" v-close-popper>
                  <Icon name="trash" />
                  {{ $t('archive') }}
                </button>
              </li>
            </ul>
          </Dropdown>
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

    <Deferred data="logins">
      <template #fallback>
        <Loader label="inloggegevens laden…" />
      </template>

      <section v-if="logins!.length">
        <header>
          <div>
            <h2>{{ $t('most recent') }}</h2>
            <h1>{{ $t('logins') }}</h1>
          </div>
        </header>

        <div class="table-wrapper">
          <div>
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
                  <td class="ip">
                    <span>
                      <span v-if="login.ip.bogon" class="flag">🌏</span>
                      <span v-else class="flag" :title="login.ip.countryCode">{{ login.ip.countryFlag }}</span>
                      <code class="ip" :title="login.ip.organization">{{ login.ip.value }}</code>
                    </span>
                  </td>
                  <td class="ua">
                    <span>
                      <Icon :name="`device-${login.userAgent.deviceTypeIcon}`" />
                      <span v-if="login.userAgent.isBot" class="value">
                        <div class="truncate" :title="login.userAgent.value">{{ login.userAgent.value }}</div>
                      </span>
                      <span v-else class="value" :title="login.userAgent.value">
                        {{ login.userAgent.clientFamily }} {{ login.userAgent.clientVersion }} @
                        {{ login.userAgent.osName }} {{ login.userAgent.osVersion }}
                      </span>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </Deferred>
  </main>
</template>

<script lang="ts" setup>
import { Deferred, Head, Link, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import Dropdown from '/@admin:components/Dropdown.vue'
import Icon from '/@admin:components/Icon.vue'
import Loader from '/@admin:components/Loader.vue'
import { deleteConfirm } from '/@admin:composables/deleteConfirm'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { http } from '/@admin:shared/http'
import { $t } from '/@admin:shared/i18n'
import type { Authorizable, Login, User } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  account: Authorizable & User
  logins?: Login[]
  links: Record<string, string>
}>()

const inviteAccount = () => {
  http.post(props.account.links.invite).then(() => {
    toast.success($t('Invitation sent.'))
  })
}

const deleteAccount = () => {
  deleteConfirm(props.account.name, {
    icon: 'user',
    action: async () => {
      await http.delete(props.account.links.view)

      toast.success(
        $t('Deleted :name.', {
          name: props.account.name,
        }),
      )

      router.visit(props.links.index)
    },
  })
}
</script>

<style scoped>
td.ip {
  & > span {
    display: flex;
    align-items: center;
    gap: 0.5em;

    & .flag {
      font-size: 1.5em;
      margin-block: -0.25em;
    }
  }

  & code {
    color: var(--gray-500);
  }
}

td.ua {
  & > span {
    display: flex;
    align-items: center;
    gap: 0.65em;

    & .v-icon {
      font-size: 1.25em;
      margin-block: -0.125em;
    }

    & .value {
      max-width: 24em;
      flex: 1;
      color: var(--gray-500);
    }
  }
}
</style>
