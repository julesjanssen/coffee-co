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
            <div class="round">
              <span v-if="session.roundStatus.value === 'paused'">
                {{ session.roundStatus.label }}
              </span>
              {{ session.currentRound?.display }}
            </div>

            <div class="auth">
              <Dropdown :label="authLabel">
                <ul role="menu" aria-hidden="true">
                  <li role="menuitem">
                    <button type="button" @click.prevent="logout">
                      {{ $t('log out') }}
                    </button>
                  </li>
                </ul>
              </Dropdown>
            </div>
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
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, onUnmounted } from 'vue'
import { Toaster } from 'vue-sonner'

import Dropdown from '/@front:components/Dropdown.vue'
import { useServerSentEvents } from '/@front:composables/server-sent-events'
import { $t } from '/@front:shared/i18n'
import type { PageProps } from '/@front:types/shared'

useServerSentEvents()

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const auth = computed(() => page.props.app.auth)
const appTitle = computed(() => appProps.value.title)
const session = computed(() => page.props.session)
const authLabel = computed(() => {
  if (auth.value.type === 'facilitator') {
    return $t('facilitator')
  }

  return auth.value.role?.label
})

const logout = () => {
  router.post('/game/logout')
}

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
