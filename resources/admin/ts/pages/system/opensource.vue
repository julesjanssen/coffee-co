<template>
  <Head title="Open source" />

  <main class="system opensource">
    <section>
      <header>
        <div>
          <h1>Open Source</h1>
          <h2>Acknowledgments</h2>
        </div>
      </header>

      <article>
        <p>
          <strong>{{ app.title }}</strong> wouldn’t be possible without the code written and freely shared by the open
          source community. <br />This web application includes code from the following modules.
        </p>
      </article>

      <table>
        <tbody>
          <template v-for="(list, license) in packages" :key="license">
            <tr>
              <td colspan="2">
                <strong>{{ license }}</strong>
              </td>
            </tr>
            <tr v-for="(packagesPerAuthor, listIndex) in list" :key="listIndex">
              <td>
                <template v-for="(authorPackage, packageIndex) in packagesPerAuthor" :key="packageIndex">
                  <a :href="authorPackage.url" target="_blank" rel="noopener noreferrer">{{ authorPackage.name }}</a>
                  <br />
                </template>
              </td>
              <td>{{ getPackageAuthor(packagesPerAuthor) }}</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
          </template>
        </tbody>
      </table>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import AuthLayout from '/@admin:layouts/Auth.vue'

type App = {
  title: string
}

type PackageType = {
  authors: string
  name: string
  url: string
  licence: string
}

defineProps<{
  packages: Record<string, PackageType[][]>
}>()

defineOptions({
  layout: [AuthLayout],
})

const page = usePage()
const app = computed(() => page.props.app as App)

const getPackageAuthor = (p: PackageType[]) => {
  return p[0]?.authors
}
</script>

<style scoped>
table {
  margin-top: 3rem;

  & td {
    vertical-align: top;
  }
}
</style>
