import api from './api'
import type { Route, RouteRequest, PaginatedResponse } from '@/types'

export const routeService = {
  async calculate(request: RouteRequest): Promise<Route> {
    const response = await api.post<Route>('/routes', request)
    return response.data
  },

  async getAll(page = 1, perPage = 15): Promise<PaginatedResponse<Route>> {
    const response = await api.get<PaginatedResponse<Route>>('/routes', {
      params: { page, per_page: perPage }
    })
    return response.data
  },

  async getById(id: string): Promise<Route> {
    const response = await api.get<Route>(`/routes/${id}`)
    return response.data
  },
}
