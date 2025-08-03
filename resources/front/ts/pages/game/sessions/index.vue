<template>
  <Head :title="$t('sessions')" />

  <div v-if="sessions.length === 0" class="empty">
    <p>No active games available</p>
  </div>

  <div>
    <ul>
      <li v-for="session in sessions" :key="session.sqid">
        <Link :href="session.links.view">
          {{ session.title }}
        </Link>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

// import GameLayout from '/@front:layouts/game.vue'
import { $t } from '/@front:shared/i18n'

// defineOptions({
//   layout: [GameLayout],
// })

defineProps<{
  sessions: any[]
}>()
</script>
