<template>
  <Modal v-if="details" :name="details.modalName" class="delete-confirm">
    <main>
      <header>
        <figure>
          <Icon :name="details.icon" />
        </figure>
      </header>

      <div class="body">
        <div class="question">
          <p v-if="details.name">{{ $t('Are you sure you want to delete “:name?”', { name: details.name }) }}</p>
          <p v-else>{{ $t('Are you sure you want to delete this item?') }}</p>
        </div>
      </div>

      <footer class="actions">
        <button type="button" @click.prevent="cancelDelete">
          {{ cancelLabel }}
        </button>
        <button type="button" class="danger" :disabled="isProcessing" @click.prevent="confirmDelete">
          {{ deleteLabel }}
        </button>
      </footer>
    </main>
  </Modal>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import Icon from '/@admin:components/Icon.vue'
import Modal from '/@admin:components/Modal.vue'
import { details, hideModal } from '/@admin:composables/deleteConfirm'
import { $t } from '/@admin:shared/i18n'

const isProcessing = ref(false)
const cancelLabel = computed(() => details.cancelLabel ?? $t('cancel'))
const deleteLabel = computed(() => details.deleteLabel ?? $t('delete'))

const cancelDelete = () => {
  hideModal()
}

const confirmDelete = () => {
  isProcessing.value = true

  Promise.resolve(details.action()).then(() => {
    isProcessing.value = false
    hideModal()
  })
}
</script>
