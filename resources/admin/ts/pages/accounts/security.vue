<template>
  <Head :title="account.name" />

  <main class="accounts security">
    <section>
      <header>
        <div>
          <h2>Security</h2>
          <h1>Passkeys</h1>
        </div>

        <div class="actions">
          <button type="button" @click.prevent="createPasskey">add a passkey</button>
        </div>
      </header>

      <article class="prose description">
        <p>
          Passkeys can be used for sign-in as a simple and secure alternative to your password and two-factor
          credentials.
        </p>
      </article>

      <article v-if="passkeys.length === 0" class="prose empty">
        <p>No passkeys configured yet.</p>
      </article>
      <table v-else>
        <thead>
          <tr>
            <th>Your passkey nickname</th>
            <th class="align-right">created</th>
            <th class="align-right">last used</th>
            <th class="actions align-right"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="passkey in passkeys" :key="passkey.id">
            <td>
              <div class="passkey-name">
                <span>{{ passkey.name }}</span>
                <span
                  v-if="passkey.backupEligible"
                  class="badge success"
                  title="This passkey is eligible for backup by its provider"
                  >synced</span
                >
              </div>
            </td>
            <td class="align-right">
              <DateTime :datetime="passkey.createdAt" />
            </td>
            <td class="align-right">
              <DateTime v-if="passkey.lastUsedAt" :datetime="passkey.lastUsedAt" />
              <span v-else class="never">never</span>
            </td>
            <td class="align-right actions">
              <button type="button" class="danger" @click.prevent="deletePasskey(passkey)">
                <Icon name="trash" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <section v-if="Array.isArray(sessions)">
      <header>
        <div>
          <h1>Sessions</h1>
        </div>
      </header>

      <article class="prose description">
        <p>
          This is a list of devices that have logged into your account. Revoke any sessions that you do not recognize.
        </p>
      </article>

      <table>
        <thead>
          <tr>
            <th>location</th>
            <th>device</th>
            <th class="align-right">last activity</th>
            <th class="align-right actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="session in sessions" :key="session.id">
            <td class="ip">
              <span>
                <span v-if="session.ip.bogon" class="flag">🌏</span>
                <span v-else class="flag" :title="session.ip.countryCode">{{ session.ip.countryFlag }}</span>
                <code class="ip" :title="session.ip.organization">{{ session.ip.value }}</code>

                <span v-if="session.isCurrent" class="badge current">current</span>
              </span>
            </td>
            <td class="ua">
              <span>
                <Icon :name="`device-${session.userAgent.deviceTypeIcon}`" />
                <span v-if="session.userAgent.isBot" class="value">
                  <div class="truncate" :title="session.userAgent.value">{{ session.userAgent.value }}</div>
                </span>
                <span v-else class="value" :title="session.userAgent.value">
                  {{ session.userAgent.clientFamily }} {{ session.userAgent.clientVersion }} @
                  {{ session.userAgent.osName }} {{ session.userAgent.osVersion }}
                </span>
              </span>
            </td>
            <td class="align-right">
              <DateTime :datetime="session.lastActivity" />
            </td>
            <td class="align-right actions">
              <button v-if="!session.isCurrent" type="button" class="danger" @click.prevent="revokeSession(session)">
                <Icon name="trash" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <Modal name="passkey-create">
    <main>
      <section>
        <header>
          <div>
            <h2>Add a passkey</h2>
          </div>
        </header>

        <div v-if="!passKeysSupported" class="empty">
          <article>
            <p>{{ $t('passkeys:no-support-message') }}</p>
          </article>
        </div>

        <form v-else @submit.prevent="generatePasskey">
          <fieldset>
            <article class="prose">
              <p>
                Your device supports passkeys, a password replacement that validates your identity using touch, facial
                recognition, a device password, or a PIN.
              </p>
              <p>
                Passkeys can be used for sign-in as a simple and secure alternative to your password and two-factor
                credentials.
              </p>
            </article>
          </fieldset>

          <fieldset>
            <div class="field">
              <label>passkey nickname</label>
              <div>
                <input v-model="form.name" type="text" minlength="3" />
                <FormError :error="form.errors.name" />
              </div>
            </div>
          </fieldset>

          <fieldset class="footer actions">
            <button type="submit" class="success" :disabled="form.processing || form.name.length < 3">
              <Icon name="fingerprint" /> add passkey
            </button>
          </fieldset>
        </form>
      </section>
    </main>
  </Modal>
</template>

<script lang="ts" setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { browserSupportsWebAuthn, startRegistration } from '@simplewebauthn/browser'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

import DateTime from '/@admin:components/DateTime.vue'
import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import Modal from '/@admin:components/Modal.vue'
import { deleteConfirm } from '/@admin:composables/deleteConfirm'
import { useModal } from '/@admin:composables/modal'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { http } from '/@admin:shared/http'
import { $t } from '/@admin:shared/i18n'
import type { User } from '/@admin:types'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  account: User
  passkeys: any[]
  sessions: any[] | undefined
}>()

const form = useForm({
  name: '',
})

if (props.sessions && props.sessions.length) {
  const currentSession = props.sessions.find((s) => s.isCurrent)
  if (currentSession) {
    form.name = `${currentSession.userAgent?.clientFamily} ${currentSession.userAgent?.deviceName}`.trim()
  }
}

const { showModal, hideModal } = useModal('passkey-create')
const passKeysSupported = computed(() => browserSupportsWebAuthn())

const createPasskey = () => {
  showModal()
}

const generatePasskey = async () => {
  const { data: options } = await http.get('/admin/account/passkeys/options/create')
  let response: any

  hideModal()

  try {
    response = await startRegistration({ optionsJSON: options })
  } catch (error: any) {
    if (error.name === 'InvalidStateError') {
      toast.error('This passkey is already registered')
      return
    }

    if (error.name === 'NotAllowedError') {
      toast.error('Passkey creation cancelled')
      return
    }

    toast.error('Passkey registration failed.')

    throw error
  }

  http
    .post('/admin/account/passkeys/create', {
      options: JSON.stringify(options),
      passkey: JSON.stringify(response),
      name: form.name,
    })
    .then(() => {
      router.reload()
      toast.success('Passkey added succesfully.')
    })
}

const deletePasskey = (passkey: any) => {
  deleteConfirm(passkey.name, {
    action: async () => {
      http.delete(passkey.links.delete).then(() => router.reload())
    },
    icon: 'fingerprint',
  })
}

const revokeSession = (session: any) => {
  deleteConfirm(async () => {
    http.delete(session.links.delete).then(() => router.reload())
  })
}
</script>

<style scoped>
.passkey-name {
  display: flex;
  align-items: center;
  gap: 0.5em;
}

table {
  table-layout: fixed;

  & th.actions {
    width: 6rem;
  }
}

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

.never {
  color: var(--gray-500);
}

.prose.description {
  margin-block: -1em 2em;
}

.empty {
  padding: var(--viewport-padding-inline);
  color: var(--gray-400);
  text-align: center;
}
</style>
