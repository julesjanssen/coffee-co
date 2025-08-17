import { markRaw } from 'vue'
import { type ExternalToast, toast } from 'vue-sonner'

import Notification from '/@front:components/Notification.vue'

type NotificationProps = InstanceType<typeof Notification>['$props']
type NotificationOptions = Omit<ExternalToast, 'componentOptions' | 'style' | 'position'>

const add = async (props: NotificationProps, options: NotificationOptions = {}) => {
  return toast.custom(markRaw(Notification), {
    componentProps: props,
    ...options,
    position: 'bottom-center',
  })
}

export const success = async (
  title: string,
  props: Omit<NotificationProps, 'type' | 'title'> = {},
  options: NotificationOptions = {},
) => {
  return await add({ ...props, title, type: 'success' }, options)
}

export const error = async (
  title: string,
  props: Omit<NotificationProps, 'type' | 'title'> = {},
  options: NotificationOptions = {},
) => {
  return await add({ ...props, title, type: 'error' }, options)
}
