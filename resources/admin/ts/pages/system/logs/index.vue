<template>
  <Head title="Logs" />

  <main class="system logs index">
    <section>
      <header>
        <div>
          <h2>System</h2>
          <h1>Log files</h1>
        </div>
      </header>

      <div v-if="logFiles.length === 0" class="empty-state">
        <p>No log files found in {{ logPath }}</p>
      </div>

      <div v-else class="log-files">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>File</th>
                <th>Size</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in logFiles" :key="file.name">
                <td>
                  <Link
                    :href="file.links.view"
                  >
                    <code>{{ file.name }}</code>
                  </Link>
                </td>
                <td>
                  <span class="size">{{ filesize(file.size) }}</span>
                </td>
                <td>
                  <DateTime :datetime="file.modified" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DateTime from '/@admin:components/DateTime.vue'
import filesize from 'filesize.js'
import AuthLayout from '/@admin:layouts/Auth.vue'

type LogFile = {
  name: string
  path: string
  size: number
  modified: string
  links: {
    view: string
  }
}

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  logFiles: LogFile[]
  logPath: string
}>()
</script>
