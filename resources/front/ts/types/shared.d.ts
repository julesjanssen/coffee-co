import type { ErrorBag, Errors } from '@inertiajs/core'

import type { GameSessionRoundStatusType, GameSessionStatusType, ProjectStatusType } from '/@front:shared/constants'

type EnumObject = {
  value: string
  label: string
}

export type GameRound = {
  id: number
  display: string
  displayFull: string
  isLastRoundOfYear: boolean
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
  currentRound: Gameround
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
  uptimePercentage: number
  uptimeBonus: number
  price: number
  labConsultingApplied: boolean
  labConsultingIncluded: boolean
  status: Omit<EnumObject, 'value'> & {
    value: ProjectStatusType
  }
  requestRound?: GameRound
  quoteBeforeRound?: GameRound
  quoteRound?: GameRound
  deliverBeforeRound?: GameRound
  downRound?: GameRound
  endOfContractRound?: GameRound
}

export type ScenarioClient = {
  sqid: string
  href: string
  title: string
}

export type NavigationItem = {
  label: string
  href: string
  disabled?: boolean
  icon?: string
  isActive: boolean
}

export type PageProps = {
  app: {
    env: string
    title: string
    auth: {
      type: 'facilitator' | 'participant'
      sqid: string
      role: EnumObject & {
        mainRoute: string
      }
      activeDuringBreak: boolean
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
