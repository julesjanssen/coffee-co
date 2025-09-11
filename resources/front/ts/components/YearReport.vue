<template>
  <Modal name="report">
    <main class="year-report">
      <header>
        <h1>{{ $t('Newsflash from global') }}</h1>
        <span class="year">{{ report.year }}</span>
      </header>

      <div class="body">
        <article v-html="report.message"></article>
      </div>
    </main>
  </Modal>
</template>

<script lang="ts" setup>
import { useStorage } from '@vueuse/core'
import { ref, watchEffect } from 'vue'

import Modal from '/@front:components/Modal.vue'
import { useModal } from '/@front:composables/modal'
import { http } from '/@front:shared/http'
import { error } from '/@front:shared/notifications'

const { showModal } = useModal('report')
const reportsSeen = useStorage<string[]>('reports-seen', [])

const props = defineProps<{
  year: string
}>()

const report = ref<any>(undefined)

const fetchReport = () => {
  return http.get('/game/year-report')
}

watchEffect(() => {
  const key = `y${props.year}`
  if (reportsSeen.value.includes(key)) {
    return
  }

  fetchReport()
    .then((response) => {
      if (response.status === 204) {
        return
      }

      report.value = response.data

      showModal()
      // reportsSeen.value.push(key)
    })
    .catch(() => {
      error('Could not load year report')
    })
})
</script>
