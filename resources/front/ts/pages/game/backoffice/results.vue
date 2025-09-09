<template>
  <div class="backoffice results">
    <section class="summary">
      <dl class="panels">
        <div>
          <dt>{{ $t('offers submitted') }}</dt>
          <dd>
            <span class="value">{{ projectsQuotedCount }}</span>
          </dd>
        </div>

        <div>
          <dt>{{ $t('revenue') }}</dt>
          <dd>
            <span class="value">
              <MoneyDisplay :value="revenue" />
            </span>
          </dd>
        </div>

        <div>
          <dt>{{ $t('profit') }}</dt>
          <dd>
            <span class="value">
              <MoneyDisplay :value="profit" />
            </span>
          </dd>
        </div>
      </dl>
    </section>

    <section class="investment-costs">
      <header>
        <h2>{{ $t('Investment costs') }}</h2>
      </header>

      <ul>
        <li v-for="item in investmentCosts" :key="item.type.value">
          <span class="type">{{ item.type.label }}</span>
          <span class="value">
            <MoneyDisplay :value="item.value" />
          </span>
        </li>
      </ul>
    </section>

    <section class="clients-nps">
      <header>
        <h2>{{ $t('Net promoter score') }}</h2>
      </header>

      <ul>
        <ClientNps v-for="client in npsPerClient" :key="client.sqid" :client="client" />
      </ul>
    </section>
  </div>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import ClientNps from '/@front:components/results/ClientNps.vue'
import GameLayout from '/@front:layouts/game.vue'
import type { EnumObject, ScenarioClientWithNPS } from '/@front:types/shared'

type InvestmentCost = {
  type: EnumObject
  value: number
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projectsQuotedCount: number
  revenue: number
  profit: number
  investmentCosts: InvestmentCost[]
  npsPerClient: ScenarioClientWithNPS[]
}>()
</script>
