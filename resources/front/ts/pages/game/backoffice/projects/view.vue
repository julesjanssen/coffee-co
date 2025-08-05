<template>
  <header class="project">
    <h2>{{ project.title }}</h2>
    <h3>{{ project.client.title }}</h3>
  </header>

  <form @submit.prevent="submitOffer">
    <fieldset>
      <button v-if="canAddProduct" type="button" class="add-product" @click.prevent="addProduct">
        <Icon name="plus" />
      </button>

      <div v-for="(p, index) in form.products" :key="index" class="field product">
        <label>{{ $t('product :number', { number: String(index + 1) }) }}</label>
        <div>
          <input
            v-model="form.products[index]"
            type="text"
            inputmode="numeric"
            maxlength="3"
            @change="productLookup(index)"
          />
          <span class="nane">
            {{ productNames[index] }}
          </span>
        </div>
      </div>
      <FormError :error="form.errors.products" />
    </fieldset>

    <fieldset>
      <div class="field">
        <label>{{ $t('price') }}</label>
        <div><input v-model="form.price" type="numeric" min="1" /> M</div>
      </div>
    </fieldset>

    <fieldset class="actions">
      <button type="submit">{{ $t('submit offer') }}</button>
    </fieldset>
  </form>

  <template v-if="solutions && solutions.length">
    <h3>solutions</h3>
    <div v-for="solution in solutions" :key="solution.products">
      {{ solution.products }}
    </div>
  </template>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watchEffect } from 'vue'

import FormError from '/@front:components/FormError.vue'
import Icon from '/@front:components/Icon.vue'
import GameLayout from '/@front:layouts/game.vue'
import { http } from '/@front:shared/http'
import { $t } from '/@front:shared/i18n'

defineOptions({
  layout: [GameLayout],
})

const props = defineProps<{
  project: any
  links: Record<string, string>
  solutions?: any[]
}>()

const form = useForm<{
  products: string[]
  price: number
}>({
  products: [],
  price: props.project.price,
})

const productNames = ref<Record<number, string>>({})
const canAddProduct = computed(() => form.products.length < 3)

const addProduct = () => {
  form.products.push('')
}

const productLookup = (index: number) => {
  const value = form.products[index]?.trim() ?? ''
  productNames.value[index] = ''

  if (value.length > 0) {
    const url = props.links['products.view'].replace('XXX', value)
    http
      .get(url)
      .then((response) => {
        const data = response.data
        if (data && data.title) {
          productNames.value[index] = `${data.title} (${data.id})`
        } else {
          productNames.value[index] = $t('Unknown product')
        }
      })
      .catch(() => {
        productNames.value[index] = $t('Unknown product')
      })
  }
}

const submitOffer = () => {
  form.post(location.pathname)
}

watchEffect(() => {
  if (form.products.length === 0) {
    form.products.push('')
  }
})
</script>
