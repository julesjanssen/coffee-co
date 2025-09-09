<template>
  <div class="technical-screen results">
    <dl class="panels">
      <div>
        <dt>{{ $t('uptime') }}</dt>
        <dd>
          <span class="value">
            <NumberDisplay :value="uptime" suffix="%" :digits="2" />
          </span>
        </dd>
      </div>
      <div>
        <dt>{{ $t('uptime bonus') }}</dt>
        <dd>
          <span class="value">
            <MoneyDisplay :value="uptimeBonus" />
          </span>
        </dd>
      </div>
    </dl>

    <div class="uptime-chart">
      <canvas ref="chart-canvas"></canvas>
    </div>
  </div>
</template>

<script setup lang="ts">
import Chart from 'chart.js/auto'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { onMounted, useTemplateRef } from 'vue'

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

const props = defineProps<{
  uptime: number
  uptimeBonus: number
  uptimePerClient: ClientUptime[]
}>()

const chartRef = useTemplateRef<HTMLCanvasElement>('chart-canvas')

const numberFormatter = new Intl.NumberFormat(document.documentElement.lang, {
  maximumFractionDigits: 1,
})

const dataLabelFormatter = (value: number | null) => {
  if (value === null) {
    return
  }

  return numberFormatter.format(value) + '%'
}

const initChart = () => {
  const element = chartRef.value as HTMLCanvasElement

  element.style.height = `${props.uptimePerClient.length * 60}px`

  new Chart(element, {
    type: 'bar',
    data: {
      labels: props.uptimePerClient.map((v) => v.client.title),
      datasets: [
        {
          data: props.uptimePerClient.map((v) => v.uptime),
          backgroundColor: '#00663a',
          borderWidth: 0,
          borderRadius: 4,
        },
      ],
    },
    plugins: [ChartDataLabels],
    options: {
      indexAxis: 'y',
      layout: {
        padding: {
          left: 20,
          right: 60,
          top: 20,
        },
      },
      datasets: {
        bar: {
          barPercentage: 0.2,
          categoryPercentage: 1.6,
        },
      },
      scales: {
        y: {
          grid: {
            display: false,
          },
          border: {
            display: false,
          },
        },
        x: {
          grid: {
            display: true,
          },
          ticks: {
            display: false,
          },
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          enabled: false,
        },
        datalabels: {
          color: '#222',
          anchor: 'end',
          align: 'end',
          formatter: dataLabelFormatter,
          font: {
            lineHeight: 1.2,
          },
        },
      },
    },
  })
}

onMounted(() => {
  initChart()
})
</script>
