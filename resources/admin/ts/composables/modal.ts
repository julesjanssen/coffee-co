import { computed, onBeforeUnmount, reactive, watch } from 'vue'

const modalState = reactive<Map<string, boolean>>(new Map())

const showModalFn = (name: string) => {
  if (modalState.has(name)) {
    return
  }

  modalState.set(name, true)
}

const hideModalFn = (name: string) => {
  if (!modalState.has(name)) {
    return
  }

  modalState.delete(name)
}

watch(modalState, () => {
  document.body.classList.toggle('modal-open', modalState.size > 0)
})

type Options = {
  clickToClose?: boolean
  escToClose?: boolean
}

const handleEscForModal = (name: string) => {
  const handleKeyEvent = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
      hideModalFn(name)
    }
  }

  document.addEventListener('keydown', handleKeyEvent)

  onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeyEvent)
  })
}

export const createModal = (name: string, options: Options) => {
  const opts = Object.assign(
    {},
    {
      clickToClose: true,
      escToClose: true,
    },
    options,
  )

  if (opts.escToClose) {
    handleEscForModal(name)
  }

  const isModalShown = computed(() => !!modalState.get(name))

  return {
    showModal: () => showModalFn(name),
    hideModal: () => hideModalFn(name),
    isModalShown,
  }
}

export const useModal = (name: string) => {
  const isModalShown = computed(() => !!modalState.get(name))

  return {
    showModal: () => showModalFn(name),
    hideModal: () => hideModalFn(name),
    isModalShown,
  }
}
