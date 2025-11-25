import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import { authService, type LoginCredentials, type RegisterData } from '@/services/authService'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<User | null>(
    localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')!) : null
  )
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  const setAuth = (newToken: string, newUser: User | null = null) => {
    token.value = newToken
    localStorage.setItem('token', newToken)
    if (newUser) {
      user.value = newUser
      localStorage.setItem('user', JSON.stringify(newUser))
    }
  }

  const clearAuth = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    error.value = null
    try {
      const response = await authService.login(credentials)
      setAuth(response.access_token)
      // Fetch user info after login
      const userInfo = await authService.me()
      user.value = userInfo
      localStorage.setItem('user', JSON.stringify(userInfo))
      return true
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError.response?.data?.message ?? 'Erreur de connexion'
      return false
    } finally {
      loading.value = false
    }
  }

  const register = async (data: RegisterData) => {
    loading.value = true
    error.value = null
    try {
      const response = await authService.register(data)
      if (response.user) {
        setAuth(response.access_token, response.user)
      } else {
        setAuth(response.access_token)
        const userInfo = await authService.me()
        user.value = userInfo
        localStorage.setItem('user', JSON.stringify(userInfo))
      }
      return true
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string; details?: string[] } } }
      error.value = axiosError.response?.data?.details?.join(', ') ??
                   axiosError.response?.data?.message ??
                   "Erreur d'inscription"
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true
    try {
      await authService.logout()
    } catch {
      // Ignore errors on logout
    } finally {
      clearAuth()
      loading.value = false
    }
  }

  const checkAuth = async () => {
    if (!token.value) return false
    try {
      const userInfo = await authService.me()
      user.value = userInfo
      localStorage.setItem('user', JSON.stringify(userInfo))
      return true
    } catch {
      clearAuth()
      return false
    }
  }

  return {
    token,
    user,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    checkAuth,
  }
})
