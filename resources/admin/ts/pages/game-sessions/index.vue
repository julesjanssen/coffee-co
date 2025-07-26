<template>
  <Head :title="$t('Game sessions')" />

  <main class="game-sessions index">
    <section>
      <header>
        <div>
          <h1>{{ $t('Game sessions') }}</h1>
        </div>

        <div class="actions">
          <Link v-if="results.can.create" class="button" :href="results.links.create">
            <Icon name="plus" />
            add session
          </Link>
        </div>
      </header>

      <div class="table-wrapper">
        <div>
          <table class="fixed">
            <thead>
              <tr>
                <th>name</th>
                <th>status</th>
                <th class="align-right">current round</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(result, index) in results.data" :key="index" :class="{ trashed: result.trashed }">
                <td>
                  <Link :href="result.links.view">{{ result.title }}</Link>
                </td>
                <td>
                  <span class="badge" :class="`status-${result.status.value}`">
                    {{ result.status.label }}
                  </span>
                </td>
                <td class="align-right">
                  <span v-if="result.currentRound">{{ result.currentRound.display }}</span>
                  <span v-else>-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Pagination :links="results.links" :only="['results']" />
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'

import Icon from '/@admin:components/Icon.vue'
import Pagination from '/@admin:components/Pagination.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import type { Authorizable, PaginatedData, SoftDeletable } from '/@admin:types'

type GameSession = any

defineOptions({
  layout: [AuthLayout],
})

defineProps<{
  results: PaginatedData<Authorizable & SoftDeletable & GameSession> & {
    links: Record<string, string>
    can: Record<string, string>
  }
}>()
</script>

<!-- <style src="/@admin:css/views/game-sessions.index.css"></style> -->
