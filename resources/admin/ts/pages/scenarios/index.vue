<template>
  <Head :title="$t('Scenarios')" />

  <main class="scenarios index">
    <section>
      <header>
        <div>
          <h1>{{ $t('Scenarios') }}</h1>
        </div>

        <div class="actions">
          <button v-if="can.update" type="button" class="button" @click.prevent="openScenarioSyncModal">
            <Icon name="refresh-cw" />
            sync scenarios
          </button>
        </div>
      </header>

      <div class="table-wrapper">
        <div>
          <table class="fixed">
            <thead>
              <tr>
                <th>name</th>
                <th>language</th>
                <th>sync date</th>
                <th class="align-right">status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(result, index) in results" :key="index">
                <td>
                  {{ result.title }}
                </td>
                <td>
                  {{ result.locale.label }}
                </td>
                <td>
                  <DateTime :datetime="result.createdAt" :time="true" />
                </td>
                <td class="align-right">
                  <span class="badge" :class="`status-${result.status.value}`">
                    {{ result.status.label }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <Modal name="scenario-sync" :click-to-close="false" :esc-to-close="false">
    <main>
      <section>
        <header>
          <div>
            <h2>Sync scenarios</h2>
          </div>
        </header>

        <div class="body">
          <ul class="list">
            <li v-for="group in scenarioGroups" :key="group.value.baseID" :class="group.value.status">
              <span class="status">
                <ProgressIndeterminate :size="14" class="progress" />
                <span class="indicator"></span>
              </span>

              <span class="title">{{ group.value.title }}</span>
              <span class="locale">{{ group.value.locale.label }}</span>
            </li>
          </ul>
        </div>
      </section>
    </main>
  </Modal>
</template>

<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import { type Ref, ref } from 'vue'
import { toast } from 'vue-sonner'

import DateTime from '/@admin:components/DateTime.vue'
import Icon from '/@admin:components/Icon.vue'
import Modal from '/@admin:components/Modal.vue'
import ProgressIndeterminate from '/@admin:components/ProgressIndeterminate.vue'
import { useModal } from '/@admin:composables/modal'
import { useTask } from '/@admin:composables/useSystemTasks'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { SystemTaskStatusType } from '/@admin:shared/constants'
import { http } from '/@admin:shared/http'
import type { EnumObject } from '/@admin:types'

type ScenarioGroup = {
  baseID: string
  title: string
  locale: EnumObject
  links: {
    sync: string
  }
}

type ScenarioGroupWithStatus = ScenarioGroup & { status: SystemTaskStatusType }

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  results: any[]
  groups: ScenarioGroup[]
  can: Record<string, string>
}>()

const scenarioGroups: Ref<ScenarioGroupWithStatus>[] = props.groups.map((g) => {
  return ref({
    ...g,
    status: 'pending',
  })
})

const { showModal, hideModal } = useModal('scenario-sync')
const { startTask } = useTask()

const openScenarioSyncModal = () => {
  showModal()

  setTimeout(() => {
    initScenarioSync()
  }, 500)
}

const initScenarioSync = async () => {
  for (const scenario of scenarioGroups) {
    try {
      await syncScenario(scenario)
    } catch {
      toast.error(`Failed to sync ${scenario.value.title} (${scenario.value.locale.label}).`)
    }
  }

  setTimeout(() => {
    hideModal()
  }, 1000)
  router.reload()
}

const syncScenario = (scenario: Ref<ScenarioGroupWithStatus>) => {
  return startTask(
    async () => {
      const response = await http.post(scenario.value.links.sync, {})
      return response.data
    },
    {
      onProgress: (t) => {
        scenario.value.status = t.status as SystemTaskStatusType
      },
    },
  )
}
</script>

<style src="/@admin:css/views/scenarios.index.css"></style>
