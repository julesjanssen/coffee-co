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
      <!-- <pre>{{ auth }}</pre> -->
    </div>
  </div>

  <Toaster :expand="true" />
</template>
<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
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
const navigation = computed(() => appProps.value.navigation || [])
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
</script>
