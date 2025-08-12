<template>
  <header>
    <h2>{{ $t('results') }}</h2>
  </header>

  <dl>
    <div>
      <dt>{{ $t('uptime') }}</dt>
      <dd>
        <NumberDisplay :value="uptime" suffix="%" :digits="2" />
      </dd>
    </div>
    <div>
      <dt>{{ $t('uptime bonus') }}</dt>
      <dd><MoneyDisplay :value="uptimeBonus" /></dd>
    </div>
  </dl>

  <div>
    <ul class="clients-uptime">
      <li v-for="item in uptimePerClient" :key="item.client.sqid">
        <p>{{ item.client.title }}</p>
        <NumberDisplay :value="item.uptime" suffix="%" :digits="2" />
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import NumberDisplay from '/@front:components/NumberDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'
import type { ScenarioClient } from '/@front:types/shared'

type ClientUptime = {
  uptime: number
  client: ScenarioClient
}

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  uptime: number
  uptimeBonus: number
  uptimePerClient: ClientUptime[]
}>()
</script>
