import type { ErrorBag, Errors } from '@inertiajs/core'

import type { GameSessionRoundStatusType, GameSessionStatusType } from '/@front:shared/constants'

type EnumObject = {
  value: string
  label: string
}

export type GameSession = {
  title: string
  roundStatus: {
    value: GameSessionRoundStatusType
    label: string
  }
  status: {
    value: GameSessionStatusType
    label: string
  }
  currentRound: {
    display: string
    isLastRoundOfYear: boolean
  }
  pausesAfterRound: boolean
  sse: {
    topic: string
  }
}

export type Project = any

export type ScenarioClient = {
  href: string
  title: string
}

export type NavigationItem = {
  label: string
  href: string
  disabled?: boolean
  icon?: string
}

export type PageProps = {
  app: {
    env: string
    title: string
    auth: {
      type: 'facilitator' | 'participant'
      sqid: string
      role?: EnumObject
    }
    navigation: NavigationItem[]
  }
  session: GameSession
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
