<template>
  <div class="pagination" v-if="shouldDisplay">
    <ul role="navigation">
      <li class="page-item prev" v-if="hasPrev">
        <a :href="links.prev!" rel="prev" :ariaLabel="$t('previous')" v-on:click.prevent="paginate(links.prev!)">
          <Icon name="chevron-left" />
          {{ $t('previous page') }}
        </a>
      </li>
      <li class="page-item next" v-if="hasNext">
        <a :href="links.next!" rel="next" :ariaLabel="$t('next')" v-on:click.prevent="paginate(links.next!)">
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
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

const props = defineProps<{
  only?: string[]
  links: Links
}>()

const hasPrev = computed(() => props.links.prev !== null)
const hasNext = computed(() => props.links.next !== null)
const shouldDisplay = computed(() => hasPrev.value || hasNext.value)

const paginate = (href: string) => {
  router.get(
    href,
    {},
    {
      only: props.only,
      preserveState: true,
    },
  )
}
</script>
