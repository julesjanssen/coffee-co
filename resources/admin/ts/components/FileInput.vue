<template>
  <Uploader :multiple="multiple" :accept="accept" @file-added="fileAdded">
    <span class="input-display">
      <Icon :name="icon" />
      <span class="label truncate">{{ label }}</span>

      <Transition name="icon-fade" mode="out-in">
        <span v-if="isUploading" class="progress">
          <ProgressCircle :percentage="progress" :size="14" />
        </span>
        <span v-else-if="isDone" key="icon" class="status">
          <Icon name="check" />
        </span>
      </Transition>
    </span>
  </Uploader>
</template>

<script lang="ts" setup>
import type { Ref } from 'vue'
import { computed, ref, unref, watch } from 'vue'

import type { FileObject } from '/@admin:components/Uploader.vue'
import { $t } from '/@admin:shared/i18n'

import Icon from './Icon.vue'
import ProgressCircle from './ProgressCircle.vue'
import Uploader from './Uploader.vue'

type FileValue = {
  name: string
  id: string
}

const files = ref<Ref<FileObject>[]>([])

const model = defineModel<FileValue[]>()

const props = withDefaults(
  defineProps<{
    accept?: string
    multiple?: boolean
  }>(),
  {
    accept: '*/*',
    multiple: false,
  },
)

const fileCount = computed(() => files.value.length)

const completedFiles = computed(() => {
  return files.value.filter((f) => unref(f).status === 'completed')
})

const progress = computed(() => {
  if (fileCount.value === 0) {
    return 100
  }

  const uploadingFiles = files.value.filter((file) => unref(file).status === 'uploading')

  if (uploadingFiles.length === 0) {
    return 100
  }

  const total = uploadingFiles.reduce((c, file) => c + unref(file).progress, 0)

  return Math.round(total / uploadingFiles.length)
})

const isUploading = computed(() => {
  if (fileCount.value === 0) {
    return false
  }

  return files.value.some((file) => unref(file).status === 'uploading')
})

const isDone = computed(() => !isUploading.value && fileCount.value > 0)

const icon = computed(() => {
  if (fileCount.value > 0) {
    return 'file-up'
  }

  return 'upload'
})

const label = computed(() => {
  if (model.value) {
    if (model.value.length > 1) {
      return $t(':num files', { num: String(model.value.length) })
    } else if (model.value.length === 1) {
      return model.value[0].name
    }
  }

  if (fileCount.value > 0) {
    if (fileCount.value > 1) {
      return $t(':num files', { num: String(fileCount.value) })
    } else {
      return unref(files.value[0]).name
    }
  }

  if (props.multiple) {
    return $t('select files')
  }

  return $t('select file')
})

const fileAdded = (fileObject: Ref<FileObject>) => {
  if (props.multiple) {
    files.value.push(fileObject)
  } else {
    files.value = [fileObject]
  }
}

watch(
  () => completedFiles.value,
  () => {
    const fileValues: FileValue[] = completedFiles.value.map((f) => {
      const file = unref(f)

      return {
        id: file.uploadID as string,
        name: file.name,
      }
    })

    model.value = fileValues
  },
  { deep: true },
)

// Clear internal files when model value is cleared externally
watch(
  () => model.value,
  (newValue, oldValue) => {
    if ((!newValue || newValue.length === 0) && oldValue && oldValue.length > 0) {
      files.value = []
    }
  },
)
</script>

<style scoped>
.v-uploader {
  display: block;
}

.input-display {
  display: flex;
  align-items: center;
  cursor: pointer;
  gap: 0.5em;

  & .label {
    overflow: hidden;
    min-width: 0;
    flex: 1;
  }

  & .progress,
  & .status {
    flex: 0 0 1em;
  }
}

.icon-fade-enter-active,
.icon-fade-leave-active {
  transition: opacity 0.3s ease;
}

.icon-fade-enter-from,
.icon-fade-leave-to {
  opacity: 0;
}
</style>
