<template>
  <section>
    <header>
      <div>
        <h1>OS &amp; Internet</h1>
      </div>
    </header>

    <dl>
      <div>
        <dt>OS</dt>
        <dd>{{ data.os }}</dd>
      </div>

      <div>
        <dt>hostname</dt>
        <dd>{{ data.hostname }}</dd>
      </div>

      <div>
        <dt>public IP</dt>
        <dd>{{ data.ip }}</dd>
      </div>

      <div v-if="data.certificate">
        <dt>certificate</dt>
        <dd>
          {{ data.certificate.issuer }} ({{ data.certificate.organization }}) valid to
          <DateTime :datetime="data.certificate.validTo" />
        </dd>
      </div>
    </dl>
  </section>
</template>

<script lang="ts" setup>
import DateTime from '/@admin:components/DateTime.vue'

defineProps<{
  data: any
}>()
</script>
