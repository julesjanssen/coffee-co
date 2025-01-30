import { reactive } from 'vue'

import { useModal } from '/@admin:composables/modal'

type DeleteConfirmAction = () => void | Promise<void>

type DeleteConfirmOptions = {
  action: DeleteConfirmAction
  icon?: string
  isModal?: boolean
}

type DeleteConfirmDetails = {
  modalName: string
  name: string | undefined
  icon: string
  action: DeleteConfirmAction
  cancelLabel?: string
  deleteLabel?: string
}

const modalName = `delete-confirm-${(Math.random() + 1).toString(36).substring(2, 6)}`

const details = reactive<DeleteConfirmDetails>({
  modalName,
  name: undefined,
  icon: 'trash',
  action: () => {},
})

const { showModal, hideModal } = useModal(modalName)

function deleteConfirm(name: string | DeleteConfirmAction, options?: DeleteConfirmOptions | DeleteConfirmAction): void {
  if (typeof name === 'function') {
    details.name = undefined
    details.action = name
  }

  if (typeof name === 'string') {
    details.name = name
  }

  if (options !== undefined) {
    if (typeof options === 'function') {
      details.action = options
    } else {
      if (options.icon) {
        details.icon = options.icon
      }

      details.action = options.action
    }
  }

  showModal()
}

export { deleteConfirm, details, hideModal }
