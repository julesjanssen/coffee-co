<template>
  <section>
    <header>
      <div>
        <h1>Storage</h1>
      </div>
    </header>

    <dl v-if="data">
      <div>
        <dt>total attachments size:</dt>
        <dd>
          {{ filesize(data.attachments.size) }}
          <span v-if="data.attachments.size > 0">&mdash; {{ filesize(data.attachments.disk) }} on disk</span>
        </dd>
      </div>

      <div v-for="(stat, index) in data.appStorage" :key="index">
        <dt>{{ stat.name }}</dt>
        <dd>
          {{ filesize(stat.size) }} &nbsp;
          <small v-if="stat.fileCount">({{ stat.fileCount }} files)</small>
        </dd>
      </div>
    </dl>
  </section>
</template>

<script lang="ts" setup>
import filesize from 'filesize.js'

defineProps<{
  data: any
}>()
</script>
