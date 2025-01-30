<template>
  <teleport :to="teleportTarget">
    <div class="v-modal v-modal-wrapper" v-bind="$attrs" :class="name">
      <transition appear name="modal-backdrop">
        <div
          class="v-modal-backdrop"
          :class="{ clickable: clickToClose }"
          v-if="isModalShown"
          @click="handleBackdropClick"
          aria-hidden="true"
        ></div>
      </transition>
      <transition appear name="modal-content">
        <div
          class="v-modal-content"
          role="dialog"
          aria-modal="true"
          aria-label="Modal window"
          tabindex="-1"
          v-if="isModalShown"
        >
          <slot />
        </div>
      </transition>
    </div>
  </teleport>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'

import { createModal } from '/@admin:composables/modal'

const props = withDefaults(
  defineProps<{
    name: string
    teleportTo?: string
    clickToClose?: boolean
    escToClose?: boolean
  }>(),
  {
    teleportTo: 'body',
    clickToClose: true,
    escToClose: true,
  },
)

const teleportTarget = ref('body')
const name = computed(() => props.name)

const { hideModal, isModalShown } = createModal(name.value, {
  clickToClose: props.clickToClose,
  escToClose: props.escToClose,
})

const handleBackdropClick = () => {
  if (props.clickToClose) {
    hideModal()
  }
}

onMounted(() => {
  teleportTarget.value = props.teleportTo
})
</script>
