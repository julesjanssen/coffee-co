<template>
  <div v-if="account" class="aside-account">
    <div class="account-slide" :class="{ active: isActive }">
      <div role="button" class="trigger" v-on:click="toggleActive">
        <figure v-if="account.avatar">
          <img :src="account.avatar.url" :alt="account.name" />
        </figure>
        <span class="name truncate">
          {{ account.name }}
        </span>

        <Icon name="chevron-down" />
      </div>

      <Transition name="expand">
        <div class="account-info" v-if="isActive">
          <ul>
            <li>
              <Link href="/admin/account/me">
                {{ $t('my account') }}
              </Link>
            </li>
            <li>
              <button type="button" @click.prevent="logout">
                {{ $t('log out') }}
              </button>
            </li>
          </ul>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'
import type { PageProps } from '/@admin:types'

const isActive = ref(false)
const toggleActive = () => (isActive.value = !isActive.value)

const page = usePage()
const account = computed(() => (page.props as PageProps).app.account)

const logout = () => {
  router.post('/auth/logout')
}
</script>

<style lang="css">
.account-info {
  max-height: 300px;
}

.expand-enter-active,
.expand-leave-active {
  overflow: hidden;
  transition: all 1s ease;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.expand-leave-to {
  transition-duration: 0.3s;
}
</style>
