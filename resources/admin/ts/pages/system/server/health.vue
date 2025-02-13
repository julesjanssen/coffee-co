<template>
  <section>
    <header>
      <div>
        <h1>Server</h1>
      </div>
    </header>

    <dl v-if="data">
      <div>
        <dt>Last cron exec:</dt>
        <dd>
          <span v-if="!data.cron" class="badge warning">never</span>
          <template v-else>
            <relative-time :datetime="data.cron" prefix="" />
          </template>
        </dd>
      </div>

      <div v-if="data.queue">
        <dt>Queued jobs:</dt>
        <dd>{{ data.queue.jobs ?? 0 }}</dd>
      </div>

      <div>
        <dt>Healthcheck token:</dt>
        <dd>
          <code>
            <span v-if="!data.healthCheckToken" class="badge danger">not set</span>
            <span v-else class="badge">
              {{ data.healthCheckToken }}
            </span>
          </code>
        </dd>
      </div>
    </dl>
  </section>
</template>

<script lang="ts" setup>
defineProps<{
  data: any
}>()
</script>
