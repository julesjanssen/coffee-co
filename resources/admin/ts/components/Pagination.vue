<template>
  <div v-if="shouldDisplay" class="pagination">
    <ul role="navigation">
      <li v-if="hasPrev" class="page-item prev">
        <a :href="links.prev!" rel="prev" :ariaLabel="$t('previous')" @click.prevent="paginate(links.prev!)">
          <Icon name="chevron-left" />
          {{ $t('previous page') }}
        </a>
      </li>
      <li v-if="hasNext" class="page-item next">
        <a :href="links.next!" rel="next" :ariaLabel="$t('next')" @click.prevent="paginate(links.next!)">
          {{ $t('next page') }}
          <Icon name="chevron-right" />
        </a>
      </li>
    </ul>
  </div>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

import Icon from '/@admin:components/Icon.vue'

type Links = {
  first?: string | null
  last?: string | null
  prev: string | null
  next: string | null
}

const props = defineProps<{
  only?: string[]
  links: Links
}>()

const hasPrev = computed(() => props.links.prev && props.links.prev !== null)
const hasNext = computed(() => props.links.next && props.links.next !== null)
const shouldDisplay = computed(() => hasPrev.value || hasNext.value)

const paginate = (href: string) => {
  router.get(
    href,
    {},
    {
      only: props.only ?? [],
      preserveState: true,
    },
  )
}
</script>
