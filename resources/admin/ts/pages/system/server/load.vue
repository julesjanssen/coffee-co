<template>
  <section>
    <header>
      <div>
        <h1>Server load</h1>
      </div>

      <div class="actions">
        <span class="badge">CPU cores: {{ data.cpuCount }}</span>
      </div>
    </header>

    <div v-if="data.results.length === 0" class="empty">
      <p>No server load data available.</p>
    </div>
    <div v-else id="server-load-chart"></div>
  </section>
</template>

<script lang="ts" setup>
import type { UTCTimestamp } from 'lightweight-charts'
import { nextTick, onMounted } from 'vue'

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

const timeToLocal = (originalTime: string) => {
  const d = new Date(originalTime)
  return (
    Date.UTC(
      d.getFullYear(),
      d.getMonth(),
      d.getDate(),
      d.getHours(),
      d.getMinutes(),
      d.getSeconds(),
      d.getMilliseconds(),
    ) / 1000
  )
}

const initChart = async () => {
  const { createChart } = await import('lightweight-charts')

  await nextTick()

  const element = document.querySelector('#server-load-chart') as HTMLElement

  const chart = createChart(element, { width: element.offsetWidth, height: 240 })

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
        time: timeToLocal(result.timestamp) as UTCTimestamp,
        value: result.values[serie.index],
      }
    })

    lineSeries.setData(sData)

    return lineSeries
  })
}

onMounted(() => {
  if (props.data.results.length === 0) {
    return
  }

  initChart()
})
</script>
