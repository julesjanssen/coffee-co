<template>
  <Head title="Server" />

  <main class="system server">
    <Health :data="health" />

    <Deferred data="php">
      <template #fallback>
        <section>
          <header>
            <div>
              <h1>PHP</h1>
            </div>
          </header>
          <Loader />
        </section>
      </template>

      <Php :data="php" />
    </Deferred>

    <Configuration :data="configuration" />

    <Deferred data="osInternet">
      <template #fallback>
        <section>
          <header>
            <div>
              <h1>OS &amp; Internet</h1>
            </div>
          </header>
          <Loader />
        </section>
      </template>

      <OsInternet :data="osInternet" />
    </Deferred>

    <Deferred data="storage">
      <template #fallback>
        <section>
          <header>
            <div>
              <h1>Storage</h1>
            </div>
          </header>
          <Loader />
        </section>
      </template>

      <Storage :data="storage" />
    </Deferred>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { Deferred } from '@inertiajs/vue3'

import Loader from '/@admin:components/Loader.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

import Configuration from './server/configuration.vue'
import Health from './server/health.vue'
import OsInternet from './server/os-internet.vue'
import Php from './server/php.vue'
import Storage from './server/storage.vue'

defineProps<{
  configuration: any
  health: any
  osInternet?: any
  php?: any
  storage?: any
}>()

defineOptions({
  layout: [AuthLayout],
})
</script>
