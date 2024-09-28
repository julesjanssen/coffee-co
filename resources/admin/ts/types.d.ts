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

export type Client = {
  title: string
}

export type Project = {
  hash: string
  title: string
  client: Client
  links: Record<string, string>
}

export type Issue = {
  hash: string
  title: string
}
