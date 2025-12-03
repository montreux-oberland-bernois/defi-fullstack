/**
 * @file Integration tests for the API service module.
 *
 * @description
 * This test suite verifies the behavior of the functions exported from `src/services/api.ts`.
 * It uses Vitest to run the tests and mock dependencies.
 *
 * The tests are structured as integration tests for the service layer. They do not
 * test the underlying business logic of the backend API, but rather ensure that the
 * frontend service functions correctly format requests for `axios` and process the
 * mocked responses.
 *
 * The `axios` library is mocked to prevent actual network requests during testing.
 * Specifically, `axios.create` is mocked to provide a controlled `apiClient` instance
 * with mocked `get` and `post` methods for each test case.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createRoute, getAnalytics } from '../services/api'

const mockAxiosInstance = vi.hoisted(() => ({
  get: vi.fn(),
  post: vi.fn(),
  interceptors: {
    request: { use: vi.fn(), eject: vi.fn() },
    response: { use: vi.fn(), eject: vi.fn() },
  },
}))

vi.mock('axios', () => ({
  default: {
    create: vi.fn(() => mockAxiosInstance),
  },
}))

describe('api service integration', () => {
  beforeEach(() => {
    vi.resetAllMocks()
    // Use the hoisted mock instance directly
    mockAxiosInstance.post.mockResolvedValue({
      data: { id: 1, fromStationId: 'MX' },
    })
    mockAxiosInstance.get.mockResolvedValue({
      data: { items: [{ analyticCode: 'X', totalDistanceKm: 10 }] },
    })
  })

  it('createRoute should call api and return data', async () => {
    const result = await createRoute({
      fromStationId: 'MX',
      toStationId: 'ZW',
      analyticCode: 'ANA',
    })

    expect(mockAxiosInstance.post).toHaveBeenCalledWith('/routes', {
      fromStationId: 'MX',
      toStationId: 'ZW',
      analyticCode: 'ANA',
    })
    expect(result).toHaveProperty('id')
  })

  it('getAnalytics should call api and return items', async () => {
    const resp = await getAnalytics('2025-01-01', '2025-01-31', 'month')

    expect(mockAxiosInstance.get).toHaveBeenCalledWith(
      '/stats/distances',
      expect.objectContaining({
        params: expect.any(URLSearchParams),
      })
    )
    expect(resp.items).toBeDefined()
  })
})
