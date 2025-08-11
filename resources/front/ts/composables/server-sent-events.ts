import { router, usePage } from '@inertiajs/vue3'
import { onUnmounted, ref } from 'vue'

import type { GameSessionRoundStatusType } from '/@front:shared/constants'
import type { PageProps, ServerEvent, ServerEventData } from '/@front:types/shared'

const initialized = ref(false)

function getEventNameFromFQCN(className: string) {
  if (!className) {
    return
  }

  const lastIndex = className.lastIndexOf('\\')

  return lastIndex === -1 ? className : className.substring(lastIndex + 1)
}

function handleGameSessionRoundStatusUpdated(eventData: ServerEventData) {
  const roundStatus: GameSessionRoundStatusType = eventData.roundStatus as GameSessionRoundStatusType
  switch (roundStatus) {
    case 'active':
    case 'paused':
      router.reload()
      break
  }
}

export function useServerSentEvents() {
  const page = usePage<PageProps>()
  let eventSource: EventSource | undefined

  const handleMessage = (messageEvent: MessageEvent<string>) => {
    const eventData = JSON.parse(messageEvent.data) as ServerEvent
    const eventType = getEventNameFromFQCN(eventData.event)

    switch (eventType) {
      case 'GameSessionRoundStatusUpdated':
        handleGameSessionRoundStatusUpdated(eventData.data)
        break
    }
  }

  const init = () => {
    const url = new URL(import.meta.env.VITE_MERCURE_URL)
    url.searchParams.append('topic', page.props.session.sse.topic)

    eventSource = new EventSource(url)
    eventSource.addEventListener('message', handleMessage)

    initialized.value = true
  }

  const close = () => {
    if (eventSource) {
      eventSource.close()
      eventSource = undefined
    }

    initialized.value = false
  }

  // Prevent re-initialisation on HMR.
  if (!initialized.value) {
    init()
  }

  onUnmounted(() => {
    close()
  })

  return {
    close,
  }
}
