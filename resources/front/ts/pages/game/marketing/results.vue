<template>
  <div class="marketing results">
    <section class="summary">
      <dl class="panels">
        <div>
          <dt>{{ $t('campaigns total') }}</dt>
          <dd>
            <span class="value">{{ campaignStats.total }}</span>
          </dd>
        </div>

        <div>
          <dt>{{ $t('extraordinary campaigns') }}</dt>
          <dd>
            <span class="value">{{ campaignStats.difficult }}</span>
          </dd>
        </div>

        <div>
          <dt>{{ $t('HDMA pre-influence') }}</dt>
          <dd>
            <span class="value"> {{ hdmaActiveRounds }} <small>months</small> </span>
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
  </div>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'
import type { EnumObject } from '/@front:types/shared'

type InvestmentCost = {
  type: EnumObject
  value: number
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  hdmaActiveRounds: number
  campaignStats: Record<string, string>
  investmentCosts: InvestmentCost[]
}>()
</script>
