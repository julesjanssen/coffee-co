<template>
  <Head :title="$t('Dashboard')" />

  <main>
    <article class="prose">
      <p>
        Donec id elit non mi porta gravida at eget metus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras
        justo odio, dapibus ac facilisis in, egestas eget quam. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
      </p>
    </article>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

import AuthLayout from '/@admin:layouts/Auth.vue'

defineOptions({
  layout: [AuthLayout],
})
</script>
