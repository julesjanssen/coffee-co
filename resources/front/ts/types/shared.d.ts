import type { ErrorBag, Errors } from '@inertiajs/core'

import type { GameSessionRoundStatusType, GameSessionStatusType, ProjectStatusType } from '/@front:shared/constants'

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

export type Project = {
  sqid: string
  title: string
  client: ScenarioClient
  failureChance: number
  price: number
  labConsultingApplied: boolean
  labConsultingIncluded: boolean
  status: Omit<EnumObject, 'value'> & {
    value: ProjectStatusType
  }
}

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
