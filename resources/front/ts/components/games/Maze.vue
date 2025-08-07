<template>
  <div class="game maze">
    <p>{{ $t('Please complete a level :mazeID maze.', { mazeID: String(level) }) }}</p>

    <div v-if="showReadyButton" class="actions">
      <button type="button" @click.prevent="emitReady">
        {{ $t('done') }}
      </button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

const props = defineProps<{
  level: number
}>()

const emit = defineEmits<{
  ready: []
}>()

const showReadyButton = ref(false)

const start = () => {
  const delay = import.meta.env.DEV ? 3_000 : 10_000 * props.level

  setTimeout(() => {
    showReadyButton.value = true
  }, delay)
}

const emitReady = () => {
  emit('ready')
}

onMounted(() => {
  start()
})
</script>
