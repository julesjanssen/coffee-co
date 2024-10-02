<template>
  <button type="button" class="aside-burger" v-on:click.prevent="toggleNavigation">
    <Icon name="menu" />
  </button>

  <aside :class="{ active: showNavigation }">
    <div class="aside-top">
      <button class="aside-burger" type="button" v-on:click.prevent="toggleNavigation">
        <Icon name="x" />
      </button>

      <h1>
        <span>{{ ($page.props as PageProps).app.title }}</span>
      </h1>
    </div>

    <Navigation />

    <Account />
  </aside>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'
import type { PageProps } from '/@admin:types'

import Account from './aside/Account.vue'
import Navigation from './aside/Navigation.vue'

const showNavigation = ref(false)

const toggleNavigation = () => (showNavigation.value = !showNavigation.value)
</script>
