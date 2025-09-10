<template>
  <div class="auth">
    <span v-if="isMarketingRole" class="hdma">
      <span v-if="auth.hdma.effective" class="active">{{ $t('HDMA is effective') }}</span>
      <span v-else-if="auth.hdma.enabled" class="active">{{ $t('HDMA is enabled') }}</span>
      <span v-else class="inactive">{{ $t('HDMA is inactive') }}</span>
    </span>

    <Dropdown :label="authLabel">
      <ul role="menu" aria-hidden="true">
        <li role="menuitem">
          <button type="button" @click.prevent="refresh">
            {{ $t('refresh') }}
          </button>
        </li>
        <li role="menuitem">
          <button type="button" @click.prevent="logout">
            {{ $t('log out') }}
          </button>
        </li>
      </ul>
    </Dropdown>
  </div>
</template>
<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import Dropdown from '/@front:components/Dropdown.vue'
import { $t } from '/@front:shared/i18n'
import type { PageProps } from '/@front:types/shared'

const page = usePage<PageProps>()
const auth = computed(() => page.props.app.auth)

const authLabel = computed(() => {
  if (auth.value.type === 'facilitator') {
    return $t('facilitator')
  }

  return auth.value.role?.label
})

const isMarketingRole = computed(() => auth.value.role?.value.includes('marketing'))

const refresh = () => {
  location.reload()
}

const logout = () => {
  router.post('/game/logout')
}
</script>
