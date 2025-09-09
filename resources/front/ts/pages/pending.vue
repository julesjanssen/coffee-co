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

          <div class="status">
            <HeaderRound />
            <HeaderAuth />
          </div>
        </header>

        <div class="main-wrapper">
          <p>pending</p>
        </div>
      </div>
    </div>
  </div>

  <Toaster :expand="true" />
</template>
<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Toaster } from 'vue-sonner'

import { useServerSentEvents } from '/@front:composables/server-sent-events'
import type { PageProps } from '/@front:types/shared'

import HeaderAuth from '../layouts/partials/HeaderAuth.vue'
import HeaderRound from '../layouts/partials/HeaderRound.vue'

useServerSentEvents()

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const auth = computed(() => page.props.app.auth)
const appTitle = computed(() => appProps.value.title)
const session = computed(() => page.props.session)
</script>
