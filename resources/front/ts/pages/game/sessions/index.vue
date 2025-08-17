<template>
  <Head :title="$t('sessions')" />

  <div class="sessions index">
    <div v-if="sessions.length === 0" class="empty">
      <p>No active sessions available.</p>
    </div>

    <div>
      <p>Select your session:</p>

      <ul class="actions">
        <li v-for="session in sessions" :key="session.sqid">
          <Link :href="session.links.view" class="button action">
            {{ session.title }}
          </Link>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

import GameLayout from '/@front:layouts/guest.vue'
import { $t } from '/@front:shared/i18n'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  sessions: any[]
}>()
</script>
