<template>
  <Head :title="session.title" />

  <div class="layout-game client-actions">
    <div class="backdrop"></div>

    <div class="container-wrapper">
      <div class="container">
        <header class="main">
          <div class="logo">
            <h1>
              <Link href="/game">
                <span>{{ appTitle }}</span>
              </Link>
            </h1>
          </div>

          <div class="status">
            <HeaderRound />
            <HeaderAuth />
          </div>
        </header>

        <div class="main-wrapper">
          <div v-if="session.roundStatus.value === 'paused'">paused</div>
          <slot v-else />
        </div>
      </div>
    </div>
  </div>

  <Toaster :expand="true" />
</template>
<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed, onUnmounted } from 'vue'
import { Toaster } from 'vue-sonner'

import { useServerSentEvents } from '/@front:composables/server-sent-events'
import type { PageProps } from '/@front:types/shared'

import HeaderAuth from './partials/HeaderAuth.vue'
import HeaderRound from './partials/HeaderRound.vue'

useServerSentEvents()

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const appTitle = computed(() => appProps.value.title)
const session = computed(() => page.props.session)

if (document.startViewTransition) {
  function handleInertiaStart() {
    document.startViewTransition(async (): Promise<void> => {
      return new Promise((resolve) => {
        document.addEventListener(
          'inertia:finish',
          () => {
            resolve()
          },
          { once: true },
        )
      })
    })
  }

  document.addEventListener('inertia:start', handleInertiaStart)
  onUnmounted(() => {
    document.removeEventListener('inertia:start', handleInertiaStart)
  })
}
</script>
