import type { ErrorBag, Errors } from '@inertiajs/core'

// import type { NotificationType, RoundStatus, TeamStatus } from '/@front:shared/constants'

export type GameSession = {
  currentRound: {
    id: number
  }
  sse: {
    topic: string
  }
}

export type PageProps = {
  app: {
    env: string
    title: string
  }
  gameSession: GameSession
} & { errors: Errors & ErrorBag } & {
  notifications: Notification[]
}

export type Notification = {
  body: string
  duration: number
  title: string
  type: string
  fingerprint?: string
}

export type ServerEventData = any

export type ServerEvent = {
  event: string
  data: ServerEventData
}
