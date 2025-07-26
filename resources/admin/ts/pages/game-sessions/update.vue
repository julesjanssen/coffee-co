<template>
  <Head :title="session.sqid ? $t('update :t', { t: session.title }) : $t('create new session')" />

  <main class="game-sessions update">
    <section>
      <header>
        <div>
          <h2>{{ $t('Game sessions') }}</h2>
          <h1>{{ session.sqid ? session.title : $t('create new session') }}</h1>
        </div>
      </header>

      <form @submit.prevent="submitForm">
        <fieldset>
          <div class="field">
            <label>scenario</label>
            <div>
              <select v-model="form.scenario" name="scenario">
                <option v-for="scenario in scenarios" :key="scenario.sqid" :value="scenario.sqid">
                  {{ scenario.title }}
                </option>
              </select>
            </div>
          </div>

          <div class="field">
            <label>{{ $t('title') }}</label>
            <div>
              <input v-model="form.title" type="text" name="title" />
              <FormError :error="form.errors.title" />
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="submit" class="success" :disabled="form.processing">
              <Icon name="plus" /> {{ $t('save') }}
            </button>
          </fieldset>
        </footer>
      </form>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { $t } from '/@admin:shared/i18n'

type GameSession = any

defineOptions({
  layout: [AuthLayout],
})

const props = defineProps<{
  session: GameSession
  scenarios: any[]
}>()

const form = useForm({
  scenario: props.session.scenario?.sqid || '',
  title: props.session.title,
})

const submitForm = () => {
  const exists = !!props.session.sqid

  form.post(location.pathname, {
    only: ['session'],
    onSuccess: () => {
      if (!exists) {
        toast.success($t('Item added.'))
      } else {
        toast.success($t('Item saved.'))
      }
    },
  })
}
</script>
