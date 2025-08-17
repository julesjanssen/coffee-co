<template>
  <div class="layout-guest">
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
        </header>

        <div class="main-wrapper">
          <slot />
        </div>
      </div>
    </div>
  </div>

  <Toaster :expand="true" />
</template>
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, onUnmounted } from 'vue'
import { Toaster } from 'vue-sonner'

import type { PageProps } from '/@front:types/shared'

const page = usePage<PageProps>()
const appProps = computed(() => page.props.app)
const appTitle = computed(() => appProps.value.title)

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
