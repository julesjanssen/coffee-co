<template>
  <Head title="Styleguide" />

  <main class="system database">
    <section>
      <header>
        <div>
          <h2>Database</h2>
          <h1>Stats &amp; settings</h1>
        </div>
      </header>

      <dl>
        <div>
          <dt>Records</dt>
          <dd><Number :value="dbRows" /></dd>
        </div>

        <div>
          <dt>Size</dt>
          <dd>{{ filesize(dbSize) }}</dd>
        </div>

        <div>
          <dt>Encrypted backups</dt>
          <dd>
            <span class="badge" :class="{ danger: !hasEncryption, success: hasEncryption }">
              {{ hasEncryption ? 'yes' : 'no' }}
            </span>
          </dd>
        </div>

        <div v-for="(variable, index) in variables" :key="index">
          <dt>{{ variable.title }}</dt>
          <dd>
            <pre><code v-html="variable.value" ></code></pre>
          </dd>
        </div>
      </dl>
    </section>

    <section v-if="backups.length > 0">
      <header>
        <div>
          <h1>Backups</h1>
        </div>
      </header>

      <table>
        <thead>
          <tr>
            <th>date / time</th>
            <th class="align-right">size</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(backup, index) in backups" :key="`backup-${index}`">
            <td>
              <DateTime :datetime="backup.createdAt" />
            </td>
            <td class="align-right">
              <span class="badge tabular-nums">
                {{ filesize(backup.filesize) }}
              </span>
            </td>
            <td class="align-right">
              <a class="button small" :href="backup.url">
                <Icon name="download" />
                download
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import filesize from 'filesize.js'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import Number from '/@admin:components/Number.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

type DatabaseVariable = {
  title: string
  value: string
}

type Backup = {
  hash: string
  path: string
  url: string
  filesize: number
  basename: string
  createdAt: string
}

defineProps<{
  dbRows: number
  dbSize: number
  hasEncryption: boolean
  variables: Record<string, DatabaseVariable>
  backups: Backup[]
}>()

defineOptions({
  layout: [AuthLayout],
})
</script>
