<template>
  <Head :title="`Log: ${file.name}`" />

  <main class="system logs view">
    <section>
      <header>
        <div>
          <h2>System / Logs</h2>
          <h1>
            <DateTime :datetime="file.modified" />
          </h1>
        </div>
        <div class="actions">
          <Link :href="links.index" class="button">
            <Icon name="chevron-left" />
            back
          </Link>
        </div>
      </header>

      <div v-if="logs.data.length === 0" class="empty-state">
        <p>No log entries found.</p>
      </div>

      <div v-else class="log-entries">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Level</th>
                <th>Message</th>
                <th class="align-right">Time</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(entry, index) in logs.data" :key="index">
                <td class="level">
                  <span class="badge" :class="`level-${entry.levelName.toLowerCase()}`">
                    {{ entry.levelName }}
                  </span>
                </td>
                <td class="message">
                  <div class="message-text">{{ entry.message }}</div>
                </td>
                <td class="time align-right">
                  <Link :href="entry.links?.view">
                    <DateTime :datetime="entry.datetime" :date="false" :time="true" />
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="logs.links" />
      </div>
    </section>
  </main>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import Pagination from '/@admin:components/Pagination.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

interface FileInfo {
  name: string
  size: number
  modified: string
}

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  file: FileInfo
  logs: any
  links: Record<string, string>
}>()
</script>

<style scoped>
table {
  & th,
  & td {
    vertical-align: top;
  }

  & td.time {
    width: 10em;
    white-space: nowrap;
  }

  & .message-text {
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    text-overflow: ellipsis;
  }

  & .badge {
    border-color: transparent;
    font-size: 0.75rem;
  }

  & .badge.level-emergency,
  & .badge.level-alert,
  & .badge.level-critical,
  & .badge.level-error {
    background-color: var(--red-200);
    color: var(--red-800);
  }

  & .badge.level-warning {
    background-color: var(--yellow-200);
    color: var(--yellow-800);
  }

  & .badge.level-notice,
  & .badge.level-info {
    background-color: var(--blue-200);
    color: var(--blue-800);
  }

  & .badge.level-debug {
    background-color: var(--gray-200);
    color: var(--gray-800);
  }
}
</style>
