<template>
  <div v-if="hasError" class="form-error">
    <span>{{ error }}</span>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = defineProps<{
  error: string | undefined
}>()

const hasError = computed(() => props.error && props.error.length > 0)
</script>
