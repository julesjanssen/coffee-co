<template>
  <Head :title="`Log Entry: ${file.name}`" />

  <main class="system logs entry">
    <section>
      <header>
        <div>
          <h2>Logs / {{ file.name }}</h2>
          <h1>
            <DateTime :datetime="entry.datetime" :time="true" />
          </h1>
        </div>

        <div class="actions">
          <Link :href="links.view" class="button">
            <Icon name="chevron-left" />
            back
          </Link>
        </div>
      </header>

      <dl class="entry-details">
        <div>
          <dt>Level</dt>
          <dd>
            <span class="badge" :class="`level-${entry.levelName.toLowerCase()}`">
              {{ entry.levelName }}
            </span>
            ({{ entry.level }})
          </dd>
        </div>

        <div>
          <dt>Channel</dt>
          <dd>{{ entry.channel }}</dd>
        </div>

        <div>
          <dt>Message</dt>
          <dd class="message">{{ entry.message }}</dd>
        </div>

        <div v-if="entry.context.userId">
          <dt>User ID</dt>
          <dd>{{ entry.context.userId }}</dd>
        </div>

        <div v-if="entry.context.userEmail">
          <dt>User email</dt>
          <dd>{{ entry.context.userEmail }}</dd>
        </div>

        <div v-if="entry.extra.tenantId">
          <dt>Tenant ID</dt>
          <dd>{{ entry.extra.tenantId }}</dd>
        </div>

        <div v-if="entry.url">
          <dt>URL</dt>
          <dd class="url">
            <a :href="entry.url" target="_blank" rel="noopener noreferrer">{{ entry.url }}</a>
          </dd>
        </div>

        <div v-if="entry.ip">
          <dt>IP Address</dt>
          <dd>{{ entry.ip }}</dd>
        </div>
      </dl>
    </section>

    <section v-if="hasException">
      <header>
        <h1>Exception</h1>
      </header>

      <div class="exception-details">
        <dl>
          <div v-if="exception.class">
            <dt>Exception class</dt>
            <dd>
              <code>{{ exception.class }}</code>
            </dd>
          </div>

          <div v-if="exception.message">
            <dt>Exception message</dt>
            <dd class="exception-message">{{ exception.message }}</dd>
          </div>

          <div v-if="exception.file">
            <dt>File</dt>
            <dd>
              <code>{{ exception.file }}:{{ exception.line }}</code>
            </dd>
          </div>

          <div v-if="exception.code">
            <dt>Code</dt>
            <dd>{{ exception.code }}</dd>
          </div>
        </dl>

        <div v-if="exception.trace && exception.trace.length > 0" class="stack-trace">
          <h2>Stack trace</h2>
          <div class="trace-container">
            <pre><code>{{ formatStackTrace(exception.trace) }}</code></pre>
          </div>
        </div>
      </div>
    </section>

    <section v-if="hasContext">
      <header>
        <h1>Context</h1>
      </header>

      <div class="json-container">
        <pre><code>{{ cleanContext }}</code></pre>
      </div>
    </section>
  </main>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  file: any
  entry: any
  links: Record<string, string>
}>()

const cleanContext = computed(() => {
  if (Object.keys(props.entry.context).length === 0) {
    return false
  }

  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const { exception, userId, userEmail, url, ip, ...rest } = props.entry.context

  if (Object.keys(rest).length === 0) {
    return false
  }

  return rest
})

const hasContext = computed(() => {
  return cleanContext.value
})

const hasException = computed(() => {
  return props.entry.context.exception
})

const exception = computed(() => {
  return props.entry.context.exception || {}
})

function formatStackTrace(trace: string[]): string {
  return trace.map((line, index) => `#${index} ${line}`).join('\n')
}
</script>

<style scoped>
& .badge {
  border-color: transparent;
  font-size: 0.75rem;
}

.badge.level-emergency,
.badge.level-alert,
.badge.level-critical,
.badge.level-error {
  background-color: var(--red-200);
  color: var(--red-800);
}

.badge.level-warning {
  background-color: var(--yellow-200);
  color: var(--yellow-800);
}

.badge.level-notice,
.badge.level-info {
  background-color: var(--blue-200);
  color: var(--blue-800);
}

.badge.level-debug {
  background-color: var(--gray-200);
  color: var(--gray-800);
}
</style>
