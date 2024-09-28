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
        <span>{{ app.title }}</span>
      </h1>
    </div>

    <Navigation />

    <Account />
  </aside>
</template>

<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'

import Account from './aside/Account.vue'
import Navigation from './aside/Navigation.vue'

type AppProps = {
  title: string
}

const showNavigation = ref(false)

const toggleNavigation = () => (showNavigation.value = !showNavigation.value)

const page = usePage()
const app = computed<AppProps>(() => page.props.app as AppProps)
</script>
