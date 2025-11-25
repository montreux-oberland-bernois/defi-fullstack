import api from './api'
import type { Station } from '@/types'

export const stationService = {
  async getAll(search?: string): Promise<Station[]> {
    const params = search ? { search } : {}
    const response = await api.get<{ data: Station[] }>('/stations', { params })
    return response.data.data
  },

  async getById(id: string): Promise<Station> {
    const response = await api.get<Station>(`/stations/${id}`)
    return response.data
  },
}
