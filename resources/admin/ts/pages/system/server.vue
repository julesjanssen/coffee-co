<template>
  <Head title="Server" />

  <main class="system server">
    <Health :data="health" />
    <Php :data="php" />
    <Configuration :data="configuration" />
    <OsInternet :data="osInternet" />
    <Storage :data="storage" />
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

import AuthLayout from '/@admin:layouts/Auth.vue'

import Configuration from './server/configuration.vue'
import Health from './server/health.vue'
import OsInternet from './server/os-internet.vue'
import Php from './server/php.vue'
import Storage from './server/storage.vue'

defineProps<{
  configuration: any
  health: any
  osInternet: any
  php: any
  storage: any
}>()

defineOptions({
  layout: [AuthLayout],
})
</script>
