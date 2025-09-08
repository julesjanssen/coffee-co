<template>
  <div class="sales-screen results">
    <section class="summary">
      <dl class="panels">
        <div>
          <dt>{{ $t('requests picked up') }}</dt>
          <dd>
            <span class="value">{{ projectsPickedup }}</span>
          </dd>
        </div>
        <div>
          <dt>{{ $t('deals won') }}</dt>
          <dd>
            <span class="value">{{ projectsWon }}</span>
          </dd>
        </div>
        <div>
          <dt>{{ $t('investment costs') }}</dt>
          <dd>
            <span class="value"><MoneyDisplay :value="investmentCost" /></span>
          </dd>
        </div>
      </dl>
    </section>

    <section class="clients-nps">
      <header>
        <h2>{{ $t('Net promoter score') }}</h2>
      </header>

      <ul>
        <ClientNps v-for="client in clients" :key="client.sqid" :client="client" />
      </ul>
    </section>
  </div>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import ClientNps from '/@front:components/results/ClientNps.vue'
import GameLayout from '/@front:layouts/game.vue'

type Client = {
  sqid: string
  title: string
  nps: number
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projectsPickedup: number
  projectsWon: number
  investmentCost: number
  clients: Client[]
}>()
</script>
