<template>
  <main class="facilitator results">
    <section class="clients">
      <h3>NPS</h3>

      <ul class="clients-nps">
        <li v-for="client in clients" :key="client.sqid">
          <p>{{ client.title }}</p>
          <strong>{{ client.nps }}</strong>
        </li>
      </ul>
    </section>

    <section class="hdma">
      <h3>HDMA</h3>

      <p v-if="hdmaActive">HDMA is active</p>
      <p v-else>HDMA is not active</p>
    </section>

    <section class="marketing">
      <h3>Marketing</h3>

      <dl>
        <div>
          <dt>KPI</dt>
          <dd>{{ marketing.kpi }}</dd>
        </div>

        <div>
          <dt>treshold</dt>
          <dd>{{ marketing.treshold }}</dd>
        </div>
      </dl>
    </section>

    <section class="profit-loss">
      <h3>Profit &amp; loss</h3>

      <dl>
        <div>
          <dt>revenue</dt>
          <dd>
            <MoneyDisplay :value="profitLoss.revenue" />
          </dd>
        </div>
        <div>
          <dt>costs</dt>
          <dd>
            <MoneyDisplay :value="profitLoss.costs" />
          </dd>
        </div>

        <div>
          <dt>profit</dt>
          <dd>
            <strong><MoneyDisplay :value="profitLoss.profit" /></strong>
          </dd>
        </div>
      </dl>
    </section>

    <section class="investment-costs">
      <h3>Investment costs</h3>

      <dl>
        <div>
          <dt>total</dt>
          <dd>
            <MoneyDisplay :value="investmentCosts.total" />
          </dd>
        </div>
      </dl>
    </section>
  </main>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  clients: any[]
  hdmaActive: boolean
  marketing: Record<string, number>
  profitLoss: Record<string, number>
  investmentCosts: Record<string, number>
}>()
</script>
