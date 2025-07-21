import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

import { useNotification } from '/@front:composables/notification'
import { $t } from '/@front:shared/i18n'
import type { PageProps, ServerEvent, ServerEventData } from '/@front:types/shared'

// import { useGameSession } from './game-session'

const initialized = ref(false)

function getEventNameFromFQCN(className: string) {
  if (!className) {
    return
  }

  const lastIndex = className.lastIndexOf('\\')

  return lastIndex === -1 ? className : className.substring(lastIndex + 1)
}

function handleGameSessionRoundUpdated(eventData: ServerEventData) {
  const notify = useNotification()

  switch (eventData.roundStatus) {
    case 'finished':
      notify.success($t('Game finished'), {
        description: $t('The game is finished, well done!'),
      })

      router.visit('/game/leaderboard')
      break

    case 'leaderboard':
      router.visit('/game/leaderboard')
      break
  }
}

export function useServerSentEvents() {
  const page = usePage<PageProps>()

  const handleMessage = (messageEvent: MessageEvent<string>) => {
    const eventData = JSON.parse(messageEvent.data) as ServerEvent
    const eventType = getEventNameFromFQCN(eventData.event)

    switch (eventType) {
      case 'GameSessionRoundUpdated':
        handleGameSessionRoundUpdated(eventData.data)
        break
    }
  }

  const init = () => {
    const url = new URL(import.meta.env.VITE_MERCURE_URL)
    url.searchParams.append('topic', page.props.gameSession.sse.topic)

    const es = new EventSource(url)
    es.addEventListener('message', handleMessage)

    initialized.value = true
  }

  // Prevent re-initialisation on HMR.
  if (!initialized.value) {
    init()
  }
}
