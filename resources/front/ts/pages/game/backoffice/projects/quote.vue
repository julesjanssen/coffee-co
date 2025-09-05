<template>
  <div class="backoffice project quote">
    <header class="project-display">
      <div class="project">
        <span class="title">{{ project.title }}</span>
        <span class="client">{{ project.client.title }}</span>
      </div>
    </header>

    <form @submit.prevent="submitOffer">
      <fieldset>
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
          </div>
          <span class="product-name">
            {{ productNames[index] }}
          </span>

          <div class="add-product">
            <button v-if="canAddProduct && index === 0" type="button" @click.prevent="addProduct">
              <Icon name="plus" />
            </button>
          </div>
        </div>
        <FormError :error="form.errors.products" />
      </fieldset>

      <fieldset>
        <div class="field price">
          <label>{{ $t('price') }}</label>
          <div data-suffix="in millions"><input v-model="form.price" type="text" inputmode="numeric" /></div>
        </div>
      </fieldset>

      <fieldset class="actions">
        <button type="submit" :disabled="form.processing || !canSubmit">{{ $t('submit offer') }}</button>
      </fieldset>
    </form>

    <template v-if="solutions && solutions.length">
      <div v-for="solution in solutions" :key="solution.products">
        {{ solution.products }}
      </div>
    </template>
  </div>
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
const productIDs = ref<Record<number, string>>({})

const canAddProduct = computed(() => form.products.length < 3)
const canSubmit = computed(() => {
  const productIDsJoin = Object.values(productIDs.value).join('')
  if (productIDsJoin.length === 0) {
    return false
  }

  if (form.price === 0) {
    return false
  }

  return true
})

const addProduct = () => {
  form.products.push('')
}

const productLookup = (index: number) => {
  const value = form.products[index]?.trim() ?? ''
  productNames.value[index] = ''
  productIDs.value[index] = ''

  if (value.length > 0) {
    const url = props.links['products.view'].replace('XXX', value)
    http
      .get(url)
      .then((response) => {
        const data = response.data
        if (data && data.title) {
          productNames.value[index] = `${data.title} (${data.id})`
          productIDs.value[index] = data.id
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

<style src="/@front:css/views/backoffice.project.css"></style>
