import api from './api'
import type { AuthResponse, User } from '@/types'

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/login', credentials)
    return response.data
  },

  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', data)
    return response.data
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout')
  },

  async me(): Promise<User> {
    const response = await api.get<User>('/auth/me')
    return response.data
  },

  async refresh(): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/refresh')
    return response.data
  },
}
