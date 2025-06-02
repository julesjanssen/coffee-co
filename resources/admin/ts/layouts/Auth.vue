<template>
  <div class="app auth-wrap">
    <Aside />

    <main>
      <div class="wrapper">
        <div v-if="app.env !== 'production'" class="env-badge">
          <span>{{ app.env }}</span>
        </div>

        <div class="main-content">
          <slot />
        </div>
      </div>
    </main>
  </div>

  <Toaster position="top-right" :expand="true" offset="16px" />

  <ChangelogUpdate />

  <DeleteConfirmModal />
</template>

<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Toaster } from 'vue-sonner'

import ChangelogUpdate from '/@admin:components/ChangelogUpdate.vue'
import DeleteConfirmModal from '/@admin:components/DeleteConfirmModal.vue'

import Aside from './partials/Aside.vue'

type App = {
  env: string
}

const page = usePage()
const app = computed(() => page.props.app as App)
</script>
