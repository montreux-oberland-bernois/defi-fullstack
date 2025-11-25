import api from './api'
import type { AnalyticDistanceList } from '@/types'

export interface StatsParams {
  from?: string
  to?: string
  groupBy?: 'day' | 'month' | 'year' | 'none'
}

export interface DashboardDistribution {
  code: string
  count: number
  distance: number
}

export interface DashboardStats {
  month: string
  routesThisMonth: number
  distanceThisMonth: number
  totalRoutes: number
  totalDistance: number
  distribution: DashboardDistribution[]
}

export const statsService = {
  async getDashboard(): Promise<DashboardStats> {
    const response = await api.get<DashboardStats>('/stats/dashboard')
    return response.data
  },

  async getDistances(params: StatsParams = {}): Promise<AnalyticDistanceList> {
    const response = await api.get<AnalyticDistanceList>('/stats/distances', { params })
    return response.data
  },
}
