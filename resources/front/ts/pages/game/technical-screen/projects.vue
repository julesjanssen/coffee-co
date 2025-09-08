<template>
  <div class="technical-screen projects">
    <ul class="projects-list">
      <li v-for="project in projects" :key="project.sqid" class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
        <span class="status">
          <strong :class="project.status.value">{{ project.status.label }}</strong>
        </span>

        <span v-if="project.status.value === 'active'" class="failure-chance"> </span>

        <span class="deadline">
          <template v-if="project.status.value === 'down'">
            <p>{{ $t('down since :round — maintenance required', { round: project.downRound!.displayFull }) }}</p>
            <p>{{ $t('end of contract: :round', { round: project.endOfContractRound!.displayFull }) }}</p>
          </template>

          <template v-if="project.status.value === 'won'">
            <p>
              {{
                $t('won in :round — deliver before :deliverRound', {
                  round: project.quoteRound!.displayFull,
                  deliverRound: project.deliverBeforeRound!.displayFull,
                })
              }}
            </p>
          </template>

          <template v-if="project.status.value === 'active'">
            <ProjectFailureChance :value="project.failureChance" />

            <p>{{ $t('end of contract: :round', { round: project.endOfContractRound!.displayFull }) }}</p>
          </template>

          <template v-if="project.status.value === 'finished'">
            <p>
              {{
                $t(':percentage% uptime & :bonus million uptime bonus', {
                  percentage: String(project.uptimePercentage),
                  bonus: String(project.uptimeBonus),
                })
              }}
            </p>
          </template>
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import ProjectFailureChance from '/@front:components/ProjectFailureChance.vue'
import GameLayout from '/@front:layouts/game.vue'
import type { Project } from '/@front:types/shared'

defineOptions({
  layout: [GameLayout],
})

defineProps<{
  projects: Project[]
}>()
</script>
