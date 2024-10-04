<template>
  <div class="guest-wrap">
    <section>
      <div class="header">
        <div class="logo">
          <h1>{{ appName }}</h1>
        </div>
      </div>

      <slot />
    </section>
  </div>

  <Toaster position="top-center" :expand="true" />
</template>

<script lang="ts" setup>
import { Toaster } from 'vue-sonner'

const appName = import.meta.env.VITE_APP_NAME
</script>

<style src="/@admin:css/layout/guest.css"></style>
