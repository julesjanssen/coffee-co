<template>
  <Head :title="session.title" />

  <div class="layout-game">
    <div class="backdrop"></div>

    <div class="container-wrapper">
      <div class="container" :class="`role-${auth.role?.value}`">
        <header class="main">
          <div class="logo">
            <h1>
              <Link href="/game">
                <span>{{ appTitle }}</span>
              </Link>
            </h1>
          </div>

          <div v-if="!isPausedNotFacilitator" class="navigation">
            <nav>
              <ul>
                <li v-for="item in navigation" :key="`nav-${item.href}`">
                  <span v-if="item.disabled">
                    {{ item.label }}
                  </span>
                  <Link v-else :href="item.href" :class="{ active: item.isActive }">
                    {{ item.label }}
                  </Link>
                </li>
              </ul>
            </nav>
          </div>

          <div class="status">
            <HeaderRound />
            <HeaderAuth />
          </div>
        </header>

        <div class="main-wrapper">
          <div v-if="isPausedNotFacilitator">
            <strong>PAUSED</strong>
            <PauseMessage />
          </div>

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

import PauseMessage from '/@front:components/PauseMessage.vue'
import { useServerSentEvents } from '/@front:composables/server-sent-events'
import type { PageProps } from '/@front:types/shared'

import HeaderAuth from './partials/HeaderAuth.vue'
import HeaderRound from './partials/HeaderRound.vue'

useServerSentEvents()

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const auth = computed(() => page.props.app.auth)
const appTitle = computed(() => appProps.value.title)
const navigation = computed(() => appProps.value.navigation || [])
const session = computed(() => page.props.session)

const isPaused = computed(() => session.value.roundStatus.value === 'paused')
const isFacilitator = computed(() => auth.value.type === 'facilitator')
const isPausedNotFacilitator = computed(() => isPaused.value && !isFacilitator.value)

if (document.startViewTransition) {
  function handleInertiaStart(e: CustomEvent) {
    const targetUrl = e.detail.visit?.url.href
    if (targetUrl && targetUrl === location.href) {
      return
    }

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
