<template>
  <section>
    <header>
      <div>
        <h1>Storage</h1>
      </div>
    </header>

    <dl v-if="data">
      <div>
        <dt>Total attachments size:</dt>
        <dd>{{ filesize(data.attachments.size) }} &mdash; {{ filesize(data.attachments.disk) }} on disk</dd>
      </div>

      <div v-for="(stat, index) in data.appStorage" :key="index">
        <dt>{{ stat.name }}</dt>
        <dd>
          {{ filesize(stat.size) }} &nbsp;
          <small>({{ stat.count }} files)</small>
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
