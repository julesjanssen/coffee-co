<template>
  <section>
    <header>
      <div>
        <h1>Server load</h1>
      </div>

      <div class="actions">
        <span class="badge">CPU count: {{ data.cpuCount }}</span>
      </div>
    </header>

    <div id="server-load-chart"></div>
  </section>
</template>

<script lang="ts">
declare const LightweightCharts: any
</script>

<script lang="ts" setup>
import { nextTick, onMounted } from 'vue'

import { loadScript } from '/@admin:shared/utils'

type LoadDataResult = {
  timestamp: string
  values: number[]
}

type LoadData = {
  cpuCount: number
  results: LoadDataResult[]
}

const props = defineProps<{
  data: LoadData
}>()

const initChart = async () => {
  await loadScript('https://unpkg.com/lightweight-charts@4.0.0/dist/lightweight-charts.standalone.production.js')

  await nextTick()

  const element = document.querySelector('#server-load-chart') as HTMLElement

  const chart = LightweightCharts.createChart(element, { width: element.offsetWidth, height: 240 })

  chart.applyOptions({
    timeScale: {
      borderVisible: false,
      visible: true,
      timeVisible: true,
      secondsVisible: false,
    },
    layout: {
      textColor: '#3f405f',
      fontSize: 12,
      fontFamily: 'sans-serif',
    },
  })

  let series = [
    {
      title: '1 minute',
      index: 0,
      color: '#6684F1',
    },
    // {
    //   title: '5 minutes',
    //   index: 1,
    //   color: '#546BDD',
    // },
    {
      title: '15 minutes',
      index: 2,
      color: '#273DC5',
    },
  ]

  series.map((serie: any) => {
    const lineSeries = chart.addLineSeries({
      baseLineVisible: false,
      color: serie.color,
      title: serie.title,
      lineWidth: 2,
    })

    const sData = props.data.results.map((result: LoadDataResult) => {
      return {
        time: new Date(result.timestamp).getTime() / 1000,
        value: result.values[serie.index],
      }
    })

    lineSeries.setData(sData)

    return lineSeries
  })
}

onMounted(() => {
  initChart()
})
</script>
