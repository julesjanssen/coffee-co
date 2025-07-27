<template>
  <div class="layout-game">
    <header class="main">
      <h1>{{ appTitle }}</h1>

      <div class="round">
        <span v-if="session.roundStatus.value === 'paused'">
          {{ session.roundStatus.label }}
        </span>
        {{ session.currentRound?.display }}
      </div>
    </header>

    <div class="navigation">
      <nav>
        <ul>
          <li v-for="item in navigation" :key="`nav-${item.href}`">
            <span v-if="item.disabled">
              {{ item.label }}
            </span>
            <Link v-else :href="item.href">
              {{ item.label }}
            </Link>
          </li>
        </ul>
      </nav>
    </div>

    <div class="main-wrapper">
      <slot />
    </div>
  </div>

  <Toaster :expand="true" />
</template>
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Toaster } from 'vue-sonner'

import { useServerSentEvents } from '/@front:composables/server-sent-events'
import type { PageProps } from '/@front:types/shared'

useServerSentEvents()

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const appTitle = computed(() => appProps.value.title)
const navigation = computed(() => appProps.value.navigation || [])
const session = computed(() => page.props.session)
</script>
