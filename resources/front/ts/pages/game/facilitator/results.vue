<template>
  <div class="facilitator results">
    <section class="clients-nps">
      <header>
        <h2>{{ $t('Net promoter score') }}</h2>
      </header>

      <ul>
        <ClientNps v-for="client in clientsWithNps" :key="client.sqid" :client="client" />
      </ul>
    </section>

    <section class="hdma">
      <header>
        <h2>HDMA</h2>
      </header>

      <p v-if="hdmaActive" class="active">HDMA is active</p>
      <p v-else class="inactive">HDMA is not active</p>
    </section>

    <section class="marketing">
      <header>
        <h2>Marketing</h2>
      </header>

      <dl class="panels">
        <div>
          <dt>KPI</dt>
          <dd>
            <span class="value">{{ marketing.kpi }}</span>
          </dd>
        </div>

        <div>
          <dt>treshold</dt>
          <dd>
            <span class="value">{{ marketing.treshold }}</span>
          </dd>
        </div>
      </dl>
    </section>

    <section class="profit-loss">
      <header>
        <h2>Profit &amp; loss</h2>
      </header>

      <dl class="panels">
        <div>
          <dt>revenue</dt>
          <dd>
            <span class="value">
              <MoneyDisplay :value="profitLoss.revenue" />
            </span>
          </dd>
        </div>
        <div>
          <dt>costs</dt>
          <dd>
            <span class="value">
              <MoneyDisplay :value="profitLoss.costs" />
            </span>
          </dd>
        </div>

        <div>
          <dt>profit</dt>
          <dd>
            <strong class="value">
              <MoneyDisplay :value="profitLoss.profit" />
            </strong>
          </dd>
        </div>
      </dl>
    </section>

    <section class="investment-costs">
      <header>
        <h2>Investment costs</h2>
      </header>

      <dl class="panels">
        <div>
          <dt>total</dt>
          <dd>
            <span class="value">
              <MoneyDisplay :value="investmentCosts.total" />
            </span>
          </dd>
        </div>
      </dl>
    </section>
  </div>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import ClientNps from '/@front:components/results/ClientNps.vue'
import GameLayout from '/@front:layouts/game.vue'
import type { ScenarioClientWithNPS } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clientsWithNps: ScenarioClientWithNPS[]
  hdmaActive: boolean
  marketing: Record<string, number>
  profitLoss: Record<string, number>
  investmentCosts: Record<string, number>
}>()
</script>

<style scoped>
section.hdma {
  & p {
    display: flex;
    align-items: center;
    gap: 0.35em;

    &::before {
      --size: 0.8em;

      width: var(--size);
      height: var(--size);
      flex: 0 0 var(--size);
      border-radius: 50%;
      background: var(--green-600);
      content: '';
    }

    &.inactive::before {
      background: var(--red-500);
    }
  }
}
</style>
