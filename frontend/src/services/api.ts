import axios, { AxiosInstance } from 'axios'

const apiClient: AxiosInstance = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
  },
})

apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export interface RouteRequest {
  fromStationId: string
  toStationId: string
  analyticCode: string
}

export interface Route {
  id: number
  fromStationId: string
  toStationId: string
  analyticCode: string
  distanceKm: number
  path: string[]
  createdAt: string
}

export interface AnalyticDistance {
  analyticCode: string
  totalDistanceKm: number
  periodStart?: string
  periodEnd?: string
  group?: string
}

export interface AnalyticsResponse {
  from?: string
  to?: string
  groupBy: string
  items: AnalyticDistance[]
}

export interface Station {
  id: string
  shortName: string
  longName: string
}

export const createRoute = async (data: RouteRequest): Promise<Route> => {
  const response = await apiClient.post('/routes', data)
  return response.data
}

export const getAnalytics = async (
  from?: string,
  to?: string,
  groupBy?: string
): Promise<AnalyticsResponse> => {
  const params = new URLSearchParams()
  if (from) params.append('from', from)
  if (to) params.append('to', to)
  if (groupBy) params.append('groupBy', groupBy)

  const response = await apiClient.get('/stats/distances', { params })
  return response.data
}

export const getStations = async (): Promise<Station[]> => {
  const response = await apiClient.get('/stations')
  return response.data.stations
}
