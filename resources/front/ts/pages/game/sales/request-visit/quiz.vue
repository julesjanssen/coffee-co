<template>
  <header class="client">
    <h2>{{ client.title }}</h2>
  </header>

  <div v-if="isCompleted">
    <p>{{ $t('Quiz completed. Submitting answers') }}</p>
  </div>

  <div v-else-if="activeQuestion">
    <form @submit.prevent="confirmAnswer">
      <pre>{{ activeQuestion.question }}</pre>

      <ul class="answers">
        <li v-for="(option, index) in activeQuestion.options" :key="`option-${index}`">
          <label class="radio">
            <input v-model="selectedAnswer" type="radio" :value="index" />
            <span class="label">{{ option }}</span>
          </label>
        </li>
      </ul>

      <button type="submit" :disabled="hasSelectedAnswer">{{ $t('confirm') }}</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watchEffect } from 'vue'

import GameLayout from '/@front:layouts/game.vue'
import { $t } from '/@front:shared/i18n'
import type { ScenarioClient } from '/@front:types/shared'

type Question = {
  question: string
  options: Record<string, string>
}

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  client: ScenarioClient
  questions: Record<string, Question>
}>()

const form = useForm({
  answers: {},
})

const isCompleted = ref(false)
const activeQuestionID = ref(0)
const selectedAnswer = ref<string>('')
const answers = ref<Record<string, string>>({})

const activeQuestionKey = computed(() => Object.keys(props.questions).at(activeQuestionID.value))
const activeQuestion = computed(() => (activeQuestionKey.value ? props.questions[activeQuestionKey.value] : undefined))
const hasSelectedAnswer = computed(() => !selectedAnswer.value)

const confirmAnswer = () => {
  if (!activeQuestionKey.value) {
    return
  }

  answers.value[activeQuestionKey.value] = selectedAnswer.value

  selectedAnswer.value = ''
  if (Object.keys(props.questions).length > activeQuestionID.value + 1) {
    activeQuestionID.value = activeQuestionID.value + 1
  } else {
    isCompleted.value = true
  }
}

watchEffect(() => {
  if (isCompleted.value === true) {
    form.answers = { ...answers.value }

    form.post(location.pathname)
    // console.log('adsyf98asf8asdf98asdf')
    // http.post(location.pathname, { answers: answers.value })
  }
})
</script>
