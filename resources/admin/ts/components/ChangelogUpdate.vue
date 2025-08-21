<template>
  <Modal name="changelog">
    <main v-if="latestRelease" class="system changelog update">
      <section>
        <header>
          <div>
            <h2><DateTime :datetime="latestRelease.releasedAt" /></h2>
            <h1>{{ $t('Application updates') }}</h1>
          </div>
        </header>

        <div class="body">
          <div class="sections">
            <div
              v-for="section in latestRelease.sections"
              :key="section.type.value"
              class="section"
              :class="section.type.toLowerCase()"
            >
              <h3>{{ section.type }}</h3>

              <article class="prose">
                <ul class="list">
                  <li v-for="entry in section.entries" :key="entry" v-html="entry"></li>
                </ul>
              </article>
            </div>
          </div>
        </div>

        <footer>
          <button type="button" @click.prevent="showAll">{{ $t('show all updates') }}</button>
        </footer>
      </section>
    </main>
  </Modal>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { useStorage } from '@vueuse/core'
import { onMounted, ref } from 'vue'

import DateTime from '/@admin:components/DateTime.vue'
import Modal from '/@admin:components/Modal.vue'
import { useModal } from '/@admin:composables/modal'
import { http } from '/@admin:shared/http'

const { showModal, hideModal } = useModal('changelog')
const changelogLastseen = useStorage('changelog-lastseen', '')
const latestRelease = ref<any | undefined>()

const isLastseenUptodate = (latestUpdate: string) => {
  if (!changelogLastseen.value.length) {
    return false
  }

  return changelogLastseen.value === latestUpdate
}

const diffInDays = (date: string) => {
  const diffInMs = new Date().getTime() - new Date(date).getTime()
  const days = diffInMs / 1000 / 3600 / 24

  return Math.round(days)
}

const checkLatestRelease = (release: any) => {
  if (!isLastseenUptodate(release.hash)) {
    latestRelease.value = release
    changelogLastseen.value = release.hash

    if (diffInDays(release.releasedAt) > 30) {
      return
    }

    showModal()
  }
}

const fetchLatestUpdate = () => {
  http
    .get('/admin/system/changelog/latest')
    .then((response) => {
      const data = response.data
      if (data && data.hash) {
        checkLatestRelease(data)
      }
    })
    .catch(() => {
      // nothing
    })
}

const showAll = () => {
  hideModal()

  router.visit('/admin/system/changelog')
}

onMounted(() => {
  setTimeout(() => {
    fetchLatestUpdate()
  }, 500)
})
</script>

<style src="/@admin:css/views/styleguide.css"></style>
