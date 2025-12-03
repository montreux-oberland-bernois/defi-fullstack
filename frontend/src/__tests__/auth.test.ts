import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import axios from 'axios'
import Auth from '../pages/AuthPage.vue'

// Mock axios
vi.mock('axios', () => ({
  default: {
    post: vi.fn(),
    create: vi.fn(() => ({
      post: vi.fn(),
      interceptors: { request: { use: vi.fn() }, response: { use: vi.fn() } },
    })),
  },
}))

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: vi.fn(() => ({ push: vi.fn() })),
}))

describe('AuthPage (API Logic)', () => {
  const postMock = axios.post as ReturnType<typeof vi.fn>
  beforeEach(() => {
    postMock.mockReset()
    vi.clearAllMocks()
    localStorage.clear()
  })

  it('registers new user successfully', async () => {
    postMock.mockResolvedValueOnce({ status: 201 })

    const wrapper = mount(Auth, {
      global: {
        stubs: {
          VContainer: true,
          VCard: true,
          VCardTitle: true,
          VCardText: true,
          VForm: true,
          VTextField: true,
          VRow: true,
          VCol: true,
          VBtn: true,
          VAlert: true,
        },
      },
    })

    // Set refs + call methods
    wrapper.vm.username = 'newuser'
    wrapper.vm.password = 'pass123'
    await wrapper.vm.onRegister()

    expect(postMock).toHaveBeenCalledWith('/api/v1/auth/register', {
      username: 'newuser',
      password: 'pass123',
    })
  })

  it('logs in existing user successfully', async () => {
    postMock.mockResolvedValueOnce({ status: 200, data: { token: 'abc123' } })

    const wrapper = mount(Auth, {
      global: {
        stubs: {
          VContainer: true,
          VCard: true,
          VCardTitle: true,
          VCardText: true,
          VForm: true,
          VTextField: true,
          VRow: true,
          VCol: true,
          VBtn: true,
          VAlert: true,
        },
      },
    })

    wrapper.vm.username = 'existing'
    wrapper.vm.password = 'pass123'
    await wrapper.vm.onLogin()

    expect(postMock).toHaveBeenCalledWith('/api/v1/auth/login', {
      username: 'existing',
      password: 'pass123',
    })
    expect(localStorage.getItem('token')).toBe('abc123')
  })
})
