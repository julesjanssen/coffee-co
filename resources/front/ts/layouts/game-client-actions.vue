<template>
  <Head :title="session.title" />

  <div class="layout-game client-actions">
    <header class="main">
      <h1>{{ appTitle }}</h1>

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
    </header>

    <div class="main-wrapper">
      <slot />
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
        document.startViewTransition(async () => {
            return new Promise((resolve) => {
                document.addEventListener("inertia:finish", () => {
                  resolve();
                }, { once: true })
            });
        });
    }

    document.addEventListener("inertia:start", handleInertiaStart);
    onUnmounted(() => {
        document.removeEventListener("inertia:start", handleInertiaStart);
    });
}
</script>
