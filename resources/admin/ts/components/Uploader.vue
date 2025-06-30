<template>
  <label class="v-uploader">
    <input type="file" name="vfile" :accept="acceptAttribute" :multiple="multiple" @change="filesSelected" />
    <slot />
  </label>
</template>

<script setup lang="ts">
import { Upload } from 'tus-js-client'
import type { Ref } from 'vue'
import { computed, ref } from 'vue'

export type FileStatus = 'pending' | 'uploading' | 'completed'

export interface FileObject {
  file: File
  uploadID?: string
  name: string
  type: string
  size: number
  progress: number
  status: FileStatus
}

const props = withDefaults(
  defineProps<{
    endpoint?: string
    name?: string
    accept?: string
    multiple?: boolean
    parallel?: boolean
  }>(),
  {
    endpoint: '/upload',
    name: 'file',
    accept: '*/*',
    multiple: false,
    parallel: true,
  },
)

const emit = defineEmits(['fileAdded', 'fileSuccess', 'fileError'])

const acceptAttribute = computed(() => (['*', '*/*'].includes(props.accept) ? undefined : props.accept))

const filesSelected = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const files = target.files

  if (!files) {
    return
  }

  for (const userFile of files) {
    if (userFile.size === 0) {
      continue
    }

    const fileObject = ref<FileObject>({
      file: userFile,
      uploadID: undefined,
      name: userFile.name,
      type: userFile.type,
      size: userFile.size,
      progress: 0,
      status: 'pending',
    })

    emit('fileAdded', fileObject.value)

    if (props.parallel) {
      startUpload(fileObject)
    } else {
      await startUpload(fileObject)
    }
  }
}

const startUpload = (fileObject: Ref<FileObject>) => {
  return uploadFile(fileObject).then((uploadID: string) => {
    fileObject.value.uploadID = uploadID
  })
}

const uploadFile = (fileObject: Ref<FileObject>): Promise<string> => {
  return new Promise((resolve) => {
    const file = fileObject.value.file

    fileObject.value.status = 'uploading'

    const upload = new Upload(file, {
      endpoint: props.endpoint,
      chunkSize: 1.5 * 1024 * 1024,
      metadata: {
        filename: fileObject.value.name,
        type: fileObject.value.type,
      },
      retryDelays: [0, 1000, 3000, 8000],

      onSuccess: () => {
        const uploadID = getUploadIDFromUpload(upload)

        fileObject.value.progress = 100
        fileObject.value.uploadID = uploadID
        fileObject.value.status = 'completed'

        emit('fileSuccess', fileObject)

        resolve(uploadID)
      },

      onProgress: (sent: number, total: number) => {
        fileObject.value.progress = Math.round((sent / total) * 100)
      },
    })

    upload.start()
  })
}

const getUploadIDFromUpload = (upload: Upload): string => {
  if (!upload.url) {
    throw new Error(`Tus upload is missing "url" property`)
  }

  return upload.url.split('/').pop() as string
}
</script>

<style scoped>
label {
  display: inline-block;
  font-weight: normal;
}

input[type='file'] {
  position: absolute;
  overflow: hidden;
  width: 1px;
  height: 1px;
  clip: rect(0 0 0 0);
  clip-path: inset(50%);
  font-size: 1px;
  white-space: nowrap;
}
</style>
