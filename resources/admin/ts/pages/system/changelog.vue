<template>
  <Head title="Changelog" />

  <main class="system changelog">
    <section>
      <header>
        <div>
          <h2>Changelog</h2>
          <h1>Application updates</h1>
        </div>
      </header>

      <div v-if="releases.length === 0" class="panel empty">
        <p>No updates</p>
      </div>

      <div class="releases">
        <div v-for="release in releases" :key="release.version" class="release">
          <template v-if="release.sections.length">
            <header>
              <h2>
                <DateTime v-if="release.releasedAt" :datetime="release.releasedAt" />
                <span v-else>Unreleased</span>
              </h2>
            </header>

            <div class="sections">
              <div
                v-for="section in release.sections"
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
          </template>
        </div>
      </div>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

import DateTime from '/@admin:components/DateTime.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

defineProps<{
  releases: any
}>()

defineOptions({
  layout: [AuthLayout],
})
</script>

<style src="/@admin:css/views/system.changelog.css"></style>
