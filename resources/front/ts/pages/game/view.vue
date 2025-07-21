<template>
  <Head :title="session.title" />

  <div>
    <p>
      Lorem ipsum dolor sit amet, <strong>{{ session.title }}</strong> consectetur adipisicing elit, sed do eiusmod
      tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
      laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
      cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
      deserunt mollit anim id est laborum.
    </p>
  </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

// import GameLayout from '/@front:layouts/game.vue'

// defineOptions({
//   layout: [GameLayout],
// })

defineProps<{
  authenticatable: any
  session: any
}>()
</script>
