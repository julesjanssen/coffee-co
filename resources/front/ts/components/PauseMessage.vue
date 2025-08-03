<template>
  <div class="pause-message">
    <Transition mode="out-in" name="pause-message">
      <p :key="messageID">{{ message }}</p>
    </Transition>
  </div>
</template>

<script lang="ts" setup>
import { useIntervalFn } from '@vueuse/core'
import { shuffle } from 'es-toolkit'
import { computed, ref } from 'vue'

const messages = shuffle([
  'Hold on, we’re caffeinating your experience…',
  'Loading… because coffee doesn’t rush perfection.',
  'Percolating productivity… please sip responsibly.',
  'Loading… we promise this won’t drip forever.',
  'Almost there — like your morning before coffee.',
  'Adding one more shot for Q4…',
  'Grinding the good stuff…',
  'Still brewing something brilliant…',
  'Pouring over the details…',
  'Roasting ideas to perfection…',
  'Extracting full flavor…',
])

const messageID = ref(0)
const message = computed(() => messages[messageID.value])

useIntervalFn(() => {
  messageID.value = messageID.value < messages.length - 1 ? messageID.value + 1 : 0
}, 8000)
</script>

<style scoped>
.pause-message-enter-active,
.pause-message-leave-active {
  transition: all 0.5s ease;
}

.pause-message-enter-from,
.pause-message-leave-to {
  opacity: 0;
}

.pause-message-enter-from {
  translate: 0.5em 0;
}

.pause-message-leave-to {
  translate: -0.5em 0;
}
</style>
