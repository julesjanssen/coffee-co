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
        <p>No log files found.</p>
      </div>

      <div v-else class="log-files">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>File</th>
                <th class="align-right">Size</th>
                <th class="align-right">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in logFiles" :key="file.name">
                <td>
                  <Link :href="file.links.view">
                    <code>{{ file.name }}</code>
                  </Link>
                </td>
                <td class="align-right">
                  <span class="size">{{ filesize(file.size) }}</span>
                </td>
                <td class="align-right">
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
import filesize from 'filesize.js'

import DateTime from '/@admin:components/DateTime.vue'
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
}>()
</script>

<style src="/@admin:css/views/system.logs.css"></style>
