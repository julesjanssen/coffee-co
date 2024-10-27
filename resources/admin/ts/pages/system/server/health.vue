<template>
  <section>
    <header>
      <div>
        <h1>Server</h1>
      </div>
    </header>

    <dl>
      <div>
        <dt>Last cron exec:</dt>
        <dd>
          <span class="badge warning" v-if="!data.cron">never</span>
          <template v-else>
            <DateTime :datetime="data.cron" :time="true" />
          </template>
        </dd>
      </div>

      <div>
        <dt>Queued jobs:</dt>
        <dd>{{ data.queue.jobs }}</dd>
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
