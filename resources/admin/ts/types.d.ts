import type { ErrorBag, Errors } from '@inertiajs/core'

export type PageProps = {
  app: {
    env: string
    title: string
    navigation: NavigationItem[]
    account: any
  }
} & { errors: Errors & ErrorBag }

export type NavigationItem = {
  title: string
  items: {
    href: string
    active: boolean
    title: string
    icon?: string
  }[]
}

export type PaginatedData<T> = {
  data: T[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number
    path: string
    per_page: number
    to: number
  }
}

export type Wrapped<T> = {
  data: T
}

type Authorizable = {
  can: {
    [key: string]: boolean
  }
}

type SoftDeletable = {
  trashed: boolean
}

export type Tenant = {
  sqid: string
  name: string
  isCurrent: boolean
  avatar?: { url: string }
  links: Record<string, string>
}

export type UserRole = {
  name: string
  title: string
  description: string
}

export type User = {
  sqid: string
  name: string
  email: string
  avatar: {
    url: string
  }
  roles: UserRole[]
  links: Record<string, string>
}

export type Login = {
  sqid: string
  authenticatable: {
    type: string
    name: string
  }
  createdAt: string
  ip: {
    value: string
    bogon: boolean
    countryCode?: string
    countryFlag?: string
    countryFlagImage?: string
    organization?: string
    geoLocation?: Geolocation
    timezone?: Geolocation
  }
  userAgent: {
    value: string
    deviceTypeIcon: string
    clientFamily: string
    clientVersion: string
    osName: string
    osVersion: string
    isBot: boolean
  }
  links: Record<string, string>
}
