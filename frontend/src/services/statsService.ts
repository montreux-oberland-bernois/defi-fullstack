import api from './api'
import type { AnalyticDistanceList } from '@/types'

export interface StatsParams {
  from?: string
  to?: string
  groupBy?: 'day' | 'month' | 'year' | 'none'
}

export const statsService = {
  async getDistances(params: StatsParams = {}): Promise<AnalyticDistanceList> {
    const response = await api.get<AnalyticDistanceList>('/stats/distances', { params })
    return response.data
  },
}
