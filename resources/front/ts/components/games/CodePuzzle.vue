<template>
  <div class="game code-puzzle">
    <PinInputRoot v-model="model" type="number" class="pin-input" placeholder="○" @complete="handleComplete">
      <PinInputInput v-for="(id, index) in characters" :key="id" :index="index" inputmode="numeric" />
    </PinInputRoot>
  </div>
</template>

<script lang="ts" setup>
import { PinInputInput, PinInputRoot } from 'reka-ui'

const model = defineModel<number[]>()

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
