<template>
  <svg class="v-icon" aria-hidden="true">
    <use :href="`${spriteHref}#${name}`" />
  </svg>
</template>

<script lang="ts" setup>
import url from '/@sprite.svg'

const spriteHref = import.meta.env.DEV ? '/assets/admin/sprite.svg' : url

defineProps<{
  name: string
}>()
</script>
