<template>
  <main class="facilitator projects">
    <table class="projects">
      <thead>
        <tr>
          <th>title</th>
          <th>status</th>
          <th class="align-right">price</th>
          <!-- <th class="align-right">uptime</th> -->
          <th class="align-right">requested</th>
          <th class="align-right">won</th>
          <th class="align-right">delivered</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="project in projects" :key="project.sqid">
          <td>{{ project.title }}</td>
          <td>{{ project.status.label }}</td>
          <td class="align-right">
            <MoneyDisplay :value="project.price" />
          </td>
          <td>{{ project.requestRound?.display ?? '-' }}</td>
          <td>{{ project.quoteRound?.display ?? '-' }}</td>
          <td>{{ project.deliveryRound?.display ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
  </main>
</template>

<script setup lang="ts">
import MoneyDisplay from '/@front:components/MoneyDisplay.vue'
import GameLayout from '/@front:layouts/game.vue'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: any[]
}>()
</script>
