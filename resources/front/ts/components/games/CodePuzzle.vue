<template>
  <div class="game code-puzzle">
    <PinInputRoot id="otp" v-model="model" class="pin-input" placeholder="○" @complete="handleComplete">
      <PinInputInput v-for="(id, index) in characters" :key="id" :index="index" inputmode="numeric" />
    </PinInputRoot>
  </div>
</template>

<script lang="ts" setup>
import { PinInputInput, PinInputRoot } from 'reka-ui'

const model = defineModel<string[]>()

withDefaults(
  defineProps<{
    characters?: number
  }>(),
  {
    characters: 4,
  },
)

const emit = defineEmits<{
  complete: []
}>()

const handleComplete = () => {
  emit('complete')
}
</script>

<style scoped>
.pin-input {
  display: flex;
  justify-content: center;
  gap: 0.5em;
}

input[type='text'] {
  width: 2ch;
  font-size: 2rem;
  padding-block: 0.5em;
  text-align: center;
}
</style>
