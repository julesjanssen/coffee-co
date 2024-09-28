<template>
  <i class="v-icon" aria-hidden="true" v-html="iconRaw"></i>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'

const icons = import.meta.glob('../../icons/**/*.svg', { query: '?raw', import: 'default' })

const iconRaw = ref<string>('')

const props = defineProps<{
  name: string
}>()

const setIcon = async () => {
  const path = `../../icons/${props.name}.svg`
  const icon = icons[path]

  if (icon) {
    const svg = await icon()
    iconRaw.value = svg as string
  }
}

watch(props, () => setIcon(), { immediate: true })
</script>
