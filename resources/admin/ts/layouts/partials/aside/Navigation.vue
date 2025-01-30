<template>
  <div class="aside-nav">
    <nav>
      <div v-for="(group, index) in navigation" :key="index" class="nav-block">
        <h4 v-if="group.title">{{ group.title }}</h4>

        <ul>
          <li v-for="(item, index) in group.items" :key="index" :class="{ active: item.active }">
            <Link :href="item.href">
              <Icon v-if="item.icon" :name="item.icon" />
              <span>{{ item.title }}</span>
            </Link>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import Icon from '/@admin:components/Icon.vue'
import type { PageProps } from '/@admin:types'

const page = usePage()
const navigation = computed(() => (page.props as PageProps).app?.navigation)
</script>
