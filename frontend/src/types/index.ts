export interface User {
  id: number
  name: string
  email: string
}

export interface AuthResponse {
  access_token: string
  token_type: string
  expires_in: number
  user?: User
}

export interface Station {
  id: string
  shortName: string
  longName: string
}

export interface Route {
  id: string
  fromStationId: string
  toStationId: string
  analyticCode: string
  distanceKm: number
  path: string[]
  createdAt: string
}

export interface RouteRequest {
  fromStationId: string
  toStationId: string
  analyticCode: string
}

export interface AnalyticDistance {
  analyticCode: string
  totalDistanceKm: number
  periodStart?: string
  periodEnd?: string
  group?: string
}

export interface AnalyticDistanceList {
  from: string | null
  to: string | null
  groupBy: 'day' | 'month' | 'year' | 'none'
  items: AnalyticDistance[]
}

export interface ApiError {
  code?: string
  message: string
  details?: string[]
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
