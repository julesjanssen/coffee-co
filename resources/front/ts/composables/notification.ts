import { markRaw, nextTick } from 'vue'
import { type ExternalToast, toast } from 'vue-sonner'

import Notification from '/@front:components/Notification.vue'

type NotificationProps = InstanceType<typeof Notification>['$props']
type NotificationOptions = Omit<ExternalToast, 'componentOptions' | 'style' | 'position'>

export function useNotification() {
  const add = async (props: NotificationProps, options: NotificationOptions = {}) => {
    await nextTick()

    return toast.custom(markRaw(Notification), {
      componentProps: props,
      ...options,
      position: 'bottom-center',
    })
  }

  const addPersistent = (props: NotificationProps, options: NotificationOptions = {}) => {
    return add(props, {
      ...options,
      duration: Infinity,
    })
  }

  const success = async (
    title: string,
    props: Omit<NotificationProps, 'type' | 'title'> = {},
    options: NotificationOptions = {},
  ) => {
    await nextTick()

    return add({ ...props, title, type: 'success' }, options)
  }

  const error = async (
    title: string,
    props: Omit<NotificationProps, 'type' | 'title'> = {},
    options: NotificationOptions = {},
  ) => {
    await nextTick()

    return add({ ...props, title, type: 'error' }, options)
  }

  return {
    add,
    addPersistent,
    success,
    error,
  }
}
