<template>
  <button type="button" class="aside-burger" @click.prevent="toggleNavigation">
    <Icon name="menu" />
  </button>

  <aside :class="{ active: showNavigation }">
    <div class="aside-top">
      <button class="aside-burger" type="button" @click.prevent="toggleNavigation">
        <Icon name="x" />
      </button>

      <TenantSwitch />
    </div>

    <Navigation />

    <Account />
  </aside>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'
import TenantSwitch from '/@admin:components/TenantSwitch.vue'

import Account from './aside/Account.vue'
import Navigation from './aside/Navigation.vue'

const showNavigation = ref(false)

const toggleNavigation = () => (showNavigation.value = !showNavigation.value)

router.on('finish', () => {
  showNavigation.value = false
})
</script>
