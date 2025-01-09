<template>
  <div class="guest-wrap">
    <section>
      <div>
        <div class="header">
          <div class="logo">
            <h1>{{ ($page.props.app as any).title }}</h1>
          </div>
        </div>

        <slot />
      </div>
    </section>

    <figure v-if="guestImage" :style="{ backgroundImage: `url(${guestImageUrl})` }">
      <figcaption>
        <p>
          Photo by
          <a :href="guestImage.author.url" target="_blank" rel="noopener noreferrer">{{ guestImage.author.name }}</a> on
          <a :href="guestImage.url" target="_blank" rel="noopener noreferrer">{{ guestImage.source }}</a>
        </p>
      </figcaption>
    </figure>
    <figure v-else></figure>
  </div>

  <Toaster position="top-center" :expand="true" />
</template>

<script lang="ts" setup>
import { Toaster } from 'vue-sonner'

const guestImage = (window as any).guestImage ?? false
const guestImageUrl = guestImage ? `https://static.jules.nl/img/blueprint/guest/${guestImage.basename}` : undefined
</script>

<style src="/@admin:css/layout/guest.css"></style>
