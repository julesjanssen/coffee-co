<template>
  <div class="tenant-switch">
    <template v-if="currentTenant">
      <VDropdown v-if="hasOtherTenants" theme="tenant-switch">
        <div role="button" class="tenant-name">
          <figure v-if="currentTenant.avatar">
            <img :src="currentTenant.avatar.url" :alt="currentTenant.name" />
          </figure>
          <h1 class="name truncate">{{ currentTenant.name }}</h1>
        </div>

        <template #popper>
          <ul class="tenants">
            <li v-for="tenant in otherTenants" :key="tenant.sqid">
              <a :href="tenant.links.switch" class="truncate">
                {{ tenant.name }}
              </a>
            </li>
          </ul>
        </template>
      </VDropdown>

      <div v-else class="tenant-name">
        <h1 class="name truncate">{{ currentTenant.name }}</h1>
      </div>
    </template>
  </div>
</template>

<script lang="ts" setup>
import { computed, type Ref } from 'vue'

import { useSetting } from '/@admin:composables/settings'
import type { Tenant } from '/@admin:types'

const tenants = useSetting('tenants') as Ref<Tenant[]>
const currentTenant = computed(() => tenants.value?.find((t: Tenant) => t.isCurrent))
const otherTenants = computed(() => tenants.value?.filter((t: Tenant) => !t.isCurrent) ?? [])
const hasOtherTenants = computed(() => otherTenants.value.length > 0)
</script>
