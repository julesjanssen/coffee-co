<template>
  <div class="guest-wrap">
    <section>
      <div>
        <div class="header">
          <div class="logo">
            <h1>{{ title }}</h1>
          </div>
        </div>

        <slot />
      </div>
    </section>

    <figure v-if="guestImage" :style="{ backgroundImage: `url(${guestImageUrl})` }">
      <figcaption>
        <p>
          Photo by
          <a :href="guestImage.author.url" target="_blank" rel="noopener noreferrer">{{ guestImage.author.name }}</a>
          <template v-if="guestImage.source">
            on <a :href="guestImage.url" target="_blank" rel="noopener noreferrer">{{ guestImage.source }}</a>
          </template>
        </p>
      </figcaption>
    </figure>
    <figure v-else></figure>
  </div>

  <Toaster position="top-center" :expand="true" />
</template>

<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Toaster } from 'vue-sonner'

const page = usePage()
const title = computed(() => (page.props.app as any)?.title)
const guestImage = computed(() => (page.props.app as any)?.guestImage)
const guestImageUrl = computed(() => (guestImage.value ? guestImage.value.img : undefined))
</script>

<style src="/@admin:css/layout/guest.css"></style>
